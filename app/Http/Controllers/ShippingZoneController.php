<?php

namespace App\Http\Controllers;

use App\Models\ShippingZone;
use App\Models\Store;
use Illuminate\Http\Request;

class ShippingZoneController extends Controller
{
    public function index()
    {
        $stores = Store::where("is_active", true)
            ->with("district.regency.province")
            ->get();
        $levels = ShippingZone::levels();

        // Satu query untuk semua zona pengiriman seluruh toko,
        // lalu inject ke setiap toko via collection — menghindari N query (1 per toko).
        $allZones = ShippingZone::whereIn("store_id", $stores->pluck("id"))
            ->get()
            ->groupBy("store_id");

        $stores->each(function ($store) use ($allZones) {
            $store->zones = ($allZones[$store->id] ?? collect())->keyBy(
                "zone_level",
            );
        });

        return view("shipping_zones.index", compact("stores", "levels"));
    }

    public function update(Request $request)
    {
        $rates = $request->input("rates", []);
        array_walk_recursive($rates, function (&$value) {
            if (is_string($value)) {
                $value = str_replace(",", "", $value);
            }
        });
        $request->merge(["rates" => $rates]);

        $request->validate([
            "rates" => "required|array",
            "rates.*" => "array",
            "rates.*.*" => "nullable|numeric|min:0",
        ]);

        foreach ($request->rates as $storeId => $zoneRates) {
            foreach ($zoneRates as $level => $rate) {
                if ($rate === null || $rate === "") {
                    continue;
                }

                ShippingZone::updateOrCreate(
                    ["store_id" => $storeId, "zone_level" => $level],
                    ["rate" => $rate],
                );
            }
        }

        return redirect()
            ->route("shipping-zones.index")
            ->with("success", "Tarif ongkir berhasil diperbarui!");
    }
}
