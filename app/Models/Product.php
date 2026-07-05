<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name', 'description', 'sku', 'price', 'unit', 'weight', 'image', 'category_id'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function stockEntries()
    {
        return $this->hasMany(StockEntry::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    /**
     * Menghitung saldo stok asli secara otomatis
     * Logika: Total Barang Masuk - Total Barang Terjual
     */
    public function getTotalStokAttribute()
    {
        // Gunakan logika yang lebih aman agar tidak lambat saat data banyak
        $masuk = $this->stockEntries()->sum('quantity');
        $keluarOffline = $this->sales()->sum('quantity_sold');
        
        // Menghitung barang keluar dari pesanan online (kecuali yang dibatalkan)
        $keluarOnline = OrderItem::where('product_id', $this->id)
            ->whereHas('order', function ($query) {
                $query->where('order_status', '!=', 'dibatalkan');
            })
            ->sum('quantity');

        return $masuk - $keluarOffline - $keluarOnline;
    }
}