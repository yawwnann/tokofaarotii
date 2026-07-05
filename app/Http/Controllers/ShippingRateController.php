<?php

namespace App\Http\Controllers;

use App\Models\Province;
use App\Models\ShippingRate;
use Illuminate\Http\Request;

class ShippingRateController extends Controller
{
    public function index()
    {
        $rates = ShippingRate::with([
            'originDistrict.regency.province',
            'destinationDistrict.regency.province',
        ])->latest()->paginate(20);
        return view('shipping_rates.index', compact('rates'));
    }

    public function create()
    {
        $provinces = Province::orderBy('name')->get();
        return view('shipping_rates.form', compact('provinces'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'origin_district_id' => 'required|exists:indonesia_districts,id',
            'destination_district_id' => 'required|exists:indonesia_districts,id|different:origin_district_id',
            'rate' => 'required|numeric|min:0',
        ]);

        ShippingRate::create($request->all());

        return redirect()->route('shipping-rates.index')
            ->with('success', 'Tarif ongkir berhasil ditambahkan.');
    }

    public function edit(ShippingRate $shippingRate)
    {
        $provinces = Province::orderBy('name')->get();
        return view('shipping_rates.form', compact('shippingRate', 'provinces'));
    }

    public function update(Request $request, ShippingRate $shippingRate)
    {
        $request->validate([
            'origin_district_id' => 'required|exists:indonesia_districts,id',
            'destination_district_id' => 'required|exists:indonesia_districts,id|different:origin_district_id',
            'rate' => 'required|numeric|min:0',
        ]);

        $shippingRate->update($request->all());

        return redirect()->route('shipping-rates.index')
            ->with('success', 'Tarif ongkir berhasil diperbarui.');
    }

    public function destroy(ShippingRate $shippingRate)
    {
        $shippingRate->delete();

        return redirect()->route('shipping-rates.index')
            ->with('success', 'Tarif ongkir berhasil dihapus.');
    }
}
