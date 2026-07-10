<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\StockEntry;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // Menyalin instance agar manipulasi startOfDay tidak merusak pembatasan format tanggal sales
        $start = $request->start_date
            ? \Carbon\Carbon::parse($request->start_date)
            : now()->startOfMonth();
        $end = $request->end_date
            ? \Carbon\Carbon::parse($request->end_date)
            : now()->endOfMonth();

        $startDate = $start->copy();
        $endDate = $end->copy();

        $groupBy = $request->get("group_by", "day");
        $categoryId = $request->get("category_id");

        // 1. Overview Stats (Filtered by Date & Category)
        $offlineSales = Sale::with('product.category')
            ->where('status', 'completed')
            ->whereBetween('sale_date', [$startDate->format("Y-m-d"), $endDate->format("Y-m-d")]);

        $onlineOrders = \App\Models\OrderItem::with('product.category', 'order')
            ->whereHas('order', function ($q) use ($startDate, $endDate) {
                $q->whereBetween(DB::raw('DATE(created_at)'), [$startDate->format("Y-m-d"), $endDate->format("Y-m-d")])
                  ->whereNotIn('order_status', ['dibatalkan', 'menunggu_pembayaran']);
            });

        if ($categoryId) {
            $offlineSales->whereHas('product', fn($q) => $q->where('category_id', $categoryId));
            $onlineOrders->whereHas('product', fn($q) => $q->where('category_id', $categoryId));
        }

        $offlineItems = $offlineSales->get()->map(function ($sale) {
            return (object) [
                'date' => \Carbon\Carbon::parse($sale->sale_date),
                'product' => $sale->product,
                'quantity' => $sale->quantity_sold,
                'total' => $sale->total_price,
                'source' => 'Offline'
            ];
        });

        $onlineItems = $onlineOrders->get()->map(function ($item) {
            return (object) [
                'date' => $item->order->created_at,
                'product' => $item->product,
                'quantity' => $item->quantity,
                'total' => $item->subtotal,
                'source' => 'Online'
            ];
        });

        $salesData = $offlineItems->merge($onlineItems)->sortByDesc(fn($item) => $item->date->timestamp)->values();

        $totalSales = $salesData->sum('total');
        $totalItemsSold = $salesData->sum('quantity');

        // 🌟 PERBAIKAN: Hanya hitung riwayat stock_entries yang bertipe 'in' sebagai Stok Masuk
        $stockQuery = StockEntry::where("type", "in")->whereBetween(
            "entry_date",
            [$startDate->format("Y-m-d"), $endDate->format("Y-m-d")],
        );

        if ($categoryId) {
            $stockQuery->whereHas("product", function ($q) use ($categoryId) {
                $q->where("category_id", $categoryId);
            });
        }
        $totalStockIn = $stockQuery->sum("quantity");

        // 2. Sales Data for Chart & Table
        $chartData = $this->getChartData($salesData, $groupBy);

        // 3. Stock Data
        // Hitung agregat stok sekali di database. Jangan panggil accessor total_stok
        // berkali-kali di halaman laporan karena accessor tersebut menjalankan query per produk.
        $productQuery = Product::with("category")
            ->withSum(
                [
                    "stockEntries as stock_in_total" => function ($q) {
                        $q->where("type", "in");
                    },
                ],
                "quantity",
            )
            ->withSum(
                [
                    "sales as offline_sold_total" => function ($q) {
                        $q->where("status", "completed");
                    },
                ],
                "quantity_sold",
            );

        if ($categoryId) {
            $productQuery->where("category_id", $categoryId);
        }

        $products = $productQuery->get();

        $onlineSoldByProductId = collect();
        if ($products->isNotEmpty()) {
            $onlineSoldByProductId = DB::table("order_items")
                ->join("orders", "order_items.order_id", "=", "orders.id")
                ->whereIn("order_items.product_id", $products->pluck("id"))
                ->where("orders.order_status", "!=", "dibatalkan")
                ->select(
                    "order_items.product_id",
                    DB::raw("SUM(order_items.quantity) as total_quantity"),
                )
                ->groupBy("order_items.product_id")
                ->pluck("total_quantity", "order_items.product_id");
        }

        $products->each(function ($product) use ($onlineSoldByProductId) {
            $product->stock_in_total = (int) ($product->stock_in_total ?? 0);
            $product->offline_sold_total =
                (int) ($product->offline_sold_total ?? 0);
            $product->online_sold_total =
                (int) ($onlineSoldByProductId[$product->id] ?? 0);
            $product->sold_total =
                $product->offline_sold_total + $product->online_sold_total;
            $product->total_stok_report =
                $product->stock_in_total - $product->sold_total;
        });

        $lowStockCount = $products
            ->filter(
                fn($p) => $p->total_stok_report < 10 &&
                    $p->total_stok_report > 0,
            )
            ->count();
        $outOfStockCount = $products
            ->filter(fn($p) => $p->total_stok_report <= 0)
            ->count();

        $categories = \App\Models\Category::all();

        return view(
            "reports.index",
            compact(
                "totalSales",
                "totalItemsSold",
                "totalStockIn",
                "salesData",
                "chartData",
                "products",
                "lowStockCount",
                "outOfStockCount",
                "startDate",
                "endDate",
                "groupBy",
                "categories",
                "categoryId",
            ),
        );
    }

    private function getChartData($salesData, $groupBy)
    {
        $labels = [];
        $revenue = [];
        $qty = [];

        $grouped = $salesData->groupBy(function ($item) use ($groupBy) {
            $date = $item->date;
            if ($groupBy == 'day') return $date->format('d M Y');
            if ($groupBy == 'week') return 'Minggu ' . $date->weekOfYear . ', ' . $date->year;
            if ($groupBy == 'month') return $date->format('M Y');
            if ($groupBy == 'year') return $date->format('Y');
            return $date->format('d M Y');
        });

        // Urutkan berdasarkan waktu paling awal (ascending)
        $grouped = $grouped->sortBy(function ($items) {
            return $items->first()->date->timestamp;
        });

        foreach ($grouped as $label => $items) {
            $labels[] = $label;
            $revenue[] = $items->sum('total');
            $qty[] = $items->sum('quantity');
        }

        return [
            'labels' => $labels,
            'revenue' => $revenue,
            'qty' => $qty,
        ];
    }

    public function export()
    {
        // Basic implementation for now, or just redirect back
        return redirect()
            ->back()
            ->with("error", "Fitur ekspor akan segera hadir!");
    }
}
