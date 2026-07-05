<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ShippingRate;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = Session::get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->withErrors(['cart' => 'Keranjang belanja kosong.']);
        }

        $address = UserAddress::where('user_id', Auth::id())
                    ->where('is_default', true)
                    ->first();

        if (!$address) {
            $address = UserAddress::where('user_id', Auth::id())->latest()->first();
        }

        $subtotal = array_reduce($cart, function ($carry, $item) {
            return $carry + ($item['price'] * $item['quantity']);
        }, 0);

        // Cari tarif ongkir berdasarkan zonasi kecamatan
        $shippingRate = 0;
        $storeDistrictId = null;

        if ($address && $address->district_id) {
            $setting = DB::table('settings')->first();
            $storeDistrictId = $setting->store_district_id ?? null;

            if ($storeDistrictId) {
                $rate = ShippingRate::where('origin_district_id', $storeDistrictId)
                    ->where('destination_district_id', $address->district_id)
                    ->first();
                $shippingRate = $rate ? (float) $rate->rate : 0;
            }
        }

        $shippingCost = $shippingRate;
        $total = $subtotal + $shippingCost;

        // Cek blokir COD
        $user = Auth::user();
        $codBlocked = $user->cod_blocked_until && now()->lessThan($user->cod_blocked_until);

        return view('customer.checkout.index', compact(
            'cart', 'address', 'subtotal', 'shippingCost', 'total', 'codBlocked'
        ));
    }

    public function process(Request $request)
    {
        $cart = Session::get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->withErrors(['cart' => 'Keranjang belanja kosong.']);
        }

        $request->validate([
            'address_id' => 'required|exists:user_addresses,id',
            'payment_method' => 'required|in:midtrans,cod',
        ]);

        $userAddress = UserAddress::where('id', $request->address_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $subtotal = array_reduce($cart, function ($carry, $item) {
            return $carry + ($item['price'] * $item['quantity']);
        }, 0);

        // Cari tarif ongkir dari database zonasi
        $setting = DB::table('settings')->first();
        $storeDistrictId = $setting->store_district_id ?? null;
        $shippingCost = 0;

        if ($storeDistrictId && $userAddress->district_id) {
            $rate = ShippingRate::where('origin_district_id', $storeDistrictId)
                ->where('destination_district_id', $userAddress->district_id)
                ->first();
            $shippingCost = $rate ? (float) $rate->rate : 0;
        }

        // Validasi jika COD dan user diblokir
        $user = Auth::user();
        if ($request->payment_method === 'cod') {
            if ($user->cod_blocked_until && now()->lessThan($user->cod_blocked_until)) {
                return redirect()->route('checkout.index')
                    ->withErrors(['cod' => 'Fitur COD diblokir sementara karena Anda telah menolak pesanan COD sebanyak 3 kali. Silakan gunakan pembayaran online.']);
            }
        }

        // Hitung COD fee (2% dari subtotal)
        $codFee = $request->payment_method === 'cod' ? round($subtotal * 0.02, 2) : 0;
        $total = $subtotal + $shippingCost + $codFee;

        $invoice = 'INV-' . date('Ymd') . '-' . rand(1000, 9999);
        $orderStatus = $request->payment_method === 'cod' ? 'menunggu_diproses' : 'menunggu_pembayaran';
        $paymentStatus = 'pending';

        try {
            DB::beginTransaction();

            $productIds = array_column($cart, 'id');
            $products = \App\Models\Product::whereIn('id', $productIds)->lockForUpdate()->get()->keyBy('id');

            foreach ($cart as $item) {
                $product = $products->get($item['id']);

                if (!$product) {
                    throw new \Exception('Produk "' . $item['name'] . '" tidak ditemukan.');
                }

                if ($product->total_stok < $item['quantity']) {
                    throw new \Exception('Stok produk "' . $product->name . '" tidak mencukupi. Sisa stok: ' . $product->total_stok);
                }
            }

            $order = Order::create([
                'invoice' => $invoice,
                'user_id' => Auth::id(),
                'user_address_id' => $request->address_id,
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'cod_fee' => $codFee,
                'total' => $total,
                'payment_method' => $request->payment_method,
                'payment_status' => $paymentStatus,
                'order_status' => $orderStatus,
                'courier' => 'Zonasi Toko',
            ]);

            foreach ($cart as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['id'],
                    'product_name' => $item['name'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['price'] * $item['quantity'],
                ]);
            }

            Session::forget('cart');

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('checkout.index')->withErrors(['checkout' => $e->getMessage()]);
        }

        if ($request->payment_method === 'cod') {
            return redirect()->route('welcome')->with('success', 'Pesanan COD berhasil dibuat! Menunggu diproses.');
        }

        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production');
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

        $params = [
            'transaction_details' => [
                'order_id' => $invoice,
                'gross_amount' => $total,
            ],
            'customer_details' => [
                'first_name' => Auth::user()->name,
                'email' => Auth::user()->email,
                'phone' => $userAddress->phone ?? '',
            ],
        ];

        try {
            $snapToken = \Midtrans\Snap::getSnapToken($params);
            $order->update(['snap_token' => $snapToken]);
        } catch (\Exception $e) {
            return redirect()->route('checkout.index')->withErrors(['midtrans' => 'Gagal terhubung ke server pembayaran: ' . $e->getMessage()]);
        }

        return redirect()->route('payment.show', $order->id)->with('success', 'Pesanan berhasil dibuat! Segera lakukan pembayaran.');
    }
}
