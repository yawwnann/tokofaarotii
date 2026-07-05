<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use Illuminate\Http\Request;
// use Midtrans\Config;
// use Midtrans\Snap;

class SaleController extends Controller
{
    public function index()
    {
        // Mengelompokkan berdasarkan transaction_group
        // Menggunakan COALESCE untuk menangani data lama yang transaction_group-nya NULL
        $sales = Sale::join('products', 'sales.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select(
                \DB::raw("COALESCE(sales.transaction_group, CONCAT('LEGACY-', sales.id)) as transaction_group"), 
                'sales.customer_name', 
                'sales.payment_method', 
                'sales.sale_date', 
                'sales.source',
                \DB::raw('SUM(sales.total_price) as total_revenue'), 
                \DB::raw('SUM(sales.quantity_sold) as total_items'), 
                \DB::raw('GROUP_CONCAT(products.name SEPARATOR ", ") as product_names'),
                \DB::raw('GROUP_CONCAT(DISTINCT categories.name SEPARATOR ", ") as category_names'),
                \DB::raw('MAX(sales.created_at) as latest_created')
            )
            ->groupBy(\DB::raw("COALESCE(sales.transaction_group, CONCAT('LEGACY-', sales.id))"), 'sales.customer_name', 'sales.payment_method', 'sales.sale_date', 'sales.source')
            ->latest('latest_created')
            ->paginate(10);
            
        $totalSales = Sale::sum('total_price');
        $totalItems = Sale::sum('quantity_sold');
        
        return view('sales.index', compact('sales', 'totalSales', 'totalItems'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = Product::with('category')->get();
        return view('sales.create', compact('products'));
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
            'product_id' => 'required|exists:products,id',
            'quantity_sold' => 'required|integer|min:1',
            'total_price' => 'required|numeric|min:0',
            'customer_name' => 'nullable|string|max:255',
            'source' => 'required|in:online,offline',
            'payment_method' => 'required|string',
        ]);

        $product = Product::findOrFail($request->product_id);
        
        // 1. Cek validasi stok sebelum menyimpan
        if ($product->total_stok < $request->quantity_sold) {
            return back()->withErrors(['quantity_sold' => 'Stok tidak mencukupi. Tersedia: ' . $product->total_stok])->withInput();
        }

        // Generasi nomor invoice / transaction group
        $transactionId = 'TRX-S-' . date('Ymd') . '-' . rand(100, 999);

        Sale::create([
            'invoice_number' => $transactionId, // Sinkronisasi kolom invoice
            'product_id' => $request->product_id,
            'quantity_sold' => $request->quantity_sold,
            'price_at_sale' => $product->price, // ✅ MENGUNCI HARGA SAAT INI
            'total_price' => $request->total_price,
            'customer_name' => $request->customer_name ?? 'Umum',
            'source' => $request->source,
            'status' => 'completed',
            'notes' => $request->notes,
            'payment_method' => $request->payment_method,
            'transaction_group' => $transactionId,
        ]);

        return redirect()->route('sales.index')->with('success', 'Penjualan berhasil dicatat');
    }

    /**
     * Store POS transaction items (Dari Halaman Kasir)
     */
    public function storePos(Request $request)
    {
        if (!$request->items || count($request->items) == 0) {
            return response()->json(['success' => false, 'message' => 'Keranjang kosong']);
        }

        try {
            $transactionId = 'TRX-' . date('Ymd') . '-' . rand(1000, 9999);

            // 1. VALIDASI STOK TERLEBIH DAHULU UNTUK SEMUA BARANG DI KERANJANG
            foreach ($request->items as $item) {
                $product = Product::find($item['id']);
                if (!$product || $product->total_stok < $item['qty']) {
                    return response()->json([
                        'success' => false, 
                        'message' => 'Stok produk "' . ($product->name ?? 'Produk') . '" tidak mencukupi. Sisa stok: ' . ($product->total_stok ?? 0)
                    ]);
                }
            }

            // 2. JIKA SEMUA STOK AMAN, BARU SIMPAN KE DATABASE
            foreach ($request->items as $item) {
                $product = Product::find($item['id']);
                
                Sale::create([
                    'invoice_number' => $transactionId, // Sinkronisasi kolom invoice
                    'product_id'    => $item['id'],
                    'quantity_sold' => $item['qty'],
                    'price_at_sale' => $product->price, // ✅ MENGUNCI HARGA SAAT INI
                    'total_price'   => $item['price'] * $item['qty'],
                    'customer_name' => $request->customer_name ?? 'Umum',
                    'source'        => 'offline',
                    'status'        => 'completed',
                    'transaction_group' => $transactionId,
                    'payment_method' => $request->payment_method ?? 'tunai',
                    'sale_date'      => now(),
                ]);
            }

            return response()->json([
                'success' => true, 
                'transaction_id' => $transactionId,
                'customer' => $request->customer_name ?? 'Umum',
                'payment_method' => $request->payment_method ?? 'tunai',
                'time' => date('d M Y H:i')
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Sale $sale)
    {
        return view('sales.show', compact('sale'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sale $sale)
    {
        $products = Product::all();
        return view('sales.edit', compact('sale', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sale $sale)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity_sold' => 'required|integer|min:1',
            'total_price' => 'required|numeric|min:0',
            'customer_name' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $sale->update($request->only([
            'product_id',
            'quantity_sold',
            'total_price',
            'customer_name',
            'notes',
        ]));

        return redirect()->route('sales.index')
            ->with('success', 'Penjualan berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // $id di sini adalah transaction_group atau virtual legacy ID
        if (str_starts_with($id, 'LEGACY-')) {
            $realId = str_replace('LEGACY-', '', $id);
            Sale::where('id', $realId)->delete();
        } else {
            Sale::where('transaction_group', $id)->delete();
        }

        return redirect()->route('sales.index')
            ->with('success', 'Transaksi berhasil dihapus');
    }

    /**
     * Confirm a pending sale.
     */
    public function confirm(Sale $sale)
    {
        $sale->update(['status' => 'completed']);

        return redirect()->route('sales.index')
            ->with('success', 'Penjualan berhasil dikonfirmasi');
    }
}

    // // logika midtrans
    // public function __construct(){
    //     Config::$serverKey = env('MIDTRANS_SERVER_KEY');
    //     Config::$isProduction = env('MIDTRANS_IS_PRODUCTION');
    //     Config::$isSanitized = true;
    //     Config::$is3ds = true; 
    // }

    // public function buatTransaksiMidtrans(Request $request) {
    //     // hitung total belanja dari kasir faa
    //     $totalBelanja = $request->total_price;
    //     $transactionId = 'TRX-' . time();

    //     // dikirim ke midtrans
    //     $params = [
    //         'transaction_details' => [
    //             'order_id' => $transactionId,
    //             'gross_amount' => (int) $totalBelanja,
    //         ],
    //         'customer_details' => [
    //             'first_name' => $request->customer_name ?? 'Umum',
    //         ],
    //     ];

    //     try {
    //         $snapToken = Snap::getSnapToken($params);

    //         return response()->json([
    //             'success' => true,
    //             'snap_token' => $snapToken,
    //             'transaction_id' => $transactionId
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json(['success' => false, 'message' => $e->getMessage()]);
    //     }
    // }
