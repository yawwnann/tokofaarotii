<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::where('user_id', Auth::id())->latest()->paginate(10);
        return view('customer.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Akses ditolak');
        }

        $order->load(['items.product', 'address']);
        return view('customer.orders.show', compact('order'));
    }

    public function confirm(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Akses ditolak');
        }

        if ($order->order_status !== 'dikirim') {
            return back()->with('error', 'Status pesanan tidak valid untuk dikonfirmasi.');
        }

        $order->update([
            'order_status' => 'selesai'
        ]);

        return back()->with('success', 'Pesanan telah dikonfirmasi selesai. Terima kasih telah berbelanja!');
    }
}
