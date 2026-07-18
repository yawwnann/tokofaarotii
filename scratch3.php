<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$product = App\Models\Product::first();
echo "Stok Awal (tanpa withStockData): " . $product->total_stok . "\n";

App\Models\StockEntry::create([
    'product_id' => $product->id,
    'type' => 'out',
    'quantity' => 10,
    'entry_date' => now()
]);

// Refresh product from DB and calculate again
$product = App\Models\Product::withStockData()->find($product->id);
App\Models\Product::injectOnlineSold(collect([$product]));
echo "Stok Akhir (dengan withStockData): " . $product->total_stok . "\n";
