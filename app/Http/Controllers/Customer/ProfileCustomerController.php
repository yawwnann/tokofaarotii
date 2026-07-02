<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserProfile;

class ProfileCustomerController extends Controller
{
    public function index()
    {
        $profile = UserProfile::firstOrCreate(
            ['user_id' => Auth::id()]
        );

        return view('customer.profile.index', compact('profile'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'phone' => 'nullable|max:20',
            'gender' => 'nullable|in:Laki-laki,Perempuan',
            'birth_date' => 'nullable|date',
        ]);

        UserProfile::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'phone' => $request->phone,
                'gender' => $request->gender,
                'birth_date' => $request->birth_date,
            ]
        );

        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}