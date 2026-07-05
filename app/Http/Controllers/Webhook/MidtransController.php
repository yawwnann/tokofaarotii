<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MidtransController extends Controller
{
    public function handle(Request $request)
    {
        $serverKey = config('midtrans.server_key');
        \Midtrans\Config::$serverKey = $serverKey;
        \Midtrans\Config::$isProduction = config('midtrans.is_production');

        $rawBody = $request->getContent();
        $notification = json_decode($rawBody, true);

        if (!$notification || !isset($notification['transaction_id'])) {
            Log::error('Midtrans Webhook: Invalid notification body');
            return response()->json(['message' => 'Invalid notification'], 400);
        }

        $orderId = $notification['order_id'] ?? null;
        $transactionStatus = $notification['transaction_status'] ?? null;
        $fraudStatus = $notification['fraud_status'] ?? null;
        $transactionId = $notification['transaction_id'] ?? null;

        if (!$orderId) {
            Log::error('Midtrans Webhook: Missing order_id');
            return response()->json(['message' => 'Missing order_id'], 400);
        }

        $order = Order::where('invoice', $orderId)->first();

        if (!$order) {
            Log::warning("Midtrans Webhook: Order not found for invoice: $orderId");
            return response()->json(['message' => 'Order not found'], 404);
        }

        if ($transactionStatus == 'capture' || $transactionStatus == 'settlement') {
            if ($fraudStatus == 'challenge') {
                $order->update(['payment_status' => 'pending']);
                Log::info("Midtrans Webhook: Order $orderId challenged");
            } else {
                $order->update([
                    'payment_status' => 'paid',
                    'order_status' => 'menunggu_diproses',
                    'transaction_id' => $transactionId,
                ]);
                Log::info("Midtrans Webhook: Order $orderId paid");
            }
        } elseif (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
            $order->update([
                'payment_status' => 'failed',
                'order_status' => 'dibatalkan',
                'transaction_id' => $transactionId,
            ]);
            Log::info("Midtrans Webhook: Order $orderId $transactionStatus");
        } elseif ($transactionStatus == 'pending') {
            $order->update([
                'payment_status' => 'pending',
                'transaction_id' => $transactionId,
            ]);
        }

        return response()->json(['message' => 'Success']);
    }
}
