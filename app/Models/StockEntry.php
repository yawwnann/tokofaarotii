<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockEntry extends Model
{
    // Sesuaikan dengan kolom fillable di database kamu
    protected $fillable = ['product_id', 'type', 'quantity', 'entry_date', 'supplier', 'notes'];

    /**
     * Relasi ke model Product
     * Hubungan: Satu entri stok mencatat satu produk tertentu
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}