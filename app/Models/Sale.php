<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = [
        'store_id',
        'product_id',
        'quantity_sold',
        'total_price',
        'price_at_sale',
        'sale_date',
        'customer_name',
        'source',
        'status',
        'notes',
        'transaction_group',
        'payment_method',
        'invoice_number',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    protected static function booted()
    {
        static::creating(function ($sale) {
            // Mengisi sale_date otomatis dengan tanggal hari ini jika kosong
            $sale->sale_date = $sale->sale_date ?? now();
        });
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}