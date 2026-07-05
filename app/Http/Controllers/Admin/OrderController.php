<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('user')->latest()->paginate(10);
        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'address', 'items.product']);
        return view('admin.orders.show', compact('order'));
    }

    // Status transisi yang valid
    private const VALID_TRANSITIONS = [
        'menunggu_pembayaran' => ['menunggu_diproses', 'dibatalkan'],
        'menunggu_diproses'   => ['diproses', 'dibatalkan'],
        'diproses'            => ['dikirim', 'dibatalkan'],
        'dikirim'             => ['selesai'],
        'selesai'             => [],
        'dibatalkan'          => [],
    ];

    public function update(Request $request, Order $order)
    {
        $request->validate([
            'order_status' => 'required|string',
            'courier' => 'nullable|string|max:100',
            'tracking_number' => 'nullable|string|max:100',
        ]);

        $newStatus = $request->order_status;
        $oldStatus = $order->order_status;

        // Validasi state machine
        $allowed = self::VALID_TRANSITIONS[$oldStatus] ?? [];
        if (!in_array($newStatus, $allowed)) {
            return redirect()->back()->withErrors([
                'order_status' => "Status tidak valid: tidak bisa mengubah dari '$oldStatus' ke '$newStatus'."
            ]);
        }

        $order->update([
            'order_status' => $newStatus,
            'courier' => $request->courier,
            'tracking_number' => $request->tracking_number,
        ]);

        // Jika order COD dibatalkan, increment cod_rejection_count user
        if ($order->payment_method === 'cod' && $request->order_status === 'dibatalkan' && $oldStatus !== 'dibatalkan') {
            $user = $order->user;
            $user->increment('cod_rejection_count');

            if ($user->cod_rejection_count >= 3) {
                $user->update([
                    'cod_blocked_until' => now()->addDays(30),
                ]);
            }
        }

        return redirect()->route('admin.orders.index')->with('success', 'Status pesanan berhasil diperbarui.');
    }
}
