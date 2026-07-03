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
}