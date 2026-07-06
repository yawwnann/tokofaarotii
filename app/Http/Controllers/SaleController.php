<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    public function index()
    {
        // Mengelompokkan berdasarkan transaction_group
        // Menggunakan COALESCE untuk menangani data lama yang transaction_group-nya NULL
        $sales = Sale::join("products", "sales.product_id", "=", "products.id")
            ->join("categories", "products.category_id", "=", "categories.id")
            ->select(
                \DB::raw(
                    "COALESCE(sales.transaction_group, CONCAT('LEGACY-', sales.id)) as transaction_group",
                ),
                "sales.customer_name",
                "sales.payment_method",
                "sales.sale_date",
                "sales.source",
                \DB::raw("SUM(sales.total_price) as total_revenue"),
                \DB::raw("SUM(sales.quantity_sold) as total_items"),
                \DB::raw(
                    'GROUP_CONCAT(products.name SEPARATOR ", ") as product_names',
                ),
                \DB::raw(
                    'GROUP_CONCAT(DISTINCT categories.name SEPARATOR ", ") as category_names',
                ),
                \DB::raw("MAX(sales.created_at) as latest_created"),
            )
            ->groupBy(
                \DB::raw(
                    "COALESCE(sales.transaction_group, CONCAT('LEGACY-', sales.id))",
                ),
                "sales.customer_name",
                "sales.payment_method",
                "sales.sale_date",
                "sales.source",
            )
            ->latest("latest_created")
            ->paginate(10);

        $totalSales = Sale::sum("total_price");
        $totalItems = Sale::sum("quantity_sold");

        return view(
            "sales.index",
            compact("sales", "totalSales", "totalItems"),
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        $products = Product::with("category")
            ->when($user->role === "kasir" && $user->store_id, function (
                $q,
            ) use ($user) {
                $q->where("store_id", $user->store_id);
            })
            ->get();
        $store = $user->store ?? DB::table("settings")->first();
        return view("sales.create", compact("products", "store"));
    }

    /**
     * Store a newly created resource in storage.
     */
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "product_id" => "required|exists:products,id",
            "quantity_sold" => "required|integer|min:1",
            "total_price" => "required|numeric|min:0",
            "customer_name" => "nullable|string|max:255",
            "source" => "required|in:online,offline",
            "payment_method" => "required|string",
        ]);

        $product = Product::findOrFail($request->product_id);

        // 1. Cek validasi stok sebelum menyimpan
        if ($product->total_stok < $request->quantity_sold) {
            return back()
                ->withErrors([
                    "quantity_sold" =>
                        "Stok tidak mencukupi. Tersedia: " .
                        $product->total_stok,
                ])
                ->withInput();
        }

        // Generasi nomor invoice / transaction group
        $transactionId = "TRX-S-" . date("Ymd") . "-" . rand(100, 999);

        Sale::create([
            "invoice_number" => $transactionId,
            "store_id" => Auth::user()->store_id,
            "product_id" => $request->product_id,
            "quantity_sold" => $request->quantity_sold,
            "price_at_sale" => $product->price,
            "total_price" => $request->total_price,
            "customer_name" => $request->customer_name ?? "Umum",
            "source" => $request->source,
            "status" => "completed",
            "notes" => $request->notes,
            "payment_method" => $request->payment_method,
            "transaction_group" => $transactionId,
        ]);

        return redirect()
            ->route("sales.index")
            ->with("success", "Penjualan berhasil dicatat");
    }

    /**
     * Store POS transaction items (Dari Halaman Kasir)
     */
    public function storePos(Request $request)
    {
        if (!$request->items || count($request->items) == 0) {
            return response()->json([
                "success" => false,
                "message" => "Keranjang kosong",
            ]);
        }

        try {
            $transactionId = "TRX-" . date("Ymd") . "-" . rand(1000, 9999);
            $paymentMethod = $request->payment_method ?? "tunai";
            $isTransfer = in_array($paymentMethod, ["transfer", "midtrans"]);

            // Pre-fetch semua produk dalam keranjang sekaligus — 1 query.
            // withStockData() + injectOnlineSold() → total 3 query flat, bukan N+1.
            $ids = collect($request->items)->pluck("id")->unique()->all();
            $productMap = Product::withStockData()->whereIn("id", $ids)->get();
            Product::injectOnlineSold($productMap);
            $productMap = $productMap->keyBy("id");

            // 1. VALIDASI STOK TERLEBIH DAHULU UNTUK SEMUA BARANG DI KERANJANG
            foreach ($request->items as $item) {
                $product = $productMap->get($item["id"]);
                if (!$product || $product->total_stok < $item["qty"]) {
                    return response()->json([
                        "success" => false,
                        "message" =>
                            'Stok produk "' .
                            ($product->name ?? "Produk") .
                            '" tidak mencukupi. Sisa stok: ' .
                            ($product->total_stok ?? 0),
                    ]);
                }
            }

            // 2. JIKA SEMUA STOK AMAN, BARU SIMPAN KE DATABASE
            foreach ($request->items as $item) {
                $product = $productMap->get($item["id"]); // dari collection, 0 query tambahan

                Sale::create([
                    "invoice_number" => $transactionId,
                    "store_id" => Auth::user()->store_id,
                    "product_id" => $item["id"],
                    "quantity_sold" => $item["qty"],
                    "price_at_sale" => $product->price,
                    "total_price" => $item["price"] * $item["qty"],
                    "customer_name" => $request->customer_name ?? "Umum",
                    "source" => "offline",
                    "status" => $isTransfer ? "pending" : "completed",
                    "transaction_group" => $transactionId,
                    "payment_method" => $paymentMethod,
                    "sale_date" => now(),
                ]);
            }

            // 3. Jika Transfer, generate Snap token Midtrans
            $snapToken = null;
            if ($isTransfer) {
                $totalBelanja = collect($request->items)->sum(
                    fn($i) => $i["price"] * $i["qty"],
                );

                \Midtrans\Config::$serverKey = config("midtrans.server_key");
                \Midtrans\Config::$isProduction = config(
                    "midtrans.is_production",
                );
                \Midtrans\Config::$isSanitized = true;
                \Midtrans\Config::$is3ds = true;

                $params = [
                    "transaction_details" => [
                        "order_id" => $transactionId,
                        "gross_amount" => (int) $totalBelanja,
                    ],
                    "customer_details" => [
                        "first_name" => $request->customer_name ?? "Pembeli",
                    ],
                ];

                try {
                    $snapToken = \Midtrans\Snap::getSnapToken($params);
                } catch (\Throwable $e) {
                    // Jika Midtrans gagal, tetap simpan transaksi dengan status pending
                    $snapToken = null;
                }
            }

            $response = [
                "success" => true,
                "transaction_id" => $transactionId,
                "customer" => $request->customer_name ?? "Umum",
                "payment_method" => $paymentMethod,
                "time" => date("d M Y H:i"),
                "is_transfer" => $isTransfer,
            ];

            if ($snapToken) {
                $response["snap_token"] = $snapToken;
            }

            return response()->json($response);
        } catch (\Throwable $e) {
            return response()->json([
                "success" => false,
                "message" => $e->getMessage(),
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Sale $sale)
    {
        $sale->load("product.category");
        return view("sales.show", compact("sale"));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sale $sale)
    {
        $sale->load("product");
        $user = Auth::user();
        $products = Product::with("category")
            ->orderBy("name")
            ->when(
                $user->role === "kasir" && $user->store_id,
                fn($q) => $q->where("store_id", $user->store_id),
            )
            ->get();
        return view("sales.edit", compact("sale", "products"));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sale $sale)
    {
        $request->validate([
            "product_id" => "required|exists:products,id",
            "quantity_sold" => "required|integer|min:1",
            "total_price" => "required|numeric|min:0",
            "customer_name" => "nullable|string|max:255",
            "notes" => "nullable|string",
        ]);

        $sale->update(
            $request->only([
                "product_id",
                "quantity_sold",
                "total_price",
                "customer_name",
                "notes",
            ]),
        );

        return redirect()
            ->route("sales.index")
            ->with("success", "Penjualan berhasil diperbarui");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // $id di sini adalah transaction_group atau virtual legacy ID
        if (str_starts_with($id, "LEGACY-")) {
            $realId = str_replace("LEGACY-", "", $id);
            Sale::where("id", $realId)->delete();
        } else {
            Sale::where("transaction_group", $id)->delete();
        }

        return redirect()
            ->route("sales.index")
            ->with("success", "Transaksi berhasil dihapus");
    }

    /**
     * Confirm a pending sale.
     */
    public function confirm(Sale $sale)
    {
        $sale->update(["status" => "completed"]);

        return redirect()
            ->route("sales.index")
            ->with("success", "Penjualan berhasil dikonfirmasi");
    }
}
