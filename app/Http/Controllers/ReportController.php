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
        $salesQuery = Sale::whereBetween("sale_date", [
            $startDate->format("Y-m-d"),
            $endDate->format("Y-m-d"),
        ]);
        if ($categoryId) {
            $salesQuery->whereHas("product", function ($q) use ($categoryId) {
                $q->where("category_id", $categoryId);
            });
        }

        $totalSales = $salesQuery->sum("total_price");
        $totalItemsSold = $salesQuery->sum("quantity_sold");

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
        $salesData = $salesQuery
            ->with(["product.category"])
            ->latest("sale_date")
            ->get();
        $chartData = $this->getChartData(
            $startDate,
            $endDate,
            $groupBy,
            $categoryId,
        );

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

    private function getChartData($startDate, $endDate, $groupBy, $categoryId)
    {
        $query = Sale::select(
            DB::raw("SUM(total_price) as total_revenue"),
            DB::raw("SUM(quantity_sold) as total_qty"),
        )->whereBetween("sale_date", [
            $startDate->format("Y-m-d"),
            $endDate->format("Y-m-d"),
        ]);

        if ($categoryId) {
            $query->whereHas("product", function ($q) use ($categoryId) {
                $q->where("category_id", $categoryId);
            });
        }

        if ($groupBy == "day") {
            $query
                ->addSelect(
                    DB::raw("DATE_FORMAT(sale_date, '%d %b %Y') as label"),
                )
                ->groupBy(DB::raw("DATE(sale_date)"), "label")
                ->orderBy(DB::raw("DATE(sale_date)"), "ASC");
        } elseif ($groupBy == "week") {
            $query
                ->addSelect(
                    DB::raw(
                        "CONCAT('Minggu ', WEEK(sale_date), ', ', YEAR(sale_date)) as label",
                    ),
                )
                ->groupBy(DB::raw("YEARWEEK(sale_date)"), "label")
                ->orderBy(DB::raw("YEARWEEK(sale_date)"), "ASC");
        } elseif ($groupBy == "month") {
            $query
                ->addSelect(DB::raw("DATE_FORMAT(sale_date, '%b %Y') as label"))
                ->groupBy(DB::raw("DATE_FORMAT(sale_date, '%Y-%m')"), "label")
                ->orderBy(DB::raw("DATE_FORMAT(sale_date, '%Y-%m')"), "ASC");
        } elseif ($groupBy == "year") {
            $query
                ->addSelect(DB::raw("YEAR(sale_date) as label"))
                ->groupBy("label")
                ->orderBy("label", "ASC");
        }

        $results = $query->get();

        return [
            "labels" => $results->pluck("label"),
            "revenue" => $results->pluck("total_revenue"),
            "qty" => $results->pluck("total_qty"),
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
