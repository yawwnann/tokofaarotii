<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = Session::get('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('cart.index')->withErrors(['cart' => 'Keranjang belanja kosong.']);
        }

        $address = UserAddress::where('user_id', Auth::id())
                    ->where('is_default', true)
                    ->first();
                    
        // Jika tidak ada yang default, ambil yang terbaru
        if (!$address) {
            $address = UserAddress::where('user_id', Auth::id())->latest()->first();
        }

        $subtotal = array_reduce($cart, function ($carry, $item) {
            return $carry + ($item['price'] * $item['quantity']);
        }, 0);

        // Ongkir diset gratis untuk tahap testing Midtrans
        $shippingCost = 0;
        $total = $subtotal + $shippingCost;

        return view('customer.checkout.index', compact('cart', 'address', 'subtotal', 'shippingCost', 'total'));
    }

    public function process(Request $request)
    {
        $cart = Session::get('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('cart.index')->withErrors(['cart' => 'Keranjang belanja kosong.']);
        }

        $request->validate([
            'address_id' => 'required|exists:user_addresses,id',
            'payment_method' => 'required|in:midtrans,cod',
        ]);

        $subtotal = array_reduce($cart, function ($carry, $item) {
            return $carry + ($item['price'] * $item['quantity']);
        }, 0);

        $shippingCost = 0; // Sesuai kesepakatan sementara
        $total = $subtotal + $shippingCost;

        $invoice = 'INV-' . date('Ymd') . '-' . rand(1000, 9999);
        $orderStatus = $request->payment_method === 'cod' ? 'menunggu_diproses' : 'menunggu_pembayaran';
        $paymentStatus = 'pending';

        // Gunakan DB Transaction untuk mencegah Race Condition
        try {
            DB::beginTransaction();

            $productIds = array_column($cart, 'id');
            // Pessimistic Locking: Kunci baris produk ini agar transaksi lain tidak bisa memodifikasi/membaca stoknya sampai transaksi ini selesai
            $products = \App\Models\Product::whereIn('id', $productIds)->lockForUpdate()->get()->keyBy('id');

            // 0. Validasi Stok Real-Time
            foreach ($cart as $item) {
                $product = $products->get($item['id']);
                
                if (!$product) {
                    throw new \Exception('Produk "' . $item['name'] . '" tidak ditemukan.');
                }
                
                // Ambil stok real-time (telah memperhitungkan OrderItem lain karena transaksi mereka sudah di-commit sebelum lock ini dilepas)
                if ($product->total_stok < $item['quantity']) {
                    throw new \Exception('Stok produk "' . $product->name . '" tidak mencukupi. Sisa stok: ' . $product->total_stok);
                }
            }

            // 1. Buat Order
            $order = Order::create([
                'invoice' => $invoice,
                'user_id' => Auth::id(),
                'user_address_id' => $request->address_id,
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'total' => $total,
                'payment_method' => $request->payment_method,
                'payment_status' => $paymentStatus,
                'order_status' => $orderStatus,
            ]);

            // 2. Buat Order Items
            foreach ($cart as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['id'],
                    'product_name' => $item['name'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['price'] * $item['quantity'],
                ]);
            }

            // 3. Hapus Keranjang
            Session::forget('cart');

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('checkout.index')->withErrors(['checkout' => $e->getMessage()]);
        }

        // Jika COD, langsung sukses
        if ($request->payment_method === 'cod') {
            return redirect()->route('welcome')->with('success', 'Pesanan COD berhasil dibuat! Menunggu diproses.');
        }

        // Konfigurasi Midtrans
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production');
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

        $params = [
            'transaction_details' => [
                'order_id' => $invoice,
                'gross_amount' => $total,
            ],
            'customer_details' => [
                'first_name' => Auth::user()->name,
                'email' => Auth::user()->email,
                'phone' => UserAddress::find($request->address_id)->phone ?? '',
            ],
        ];

        try {
            $snapToken = \Midtrans\Snap::getSnapToken($params);
            $order->update(['snap_token' => $snapToken]);
        } catch (\Exception $e) {
            return redirect()->route('checkout.index')->withErrors(['midtrans' => 'Gagal terhubung ke server pembayaran: ' . $e->getMessage()]);
        }

        // Arahkan ke halaman pembayaran
        return redirect()->route('payment.show', $order->id)->with('success', 'Pesanan berhasil dibuat! Segera lakukan pembayaran.');
    }
}
