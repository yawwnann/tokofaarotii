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
        $orders = Order::with("user")->latest()->paginate(10);
        return view("admin.orders.index", compact("orders"));
    }

    public function show(Order $order)
    {
        $order->load(["user", "address", "items.product"]);
        return view("admin.orders.show", compact("order"));
    }

    // Tiga status yang bisa dipilih admin secara manual.
    private const ALLOWED_STATUSES = ["diproses", "dikirim", "selesai"];

    // Transisi yang valid: dari status saat ini → status berikutnya yang boleh dipilih.
    // Hanya mencakup tiga status yang diizinkan.
    // Pesanan di status awal (menunggu_*) bisa langsung diproses oleh admin.
    private const VALID_TRANSITIONS = [
        "menunggu_pembayaran" => ["diproses"],
        "menunggu_diproses" => ["diproses"],
        "diproses" => ["dikirim"],
        "sedang_dikemas" => ["dikirim"], // fallback untuk data lama
        "dikirim" => ["selesai"],
        "selesai" => [],
        "dibatalkan" => [],
    ];

    public function update(Request $request, Order $order)
    {
        $request->validate(
            [
                "order_status" => "required|string",
                "courier" => "nullable|string|max:100",
                // Nomor resi wajib diisi ketika status diubah menjadi 'dikirim'
                "tracking_number" =>
                    $request->order_status === "dikirim"
                        ? "required|string|max:100"
                        : "nullable|string|max:100",
            ],
            [
                "tracking_number.required" =>
                    "Nomor resi pengiriman wajib diisi saat status diubah ke Dikirim.",
            ],
        );

        $newStatus = $request->order_status;
        $oldStatus = $order->order_status;

        // Pastikan status tujuan hanya dari tiga yang diizinkan
        if (!in_array($newStatus, self::ALLOWED_STATUSES)) {
            return redirect()
                ->back()
                ->withErrors([
                    "order_status" => "Status '$newStatus' tidak diizinkan.",
                ]);
        }

        // Validasi state machine: cek apakah transisi ini valid
        $allowed = self::VALID_TRANSITIONS[$oldStatus] ?? [];
        if (!in_array($newStatus, $allowed)) {
            return redirect()
                ->back()
                ->withErrors([
                    "order_status" => "Tidak bisa mengubah status dari '$oldStatus' ke '$newStatus'.",
                ]);
        }

        $order->update([
            "order_status" => $newStatus,
            "courier" => $request->courier,
            "tracking_number" => $request->tracking_number,
        ]);

        // Jika order COD dibatalkan, increment cod_rejection_count user
        if (
            $order->payment_method === "cod" &&
            $request->order_status === "dibatalkan" &&
            $oldStatus !== "dibatalkan"
        ) {
            $user = $order->user;
            $user->increment("cod_rejection_count");

            if ($user->cod_rejection_count >= 3) {
                $user->update([
                    "cod_blocked_until" => now()->addDays(30),
                ]);
            }
        }

        return redirect()
            ->route("admin.orders.index")
            ->with("success", "Status pesanan berhasil diperbarui.");
    }
}
