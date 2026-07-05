<?php

namespace App\Http\Controllers;

use App\Models\Province;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StoreController extends Controller
{
    public function index()
    {
        $stores = Store::with('district.regency.province')->latest()->paginate(20);
        return view('stores.index', compact('stores'));
    }

    public function create()
    {
        $provinces = Province::orderBy('name')->get();
        return view('stores.form', compact('provinces'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:stores,slug',
            'district_id' => 'required|exists:indonesia_districts,id',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'is_active' => 'required|boolean',
        ]);

        $data = $request->all();
        $data['slug'] = $request->slug ?: Str::slug($request->name);

        // Ensure unique slug
        $baseSlug = $data['slug'];
        $counter = 1;
        while (Store::where('slug', $data['slug'])->exists()) {
            $data['slug'] = $baseSlug . '-' . $counter;
            $counter++;
        }

        Store::create($data);

        return redirect()->route('stores.index')
            ->with('success', 'Toko berhasil ditambahkan.');
    }

    public function edit(Store $store)
    {
        $provinces = Province::orderBy('name')->get();
        return view('stores.form', compact('store', 'provinces'));
    }

    public function update(Request $request, Store $store)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:stores,slug,' . $store->id,
            'district_id' => 'required|exists:indonesia_districts,id',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'is_active' => 'required|boolean',
        ]);

        $data = $request->all();
        $data['slug'] = $request->slug ?: Str::slug($request->name);

        $store->update($data);

        return redirect()->route('stores.index')
            ->with('success', 'Toko berhasil diperbarui.');
    }

    public function destroy(Store $store)
    {
        // Cegah hapus jika masih ada user atau product terkait
        $userCount = User::where('store_id', $store->id)->count();
        $productCount = $store->products()->count();

        if ($userCount > 0) {
            return redirect()->route('stores.index')
                ->with('error', "Tidak dapat menghapus toko karena masih ada {$userCount} user yang terikat.");
        }

        if ($productCount > 0) {
            return redirect()->route('stores.index')
                ->with('error', "Tidak dapat menghapus toko karena masih ada {$productCount} produk yang terikat.");
        }

        $store->delete();

        return redirect()->route('stores.index')
            ->with('success', 'Toko berhasil dihapus.');
    }
}
