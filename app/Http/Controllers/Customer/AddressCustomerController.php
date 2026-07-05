<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressCustomerController extends Controller
{
    public function index()
    {
        $addresses = UserAddress::where('user_id', Auth::id())
                        ->orderByDesc('is_default')
                        ->latest()
                        ->get();

        return view('customer.address.index', compact('addresses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'label' => 'required|string|max:255',
            'receiver_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'province_id' => 'required|string',
            'province' => 'required|string|max:255',
            'city_id' => 'required|string',
            'city' => 'required|string|max:255',
            'district_id' => 'required|string',
            'district' => 'required|string|max:255',
            'village_id' => 'nullable|string',
            'village' => 'nullable|string|max:255',
            'postal_code' => 'required|string|max:20',
            'address' => 'required|string',
        ]);

        $isFirst = UserAddress::where('user_id', Auth::id())->count() === 0;

        UserAddress::create(array_merge($request->all(), [
            'user_id' => Auth::id(),
            'is_default' => $isFirst,
        ]));

        return redirect()->back()->with('success', 'Alamat berhasil ditambahkan.');
    }

    public function update(Request $request, UserAddress $address)
    {
        if ($address->user_id !== Auth::id()) abort(403);

        $request->validate([
            'label' => 'required|string|max:255',
            'receiver_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'province_id' => 'required|string',
            'province' => 'required|string|max:255',
            'city_id' => 'required|string',
            'city' => 'required|string|max:255',
            'district_id' => 'required|string',
            'district' => 'required|string|max:255',
            'village_id' => 'nullable|string',
            'village' => 'nullable|string|max:255',
            'postal_code' => 'required|string|max:20',
            'address' => 'required|string',
        ]);

        $address->update($request->all());

        return redirect()->back()->with('success', 'Alamat berhasil diperbarui.');
    }

    public function destroy(UserAddress $address)
    {
        if ($address->user_id !== Auth::id()) abort(403);
        $address->delete();
        return redirect()->back()->with('success', 'Alamat berhasil dihapus.');
    }

    public function setAsDefault(UserAddress $address)
    {
        if ($address->user_id !== Auth::id()) abort(403);
        
        UserAddress::where('user_id', Auth::id())->update(['is_default' => false]);
        $address->update(['is_default' => true]);

        return redirect()->back()->with('success', 'Alamat utama berhasil diubah.');
    }
}