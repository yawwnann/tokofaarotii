<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $categories = Category::all();
        $user = Auth::user();

        $products = Product::with("category", "store")
            ->withStockData() // agregat stok masuk & terjual offline — 2 query tambahan flat
            // Filter berdasarkan toko jika user adalah kasir
            ->when($user->role === "kasir" && $user->store_id, function (
                $query,
            ) use ($user) {
                $query->where("store_id", $user->store_id);
            })
            // Filter Kategori
            ->when($request->category_id, function ($query) use ($request) {
                $query->where("category_id", $request->category_id);
            })
            // Filter Pencarian Nama atau SKU
            ->when($request->search, function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where(
                        "name",
                        "like",
                        "%" . $request->search . "%",
                    )->orWhere("sku", "like", "%" . $request->search . "%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString(); // Menjaga filter tetap ada saat pindah halaman

        // Inject penjualan online — 1 query agregat untuk seluruh halaman
        Product::injectOnlineSold($products->getCollection());

        return view("products.index", compact("products", "categories"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return redirect()->route("products.index");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            "name" => "required|string|max:255",
            "sku" => "nullable|string|max:50",
            "description" => "nullable|string",
            "price" => "required|numeric|min:0",
            "category_id" => "required|exists:categories,id",
            "unit" => "required|string|max:20",
            "image" => "nullable|image|mimes:jpeg,png,jpg,gif|max:2048",
        ]);

        // Auto-assign store_id
        $user = Auth::user();
        if ($user->role === "kasir" && $user->store_id) {
            $validated["store_id"] = $user->store_id;
        } elseif ($user->role === "admin_master" && $request->store_id) {
            // Admin bisa override store_id
            $validated["store_id"] = $request->store_id;
        }

        if ($request->hasFile("image")) {
            $validated["image"] = $request
                ->file("image")
                ->store("products", "public");
        }

        Product::create($validated);

        return redirect()
            ->route("products.index")
            ->with("success", "Produk berhasil ditambahkan");
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        // Eager-load relasi yang diakses di view; total_stok pakai path lambat (single-product)
        $product->load("category", "store", "stockEntries", "sales");
        return view("products.show", compact("product"));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $categories = Category::all();
        $stores = Store::where("is_active", true)->get();
        return view(
            "products.edit",
            compact("product", "categories", "stores"),
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            "name" => "required|string|max:255",
            "sku" => "nullable|string|max:50",
            "description" => "nullable|string",
            "price" => "required|numeric|min:0",
            "category_id" => "required|exists:categories,id",
            "unit" => "required|string|max:20",
            "image" => "nullable|image|mimes:jpeg,png,jpg,gif|max:2048",
            "store_id" => "nullable|exists:stores,id",
        ]);

        // Update store_id: admin bisa ganti, kasir tidak bisa ubah
        $user = Auth::user();
        if ($user->role === "admin_master" && $request->has("store_id")) {
            $validated["store_id"] = $request->store_id;
        }
        // Kasir: store_id tetap dari tokonya (tidak bisa diubah)
        // Produk legacy tanpa store_id: tetap null (fallback ke settings)

        if ($request->hasFile("image")) {
            // Delete old image if exists
            if ($product->image) {
                Storage::disk("public")->delete($product->image);
            }
            $validated["image"] = $request
                ->file("image")
                ->store("products", "public");
        }

        $product->update($validated);

        return redirect()
            ->route("products.index")
            ->with("success", "Produk berhasil diperbarui");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        // Delete image if exists
        if ($product->image) {
            Storage::disk("public")->delete($product->image);
        }

        $product->delete();

        return redirect()
            ->route("products.index")
            ->with("success", "Produk berhasil dihapus");
    }

    /**
     * Menampilkan katalog produk makanan untuk publik (Frozen Food & Bakery).
     */
    public function produkMakanan()
    {
        // Ambil kategori yang relevan beserta produknya
        $categories = Category::where("name", "like", "%Frozen%")
            ->orWhere("name", "like", "%Bakery%")
            ->orWhere("name", "like", "%Roti%")
            ->with([
                "products" => function ($query) {
                    $query->latest();
                },
            ])
            ->get();

        // Sortir agar Frozen Food muncul pertama jika ada
        $categories = $categories->sortBy(function ($cat) {
            return str_contains(strtolower($cat->name), "frozen") ? 0 : 1;
        });

        return view("produk-makanan", compact("categories"));
    }
}
