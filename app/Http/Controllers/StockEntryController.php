<?php

namespace App\Http\Controllers;

use App\Models\StockEntry;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StockEntryController extends Controller
{
    public function index()
    {
        // withStockData di nested eager load — menambahkan agregat stok langsung ke
        // setiap produk yang dimuat, tanpa query per baris.
        $query = StockEntry::with([
            "product" => fn($q) => $q->withStockData(),
        ])->where("type", "in");

        // Filter by store for kasir
        $user = Auth::user();
        if ($user->role === "kasir" && $user->store_id) {
            $query->whereHas("product", function ($q) use ($user) {
                $q->where("store_id", $user->store_id);
            });
        }

        // Filter by product
        if (request("product_id")) {
            $query->where("product_id", request("product_id"));
        }

        // Filter by date
        if (request("start_date")) {
            $query->whereDate("created_at", ">=", request("start_date"));
        }

        $stockEntries = $query->latest()->paginate(10);

        // Inject penjualan online ke produk-produk yang tampil di halaman ini — 1 query agregat.
        $visibleProducts = $stockEntries
            ->getCollection()
            ->pluck("product")
            ->filter()
            ->unique("id")
            ->values();
        Product::injectOnlineSold($visibleProducts);

        $products = Product::orderBy("name")
            ->when($user->role === "kasir" && $user->store_id, function (
                $q,
            ) use ($user) {
                $q->where("store_id", $user->store_id);
            })
            ->get();

        // Satu query agregat menggantikan 4 query duplikat sebelumnya
        $stockStats = StockEntry::where("type", "in")
            ->selectRaw(
                "SUM(quantity) as total_in, COUNT(*) as total_transactions",
            )
            ->first();
        $totalIn = (int) ($stockStats->total_in ?? 0);
        $totalTransactions = (int) ($stockStats->total_transactions ?? 0);

        return view(
            "stock-entries.index",
            compact("stockEntries", "products", "totalIn", "totalTransactions"),
        );
    }

    public function create()
    {
        $user = Auth::user();
        $products = Product::orderBy("name")
            ->when($user->role === "kasir" && $user->store_id, function (
                $q,
            ) use ($user) {
                $q->where("store_id", $user->store_id);
            })
            ->get();
        return view("stock-entries.create", compact("products"));
    }

    public function store(Request $request)
    {
        $request->validate([
            "product_id" => "required|exists:products,id",
            "quantity" => "required|integer|min:1",
            "entry_date" => "required|date",
        ]);

        // Simpan riwayat stok masuk (selalu tipe 'in')
        $data = $request->all();
        $data["type"] = "in";

        StockEntry::create($data);

        return redirect()
            ->route("stock-entries.index")
            ->with("success", "Stok berhasil ditambahkan!");
    }

    public function show(StockEntry $stockEntry)
    {
        $stockEntry->load("product");
        return view("stock-entries.show", compact("stockEntry"));
    }

    public function edit(StockEntry $stockEntry)
    {
        $user = Auth::user();
        $products = Product::orderBy("name")
            ->when($user->role === "kasir" && $user->store_id, function (
                $q,
            ) use ($user) {
                $q->where("store_id", $user->store_id);
            })
            ->get();
        return view("stock-entries.edit", compact("stockEntry", "products"));
    }

    public function update(Request $request, StockEntry $stockEntry)
    {
        $request->validate([
            "product_id" => "required|exists:products,id",
            "quantity" => "required|integer|min:1",
            "entry_date" => "required|date",
            "supplier" => "nullable|string|max:255",
            "notes" => "nullable|string",
        ]);

        $data = $request->all();
        $data["type"] = "in"; // Tetap paksa tipe 'in'

        $stockEntry->update($data);

        return redirect()
            ->route("stock-entries.index")
            ->with("success", "Stok berhasil diperbarui");
    }

    public function destroy(StockEntry $stockEntry)
    {
        $stockEntry->delete();

        return redirect()
            ->route("stock-entries.index")
            ->with("success", "Stok berhasil dihapus");
    }
}
