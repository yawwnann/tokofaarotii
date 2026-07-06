<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockEntry;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Initialize all variables to avoid undefined variable errors in catch block
        $totalProducts = 0;
        $totalCategories = 0;
        $totalStock = 0;
        $totalSales = 0;
        $totalItemSold = 0;
        $averageMonthlySales = 0;
        $topProducts = collect();
        $monthlySales = collect();
        $lowStock = collect();
        $outOfStock = collect();
        $topCategories = collect();
        $totalTransactions = 0;
        $todaySales = 0;
        $currentMonthSales = 0;
        $growthPercentage = 0;

        $chartLabels = [];
        $chartData = [];
        $chartItems = [];
        $categoryLabels = [];
        $categoryData = [];

        try {
            // ==================== STATISTIK DASAR ====================
            $totalProducts = Product::count();
            $totalCategories = Category::count();
            $totalStock = StockEntry::sum("quantity") ?? 0;
            $totalSales = Sale::sum("total_price") ?? 0;
            $totalItemSold = Sale::sum("quantity_sold") ?? 0;

            // ==================== MONTHLY SALES (6 BULAN TERAKHIR) ====================
            // Cek kolom yang ada di tabel sales
            $dateColumn = $this->getDateColumn();

            // Data penjualan bulanan untuk chart
            $monthlySales = $this->getMonthlySalesData($dateColumn);

            // Hitung rata-rata penjualan per bulan
            $averageMonthlySales = $this->calculateAverageMonthlySales(
                $dateColumn,
            );

            // ==================== PRODUK TERLARIS (TOP 3 FROZEN & TOP 3 BAKERY) ====================
            $topFrozen = Sale::select(
                "product_id",
                DB::raw("SUM(quantity_sold) as total_sold"),
            )
                ->join("products", "sales.product_id", "=", "products.id")
                ->join(
                    "categories",
                    "products.category_id",
                    "=",
                    "categories.id",
                )
                ->where("categories.name", "LIKE", "%frozen%")
                ->groupBy("product_id")
                ->orderBy("total_sold", "DESC")
                ->limit(3)
                ->with([
                    "product" => function ($query) {
                        $query->with("category");
                    },
                ])
                ->get();

            $topBakery = Sale::select(
                "product_id",
                DB::raw("SUM(quantity_sold) as total_sold"),
            )
                ->join("products", "sales.product_id", "=", "products.id")
                ->join(
                    "categories",
                    "products.category_id",
                    "=",
                    "categories.id",
                )
                ->where("categories.name", "LIKE", "%bakery%")
                ->groupBy("product_id")
                ->orderBy("total_sold", "DESC")
                ->limit(3)
                ->with([
                    "product" => function ($query) {
                        $query->with("category");
                    },
                ])
                ->get();

            $topProducts = $topFrozen->concat($topBakery);

            // ==================== ANALISIS STOK ====================
            $stockAnalysis = $this->analyzeStock();
            $lowStock = $stockAnalysis["lowStock"];
            $outOfStock = $stockAnalysis["outOfStock"];

            // ==================== TOP KATEGORI Stock ====================
            // Simpan hasil ke variabel agar bisa diteruskan ke prepareChartData()
            // dan tidak dieksekusi dua kali.
            $topCategories = $this->getTopCategories();

            // ==================== STATISTIK TAMBAHAN ====================
            // Total transaksi
            $totalTransactions = Sale::count();

            // Penjualan hari ini
            $todaySales =
                Sale::whereDate($dateColumn, Carbon::today())->sum(
                    "total_price",
                ) ?? 0;

            // Penjualan bulan ini
            $currentMonthSales =
                Sale::whereMonth($dateColumn, Carbon::now()->month)
                    ->whereYear($dateColumn, Carbon::now()->year)
                    ->sum("total_price") ?? 0;

            // Persentase pertumbuhan bulan ini vs bulan lalu
            $growthPercentage = $this->calculateGrowthPercentage($dateColumn);

            // ==================== PREPARE DATA UNTUK CHART ====================
            $preparedChartData = $this->prepareChartData(
                $monthlySales,
                $topCategories,
            );
            $chartLabels = $preparedChartData["chartLabels"];
            $chartData = $preparedChartData["chartData"];
            $chartItems = $preparedChartData["chartItems"];
            $categoryLabels = $preparedChartData["categoryLabels"];
            $categoryData = $preparedChartData["categoryData"];

            return view(
                "dashboard.index",
                compact(
                    "totalProducts",
                    "totalCategories",
                    "totalStock",
                    "totalSales",
                    "totalItemSold",
                    "averageMonthlySales",
                    "topProducts",
                    "monthlySales",
                    "lowStock",
                    "outOfStock",
                    "topCategories",
                    "totalTransactions",
                    "todaySales",
                    "currentMonthSales",
                    "growthPercentage",
                    "chartLabels",
                    "chartData",
                    "chartItems",
                    "categoryLabels",
                    "categoryData",
                ),
            );
        } catch (\Exception $e) {
            \Log::error("Dashboard Error: " . $e->getMessage());

            // Return initialized default data if error
            return view("dashboard.index", [
                "totalProducts" => $totalProducts,
                "totalCategories" => $totalCategories,
                "totalStock" => $totalStock,
                "totalSales" => $totalSales,
                "totalItemSold" => $totalItemSold,
                "averageMonthlySales" => $averageMonthlySales,
                "topProducts" => $topProducts,
                "monthlySales" => $monthlySales,
                "lowStock" => $lowStock,
                "outOfStock" => $outOfStock,
                "topCategories" => $topCategories,
                "totalTransactions" => $totalTransactions,
                "todaySales" => $todaySales,
                "currentMonthSales" => $currentMonthSales,
                "growthPercentage" => $growthPercentage,
                "chartLabels" => $chartLabels,
                "chartData" => $chartData,
                "chartItems" => $chartItems,
                "categoryLabels" => $categoryLabels,
                "categoryData" => $categoryData,
            ]);
        }
    }

    /**
     * Mendeteksi kolom tanggal di tabel sales
     */
    private function getDateColumn()
    {
        try {
            $columns = DB::select("DESCRIBE sales");
            $columnNames = array_column($columns, "Field");

            // Prioritas kolom tanggal
            $dateColumns = [
                "sale_date",
                "date",
                "transaction_date",
                "created_at",
                "updated_at",
            ];

            foreach ($dateColumns as $col) {
                if (in_array($col, $columnNames)) {
                    return $col;
                }
            }
        } catch (\Exception $e) {
            \Log::warning("Cannot detect date column: " . $e->getMessage());
        }

        return "created_at"; // fallback
    }

    /**
     * Mengambil data penjualan bulanan
     */
    private function getMonthlySalesData($dateColumn)
    {
        return Sale::select(
            DB::raw("DATE_FORMAT({$dateColumn}, '%Y-%m') as month_year"),
            DB::raw("DATE_FORMAT({$dateColumn}, '%b %Y') as month_display"),
            DB::raw("MONTH({$dateColumn}) as month"),
            DB::raw("YEAR({$dateColumn}) as year"),
            DB::raw("SUM(total_price) as total_sales"),
            DB::raw("SUM(quantity_sold) as total_items"),
            DB::raw("COUNT(*) as transaction_count"),
        )
            ->where(
                $dateColumn,
                ">=",
                Carbon::now()->subMonths(5)->startOfMonth(),
            )
            ->groupBy("year", "month", "month_year", "month_display")
            ->orderBy("year", "ASC")
            ->orderBy("month", "ASC")
            ->get();
    }

    /**
     * Menghitung rata-rata penjualan bulanan
     */
    private function calculateAverageMonthlySales($dateColumn)
    {
        try {
            $monthlyTotals = Sale::select(
                DB::raw("MONTH({$dateColumn}) as month"),
                DB::raw("YEAR({$dateColumn}) as year"),
                DB::raw("SUM(total_price) as total"),
            )
                ->where($dateColumn, ">=", Carbon::now()->subMonths(12))
                ->groupBy("year", "month")
                ->pluck("total");

            return $monthlyTotals->avg() ?? 0;
        } catch (\Exception $e) {
            \Log::warning(
                "Cannot calculate average monthly sales: " . $e->getMessage(),
            );
            return 0;
        }
    }

    /**
     * Menganalisis stok produk
     */
    private function analyzeStock()
    {
        // Gunakan withStockData scope + injectOnlineSold agar tidak ada N+1:
        // withStockData  → 2 query agregat (stok masuk & terjual offline)
        // injectOnlineSold → 1 query agregat (terjual online)
        // Total: 3 query flat, bukan 3N seperti sebelumnya.
        $products = Product::withStockData()->get();
        Product::injectOnlineSold($products);

        $lowStock = collect();
        $outOfStock = collect();

        foreach ($products as $product) {
            $currentStock = $product->total_stok; // 0 query tambahan

            if ($currentStock < 10 && $currentStock > 0) {
                $lowStock->push($product);
            } elseif ($currentStock <= 0) {
                $outOfStock->push($product);
            }
        }

        return [
            "lowStock" => $lowStock,
            "outOfStock" => $outOfStock,
        ];
    }

    /**
     * Mendapatkan kategori teratas
     */
    private function getTopCategories()
    {
        return Sale::select(
            "categories.id",
            "categories.name",
            DB::raw("SUM(sales.total_price) as total_sales"),
            DB::raw("SUM(sales.quantity_sold) as total_quantity"),
            DB::raw("COUNT(DISTINCT sales.id) as transaction_count"),
        )
            ->join("products", "sales.product_id", "=", "products.id")
            ->join("categories", "products.category_id", "=", "categories.id")
            ->groupBy("categories.id", "categories.name")
            ->orderBy("total_sales", "DESC")
            ->limit(5)
            ->get();
    }

    /**
     * Menghitung persentase pertumbuhan
     */
    private function calculateGrowthPercentage($dateColumn)
    {
        try {
            $currentMonth = Carbon::now()->month;
            $currentYear = Carbon::now()->year;
            $lastMonth = Carbon::now()->subMonth()->month;
            $lastMonthYear = Carbon::now()->subMonth()->year;

            $currentMonthSales =
                Sale::whereMonth($dateColumn, $currentMonth)
                    ->whereYear($dateColumn, $currentYear)
                    ->sum("total_price") ?? 0;

            $lastMonthSales =
                Sale::whereMonth($dateColumn, $lastMonth)
                    ->whereYear($dateColumn, $lastMonthYear)
                    ->sum("total_price") ?? 0;

            if ($lastMonthSales > 0) {
                return round(
                    (($currentMonthSales - $lastMonthSales) / $lastMonthSales) *
                        100,
                    2,
                );
            }

            return $currentMonthSales > 0 ? 100 : 0;
        } catch (\Exception $e) {
            \Log::warning(
                "Cannot calculate growth percentage: " . $e->getMessage(),
            );
            return 0;
        }
    }

    /**
     * Menyiapkan data untuk chart
     */
    private function prepareChartData($monthlySales, $topCategories = null)
    {
        // Data untuk monthly sales chart
        $chartLabels = $monthlySales->pluck("month_display")->toArray();
        $chartData = $monthlySales->pluck("total_sales")->toArray();
        $chartItems = $monthlySales->pluck("total_items")->toArray();

        // Gunakan $topCategories yang sudah diambil di index() — tidak query ulang.
        $categories = $topCategories ?? $this->getTopCategories();
        $categoryLabels = $categories->pluck("name")->toArray();
        $categoryData = $categories->pluck("total_sales")->toArray();

        return [
            "chartLabels" => $chartLabels,
            "chartData" => $chartData,
            "chartItems" => $chartItems,
            "categoryLabels" => $categoryLabels,
            "categoryData" => $categoryData,
        ];
    }

    /**
     * API untuk data analytics
     */
    public function analytics(Request $request)
    {
        try {
            $dateColumn = $this->getDateColumn();

            // Data penjualan harian 30 hari terakhir
            $dailySales = Sale::select(
                DB::raw("DATE({$dateColumn}) as date"),
                DB::raw("DATE_FORMAT({$dateColumn}, '%W') as day_name"),
                DB::raw("SUM(total_price) as total_sales"),
                DB::raw("SUM(quantity_sold) as total_items"),
                DB::raw("COUNT(*) as transactions"),
            )
                ->where($dateColumn, ">=", Carbon::now()->subDays(30))
                ->groupBy("date", "day_name")
                ->orderBy("date", "ASC")
                ->get();

            // Data penjualan per jam (untuk hari ini)
            $hourlySales = Sale::select(
                DB::raw("HOUR({$dateColumn}) as hour"),
                DB::raw("SUM(total_price) as total_sales"),
                DB::raw("SUM(quantity_sold) as total_items"),
            )
                ->whereDate($dateColumn, Carbon::today())
                ->groupBy("hour")
                ->orderBy("hour", "ASC")
                ->get();

            // Top produk bulan ini
            $topProductsMonthly = Sale::select(
                "products.id",
                "products.name",
                DB::raw("SUM(sales.quantity_sold) as total_sold"),
                DB::raw("SUM(sales.total_price) as total_revenue"),
            )
                ->join("products", "sales.product_id", "=", "products.id")
                ->whereMonth($dateColumn, Carbon::now()->month)
                ->whereYear($dateColumn, Carbon::now()->year)
                ->groupBy("products.id", "products.name")
                ->orderBy("total_sold", "DESC")
                ->limit(10)
                ->get();

            return response()->json([
                "success" => true,
                "dailySales" => $dailySales,
                "hourlySales" => $hourlySales,
                "topProductsMonthly" => $topProductsMonthly,
                "updated_at" => now()->toDateTimeString(),
            ]);
        } catch (\Exception $e) {
            \Log::error("Analytics API Error: " . $e->getMessage());

            return response()->json(
                [
                    "success" => false,
                    "message" => "Failed to fetch analytics data",
                    "error" => $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Export data monthly sales
     */
    public function exportMonthlySales(Request $request)
    {
        try {
            $dateColumn = $this->getDateColumn();
            $months = $request->get("months", 6);

            $monthlySales = Sale::select(
                DB::raw("DATE_FORMAT({$dateColumn}, '%Y-%m') as month"),
                DB::raw("DATE_FORMAT({$dateColumn}, '%M %Y') as month_name"),
                DB::raw("SUM(total_price) as total_sales"),
                DB::raw("SUM(quantity_sold) as total_items"),
                DB::raw("COUNT(*) as transactions"),
                DB::raw("AVG(total_price) as average_transaction"),
            )
                ->where(
                    $dateColumn,
                    ">=",
                    Carbon::now()
                        ->subMonths($months - 1)
                        ->startOfMonth(),
                )
                ->groupBy("month", "month_name")
                ->orderBy("month", "DESC")
                ->get();

            return response()->json([
                "success" => true,
                "data" => $monthlySales,
                "period" => "{$months} bulan terakhir",
                "generated_at" => now()->toDateTimeString(),
            ]);
        } catch (\Exception $e) {
            \Log::error("Export Monthly Sales Error: " . $e->getMessage());

            return response()->json(
                [
                    "success" => false,
                    "message" => "Failed to export monthly sales data",
                ],
                500,
            );
        }
    }
}
