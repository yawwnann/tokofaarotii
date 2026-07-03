<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Product;
use App\Models\UserAddress;
use App\Models\StockEntry;
use App\Models\Sale;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        // Bersihkan data lama agar tidak menumpuk saat dijalankan berulang
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Sale::truncate();
        Order::truncate();
        OrderItem::truncate();
        StockEntry::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $customer = User::where('role', 'customer')->first();
        if (!$customer) {
            $this->command->warn('Customer user not found. Run UserSeeder first.');
            return;
        }

        // 1. Buat Alamat Customer
        $address = UserAddress::updateOrCreate(
            ['user_id' => $customer->id, 'is_default' => true],
            [
                'receiver_name' => 'Bapak Pelanggan',
                'phone' => '081234567890',
                'address' => 'Jl. Kebon Jeruk No. 12',
                'district' => 'Kebon Jeruk',
                'city' => 'Jakarta Barat',
                'province' => 'DKI Jakarta',
                'postal_code' => '11530',
            ]
        );

        $products = Product::all();
        if ($products->isEmpty()) {
            $this->command->warn('No products found. Run ProductSeeder first.');
            return;
        }

        $names = ['Budi', 'Siti', 'Agus', 'Ayu', 'Rudi', 'Rina', 'Joko', 'Dina', 'Tono', 'Yuni'];

        // 2. Buat Data Stok Masuk (StockEntry) & Penjualan Offline (Sale)
        $this->command->info('Seeding Stock Entries & Offline Sales (Distribusi 6 Bulan)...');
        
        foreach ($products as $product) {
            // Beri stok masuk beberapa kali dalam 6 bulan terakhir
            for ($s = 0; $s < rand(2, 4); $s++) {
                $entryDate = Carbon::now()->subDays(rand(1, 180));
                StockEntry::create([
                    'product_id' => $product->id,
                    'type' => 'in',
                    'supplier' => 'Supplier Utama FAA',
                    'notes' => 'Restock reguler',
                    'quantity' => rand(50, 150),
                    'entry_date' => $entryDate,
                    'created_at' => $entryDate,
                    'updated_at' => $entryDate,
                ]);
            }

            // Penjualan Offline (10 - 25 transaksi per produk selama 6 bulan terakhir)
            $saleCount = rand(10, 25);
            for ($i = 0; $i < $saleCount; $i++) {
                $qtySold = rand(1, 5);
                $saleDate = Carbon::now()->subDays(rand(1, 180));
                Sale::create([
                    'product_id' => $product->id,
                    'quantity_sold' => $qtySold,
                    'sale_date' => $saleDate,
                    'total_price' => $qtySold * $product->price,
                    'source' => 'offline',
                    'status' => 'completed',
                    'customer_name' => 'Pelanggan ' . $names[array_rand($names)],
                    'notes' => 'Pembelian langsung di toko',
                    'created_at' => $saleDate,
                    'updated_at' => $saleDate,
                ]);
            }
        }

        // 3. Buat Data Pesanan Online (Order & OrderItem)
        $this->command->info('Seeding Online Orders (Distribusi 6 Bulan)...');
        $statuses = ['selesai', 'selesai', 'selesai', 'dikirim', 'diproses', 'menunggu_pembayaran', 'dibatalkan'];
        
        for ($i = 0; $i < 40; $i++) {
            $orderStatus = $statuses[array_rand($statuses)];
            $paymentStatus = in_array($orderStatus, ['selesai', 'dikirim', 'diproses']) ? 'paid' : ($orderStatus === 'dibatalkan' ? 'failed' : 'pending');
            $paymentMethod = rand(0, 1) ? 'midtrans' : 'cod';
            
            $shippingCost = rand(10, 25) * 1000; // 10rb - 25rb
            $orderDate = Carbon::now()->subDays(rand(1, 180));
            
            $order = Order::create([
                'invoice' => 'INV-' . strtoupper(Str::random(8)),
                'user_id' => $customer->id,
                'user_address_id' => $address->id,
                'subtotal' => 0,
                'shipping_cost' => $shippingCost,
                'total' => 0,
                'payment_method' => $paymentMethod,
                'payment_status' => $paymentStatus,
                'order_status' => $orderStatus,
                'courier' => ['JNE', 'JNT', 'Sicepat'][rand(0, 2)],
                'tracking_number' => in_array($orderStatus, ['dikirim', 'selesai']) ? 'RESI' . rand(100000000, 999999999) : null,
                'notes' => 'Pesanan E-Commerce otomatis',
                'created_at' => $orderDate,
                'updated_at' => $orderDate,
            ]);

            // Tambah 1-4 macam produk ke dalam pesanan
            $orderProducts = $products->random(rand(1, 4));
            $subtotal = 0;

            foreach ($orderProducts as $product) {
                $qty = rand(1, 4);
                $itemSubtotal = $product->price * $qty;
                
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $qty,
                    'price' => $product->price,
                    'subtotal' => $itemSubtotal,
                    'created_at' => $orderDate,
                    'updated_at' => $orderDate,
                ]);

                $subtotal += $itemSubtotal;
            }

            $order->update([
                'subtotal' => $subtotal,
                'total' => $subtotal + $shippingCost
            ]);
        }
        
        $this->command->info('Dummy data (6 months variance) seeded successfully!');
    }
}
