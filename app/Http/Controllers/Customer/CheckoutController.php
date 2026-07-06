<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\District;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ShippingZone;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = Session::get("cart", []);

        if (empty($cart)) {
            return redirect()
                ->route("cart.index")
                ->withErrors(["cart" => "Keranjang belanja kosong."]);
        }

        $address = UserAddress::where("user_id", Auth::id())
            ->where("is_default", true)
            ->first();

        if (!$address) {
            $address = UserAddress::where("user_id", Auth::id())
                ->latest()
                ->first();
        }

        $subtotal = array_reduce(
            $cart,
            function ($carry, $item) {
                return $carry + $item["price"] * $item["quantity"];
            },
            0,
        );

        $shippingResult =
            $address && $address->district_id
                ? $this->calculateShippingCost($cart, $address->district_id)
                : ["cost" => 0, "breakdown" => [], "has_rates" => false];

        $shippingCost = $shippingResult["cost"];
        $shippingBreakdown = $shippingResult["breakdown"];
        $hasRates = $shippingResult["has_rates"];
        $total = $subtotal + $shippingCost;

        $user = Auth::user();
        $codBlocked =
            $user->cod_blocked_until &&
            now()->lessThan($user->cod_blocked_until);

        return view(
            "customer.checkout.index",
            compact(
                "cart",
                "address",
                "subtotal",
                "shippingCost",
                "shippingBreakdown",
                "hasRates",
                "total",
                "codBlocked",
            ),
        );
    }

    public function process(Request $request)
    {
        $cart = Session::get("cart", []);

        if (empty($cart)) {
            return redirect()
                ->route("cart.index")
                ->withErrors(["cart" => "Keranjang belanja kosong."]);
        }

        $request->validate([
            "address_id" => "required|exists:user_addresses,id",
            "payment_method" => "required|in:midtrans,cod",
        ]);

        $userAddress = UserAddress::where("id", $request->address_id)
            ->where("user_id", Auth::id())
            ->firstOrFail();

        $subtotal = array_reduce(
            $cart,
            function ($carry, $item) {
                return $carry + $item["price"] * $item["quantity"];
            },
            0,
        );

        $shippingResult = $userAddress->district_id
            ? $this->calculateShippingCost($cart, $userAddress->district_id)
            : ["cost" => 0, "breakdown" => [], "has_rates" => false];

        $shippingCost = $shippingResult["cost"];

        if (!$shippingResult["has_rates"]) {
            return redirect()
                ->route("checkout.index")
                ->withErrors([
                    "shipping" =>
                        "Belum ada tarif ongkir yang dikonfigurasi. Silakan hubungi admin toko.",
                ]);
        }

        $user = Auth::user();
        if ($request->payment_method === "cod") {
            if (
                $user->cod_blocked_until &&
                now()->lessThan($user->cod_blocked_until)
            ) {
                return redirect()
                    ->route("checkout.index")
                    ->withErrors([
                        "cod" =>
                            "Fitur COD diblokir sementara karena Anda telah menolak pesanan COD sebanyak 3 kali. Silakan gunakan pembayaran online.",
                    ]);
            }
        }

        $codFee =
            $request->payment_method === "cod" ? round($subtotal * 0.02, 2) : 0;
        $total = $subtotal + $shippingCost + $codFee;

        $invoice = "INV-" . date("Ymd") . "-" . rand(1000, 9999);
        $orderStatus =
            $request->payment_method === "cod"
                ? "menunggu_diproses"
                : "menunggu_pembayaran";
        $paymentStatus = "pending";

        $snapToken = null;
        if ($request->payment_method === "midtrans") {
            \Midtrans\Config::$serverKey = config("midtrans.server_key");
            \Midtrans\Config::$isProduction = config("midtrans.is_production");
            \Midtrans\Config::$isSanitized = true;
            \Midtrans\Config::$is3ds = true;

            $params = [
                "transaction_details" => [
                    "order_id" => $invoice,
                    "gross_amount" => $total,
                ],
                "customer_details" => [
                    "first_name" => Auth::user()->name,
                    "email" => Auth::user()->email,
                    "phone" => $userAddress->phone ?? "",
                ],
            ];

            try {
                $snapToken = \Midtrans\Snap::getSnapToken($params);
            } catch (\Throwable $e) {
                return redirect()
                    ->route("checkout.index")
                    ->withErrors([
                        "midtrans" =>
                            "Gagal terhubung ke server pembayaran: " .
                            $e->getMessage(),
                    ]);
            }

            if (!$snapToken) {
                return redirect()
                    ->route("checkout.index")
                    ->withErrors([
                        "midtrans" =>
                            "Gagal mendapatkan token pembayaran. Silakan coba lagi.",
                    ]);
            }
        }

        try {
            DB::beginTransaction();

            $productIds = array_column($cart, "id");

            // withStockData + lockForUpdate: agregat stok dihitung via withSum,
            // lock baris products tetap aktif untuk mencegah race condition.
            $products = Product::withStockData()
                ->whereIn("id", $productIds)
                ->lockForUpdate()
                ->get();
            Product::injectOnlineSold($products);
            $products = $products->keyBy("id");

            foreach ($cart as $item) {
                $product = $products->get($item["id"]);

                if (!$product) {
                    throw new \Exception(
                        'Produk "' . $item["name"] . '" tidak ditemukan.',
                    );
                }

                if ($product->total_stok < $item["quantity"]) {
                    throw new \Exception(
                        'Stok produk "' .
                            $product->name .
                            '" tidak mencukupi. Sisa stok: ' .
                            $product->total_stok,
                    );
                }
            }

            $order = Order::create([
                "invoice" => $invoice,
                "user_id" => Auth::id(),
                "user_address_id" => $request->address_id,
                "subtotal" => $subtotal,
                "shipping_cost" => $shippingCost,
                "cod_fee" => $codFee,
                "total" => $total,
                "payment_method" => $request->payment_method,
                "payment_status" => $paymentStatus,
                "order_status" => $orderStatus,
                "courier" => "Zonasi Toko",
                "snap_token" => $snapToken,
            ]);

            foreach ($cart as $item) {
                OrderItem::create([
                    "order_id" => $order->id,
                    "product_id" => $item["id"],
                    "product_name" => $item["name"],
                    "quantity" => $item["quantity"],
                    "price" => $item["price"],
                    "subtotal" => $item["price"] * $item["quantity"],
                ]);
            }

            Session::forget("cart");

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->route("checkout.index")
                ->withErrors(["checkout" => $e->getMessage()]);
        }

        if ($request->payment_method === "cod") {
            return redirect()
                ->route("welcome")
                ->with(
                    "success",
                    "Pesanan COD berhasil dibuat! Menunggu diproses.",
                );
        }

        return redirect()
            ->route("payment.show", $order->id)
            ->with(
                "success",
                "Pesanan berhasil dibuat! Segera lakukan pembayaran.",
            );
    }

    private function calculateShippingCost(
        array $cart,
        string $destinationDistrictId,
    ): array {
        $defaultDistrictId = DB::table("settings")->value("store_district_id");
        $totalCost = 0;
        $breakdown = [];
        $hasRates = false;

        $productIds = array_column($cart, "id");

        // store.district.regency.province sudah eager-loaded —
        // tidak perlu query District ulang di dalam loop.
        $products = Product::whereIn("id", $productIds)
            ->with("store.district.regency.province")
            ->get();

        $destinationDistrict = District::with("regency.province")->find(
            $destinationDistrictId,
        );
        if (!$destinationDistrict) {
            return ["cost" => 0, "breakdown" => [], "has_rates" => false];
        }

        // Pre-fetch default district sekali di luar loop (jika ada).
        $defaultDistrict = $defaultDistrictId
            ? District::with("regency.province")->find($defaultDistrictId)
            : null;

        // Pre-fetch semua ShippingZone untuk semua toko sekaligus — 1 query.
        // Sebelumnya: 1 query per toko di dalam loop.
        $storeIds = $products
            ->groupBy("store_id")
            ->keys()
            ->filter()
            ->values()
            ->all();
        $allZones = ShippingZone::whereIn("store_id", $storeIds)
            ->get()
            ->groupBy("store_id")
            ->map(fn($zones) => $zones->keyBy("zone_level"));

        foreach ($products->groupBy("store_id") as $storeId => $items) {
            $store = $items->first()->store;
            if (!$store) {
                continue;
            }

            // Gunakan district yang sudah eager-loaded; fallback ke default district.
            $originDistrict = $store->district ?? $defaultDistrict;
            if (!$originDistrict) {
                continue;
            }

            $zoneLevel = $this->determineZoneLevel(
                $originDistrict,
                $destinationDistrict,
            );

            // Lookup dari collection in-memory — 0 query tambahan.
            $zone = ($allZones[$storeId] ?? collect())->get($zoneLevel);

            $cost = $zone ? (float) $zone->rate : 0;
            if ($zone) {
                $hasRates = true;
            }
            $totalCost += $cost;

            $breakdown[] = [
                "store_name" => $store->name ?? "Toko Utama",
                "store_district" =>
                    $store->district?->name ??
                    ($defaultDistrictId ? "Kecamatan Default" : "-"),
                "cost" => $cost,
                "product_count" => $items->count(),
                "zone_level" => $zoneLevel,
                "rate_found" => (bool) $zone,
            ];
        }

        return [
            "cost" => $totalCost,
            "breakdown" => $breakdown,
            "has_rates" => $hasRates,
        ];
    }

    private function determineZoneLevel(
        District $origin,
        District $destination,
    ): string {
        if ($origin->id === $destination->id) {
            return "same_district";
        }

        if ($origin->regency_id === $destination->regency_id) {
            return "same_regency";
        }

        $originProvinceId = $origin->regency->province_id;
        $destProvinceId = $destination->regency->province_id;

        if ($originProvinceId === $destProvinceId) {
            return "same_province";
        }

        if (
            $origin->regency->province->island ===
            $destination->regency->province->island
        ) {
            return "same_island";
        }

        return "different_island";
    }
}
