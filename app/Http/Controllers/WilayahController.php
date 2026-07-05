<?php

namespace App\Http\Controllers;

use App\Models\Province;
use App\Models\Regency;
use App\Models\District;
use App\Models\Village;
use Illuminate\Http\Request;

class WilayahController extends Controller
{
    /**
     * Get all provinces.
     */
    public function getProvinces()
    {
        $provinces = Province::orderBy('name')->get(['id', 'name']);
        return response()->json($provinces);
    }

    /**
     * Get regencies by province ID.
     */
    public function getRegencies($provinceId)
    {
        $regencies = Regency::where('province_id', $provinceId)
            ->orderBy('name')
            ->get(['id', 'name']);
        return response()->json($regencies);
    }

    /**
     * Get districts by regency/city ID.
     */
    public function getDistricts($regencyId)
    {
        $districts = District::where('regency_id', $regencyId)
            ->orderBy('name')
            ->get(['id', 'name']);
        return response()->json($districts);
    }

    /**
     * Get villages by district ID.
     */
    public function getVillages($districtId)
    {
        $villages = Village::where('district_id', $districtId)
            ->orderBy('name')
            ->get(['id', 'name']);
        return response()->json($villages);
    }
}
