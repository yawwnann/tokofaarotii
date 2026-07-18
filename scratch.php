<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$product = App\Models\Product::first();
echo "Stok Masuk: " . $product->stockEntries()->where('type', 'in')->sum('quantity') . "\n";
echo "Keluar Offline: " . $product->sales()->where('status', 'completed')->sum('quantity_sold') . "\n";
echo "Keluar Online: " . App\Models\OrderItem::where('product_id', $product->id)
    ->whereHas('order', fn($q) => $q->where('order_status', '!=', 'dibatalkan'))
    ->sum('quantity') . "\n";
echo "Total Stok (Method): " . $product->total_stok . "\n";
