<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    protected $fillable = [
        "name",
        "description",
        "sku",
        "price",
        "unit",
        "weight",
        "image",
        "category_id",
        "store_id",
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
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
     * Scope: sertakan agregat stok masuk (type=in) dan penjualan offline (status=completed).
     * Gunakan scope ini di controller saat menampilkan banyak produk sekaligus agar
     * accessor total_stok tidak menjalankan query baru per produk.
     *
     * Kolom yang ditambahkan ke setiap model:
     *   - stock_in_total       → total stok masuk
     *   - offline_sold_total   → total terjual via POS/offline
     */
    public function scopeWithStockData(Builder $query): Builder
    {
        return $query
            ->withSum(
                [
                    "stockEntries as stock_in_total" => fn($q) => $q->where(
                        "type",
                        "in",
                    ),
                ],
                "quantity",
            )
            ->withSum(
                [
                    "sales as offline_sold_total" => fn($q) => $q->where(
                        "status",
                        "completed",
                    ),
                ],
                "quantity_sold",
            );
    }

    /**
     * Inject jumlah penjualan online ke setiap produk dalam collection.
     * Menjalankan SATU query agregat untuk seluruh collection, menghindari N+1
     * dari OrderItem. Harus dipanggil setelah withStockData().
     *
     * Kolom yang ditambahkan ke setiap model:
     *   - online_sold_total    → total terjual via order online (kecuali dibatalkan)
     */
    public static function injectOnlineSold(Collection $products): void
    {
        if ($products->isEmpty()) {
            return;
        }

        $onlineSold = DB::table("order_items")
            ->join("orders", "order_items.order_id", "=", "orders.id")
            ->whereIn("order_items.product_id", $products->pluck("id"))
            ->where("orders.order_status", "!=", "dibatalkan")
            ->select(
                "order_items.product_id",
                DB::raw("SUM(order_items.quantity) as total_quantity"),
            )
            ->groupBy("order_items.product_id")
            ->pluck("total_quantity", "order_items.product_id");

        $products->each(
            fn($p) => ($p->online_sold_total =
                (int) ($onlineSold[$p->id] ?? 0)),
        );
    }

    /**
     * Hitung saldo stok.
     * Logika: Stok Masuk − Terjual Offline (completed) − Terjual Online (non-dibatalkan)
     *
     * Path cepat  — jika controller sudah memanggil withStockData() + injectOnlineSold(),
     *               nilai pre-computed digunakan langsung (0 query tambahan).
     * Path lambat — fallback query langsung untuk konteks single-product
     *               (show, validasi stok tunggal, dsb.).
     */
    public function getTotalStokAttribute(): int
    {
        // Path cepat: atribut pre-computed sudah ada di model
        if (array_key_exists("stock_in_total", $this->attributes)) {
            return (int) ($this->attributes["stock_in_total"] ?? 0) -
                (int) ($this->attributes["offline_sold_total"] ?? 0) -
                (int) ($this->attributes["online_sold_total"] ?? 0);
        }

        // Path lambat: single-product — jalankan query langsung
        $masuk = $this->stockEntries()->where("type", "in")->sum("quantity");
        $keluarOffline = $this->sales()
            ->where("status", "completed")
            ->sum("quantity_sold");
        $keluarOnline = OrderItem::where("product_id", $this->id)
            ->whereHas(
                "order",
                fn($q) => $q->where("order_status", "!=", "dibatalkan"),
            )
            ->sum("quantity");

        return $masuk - $keluarOffline - $keluarOnline;
    }
}
