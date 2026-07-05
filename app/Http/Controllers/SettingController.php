<?php

namespace App\Http\Controllers;

use App\Models\Province;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $settings = DB::table('settings')->first();
        $provinces = Province::orderBy('name')->get();

        return view('settings.index', compact('settings', 'provinces'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'store_name' => 'required|string|max:255',
            'store_whatsapp' => 'nullable|string|max:20',
            'store_email' => 'nullable|email|max:255',
            'store_address' => 'nullable|string',
            'store_district_id' => 'nullable|exists:indonesia_districts,id',
            'store_tagline' => 'nullable|string|max:255',
            'store_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'footer_text' => 'nullable|string|max:255',
            'maintenance_mode' => 'required|in:0,1',
        ]);

        $currentSetting = DB::table('settings')->first();

        $data = [
            'store_name' => $request->store_name,
            'store_whatsapp' => $request->store_whatsapp,
            'store_email' => $request->store_email,
            'store_address' => $request->store_address,
            'store_district_id' => $request->store_district_id,
            'store_tagline' => $request->store_tagline,
            'footer_text' => $request->footer_text,
            'maintenance_mode' => $request->maintenance_mode,
            'updated_at' => now(),
        ];

        if ($request->hasFile('store_logo')) {
            if ($currentSetting && $currentSetting->store_logo) {
                Storage::disk('public')->delete($currentSetting->store_logo);
            }

            $data['store_logo'] = $request->file('store_logo')->store('logos', 'public');
        }

        if ($currentSetting) {
            DB::table('settings')->where('id', $currentSetting->id)->update($data);
        } else {
            $data['created_at'] = now();
            DB::table('settings')->insert($data);
        }

        return redirect()->back()->with('success', 'Pengaturan sistem berhasil diperbarui!');
    }
}
