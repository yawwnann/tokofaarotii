<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$entries = App\Models\StockEntry::select('type', \DB::raw('count(*) as total'))->groupBy('type')->get();
foreach ($entries as $entry) {
    echo $entry->type . ": " . $entry->total . "\n";
}
