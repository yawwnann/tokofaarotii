<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\District;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Province;
use App\Models\Regency;
use App\Models\Sale;
use App\Models\StockEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ProductModelTest extends TestCase
{
    use DatabaseTransactions;

    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();

        $category = Category::create(['name' => 'Test Category']);
        $this->product = Product::create([
            'name' => 'Produk Test',
            'price' => 10000,
            'sku' => 'SKU-' . uniqid(),
            'category_id' => $category->id,
            'weight' => 100,
        ]);
    }

    public function test_total_stok_is_sum_of_stock_entries_when_no_sales_or_orders()
    {
        StockEntry::create([
            'product_id' => $this->product->id,
            'type' => 'in',
            'quantity' => 10,
            'entry_date' => now(),
        ]);
        StockEntry::create([
            'product_id' => $this->product->id,
            'type' => 'in',
            'quantity' => 5,
            'entry_date' => now(),
        ]);

        $this->assertEquals(15, $this->product->total_stok);
    }

    public function test_total_stok_reduces_by_completed_sales()
    {
        StockEntry::create([
            'product_id' => $this->product->id,
            'type' => 'in',
            'quantity' => 20,
            'entry_date' => now(),
        ]);

        Sale::create([
            'product_id' => $this->product->id,
            'quantity_sold' => 5,
            'total_price' => 50000,
            'sale_date' => now(),
            'source' => 'offline',
            'status' => 'completed',
            'payment_method' => 'tunai',
            'transaction_group' => 'TRX-TEST-001',
        ]);

        $this->assertEquals(15, $this->product->total_stok);
    }

    public function test_total_stok_ignores_pending_sales()
    {
        StockEntry::create([
            'product_id' => $this->product->id,
            'type' => 'in',
            'quantity' => 20,
            'entry_date' => now(),
        ]);

        Sale::create([
            'product_id' => $this->product->id,
            'quantity_sold' => 5,
            'total_price' => 50000,
            'sale_date' => now(),
            'source' => 'offline',
            'status' => 'pending',
            'payment_method' => 'transfer',
            'transaction_group' => 'TRX-TEST-001',
        ]);

        $this->assertEquals(20, $this->product->total_stok);
    }

    public function test_total_stok_reduces_by_non_cancelled_order_items()
    {
        StockEntry::create([
            'product_id' => $this->product->id,
            'type' => 'in',
            'quantity' => 20,
            'entry_date' => now(),
        ]);

        $user = User::factory()->create();
        $order = Order::create([
            'user_id' => $user->id,
            'invoice' => 'INV-TEST-' . uniqid(),
            'user_address_id' => null,
            'subtotal' => 20000,
            'shipping_cost' => 5000,
            'cod_fee' => 0,
            'total' => 25000,
            'payment_method' => 'cod',
            'order_status' => 'diproses',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'quantity' => 3,
            'price' => 10000,
            'subtotal' => 30000,
        ]);

        $this->assertEquals(17, $this->product->total_stok);
    }

    public function test_total_stok_ignores_cancelled_order_items()
    {
        StockEntry::create([
            'product_id' => $this->product->id,
            'type' => 'in',
            'quantity' => 20,
            'entry_date' => now(),
        ]);

        $user = User::factory()->create();
        $order = Order::create([
            'user_id' => $user->id,
            'invoice' => 'INV-TEST-' . uniqid(),
            'user_address_id' => null,
            'subtotal' => 20000,
            'shipping_cost' => 5000,
            'cod_fee' => 0,
            'total' => 25000,
            'payment_method' => 'cod',
            'order_status' => 'dibatalkan',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'quantity' => 3,
            'price' => 10000,
            'subtotal' => 30000,
        ]);

        $this->assertEquals(20, $this->product->total_stok);
    }

    public function test_total_stok_includes_multiple_sources()
    {
        StockEntry::create([
            'product_id' => $this->product->id,
            'type' => 'in',
            'quantity' => 50,
            'entry_date' => now(),
        ]);

        Sale::create([
            'product_id' => $this->product->id,
            'quantity_sold' => 10,
            'total_price' => 100000,
            'sale_date' => now(),
            'source' => 'offline',
            'status' => 'completed',
            'payment_method' => 'tunai',
            'transaction_group' => 'TRX-TEST-001',
        ]);

        $user = User::factory()->create();
        $order = Order::create([
            'user_id' => $user->id,
            'invoice' => 'INV-TEST-' . uniqid(),
            'user_address_id' => null,
            'subtotal' => 40000,
            'shipping_cost' => 5000,
            'cod_fee' => 0,
            'total' => 45000,
            'payment_method' => 'cod',
            'order_status' => 'selesai',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'quantity' => 4,
            'price' => 10000,
            'subtotal' => 40000,
        ]);

        $this->assertEquals(36, $this->product->total_stok);
    }

    public function test_total_stok_returns_zero_with_no_stock_entries()
    {
        $this->assertEquals(0, $this->product->total_stok);
    }

    public function test_total_stok_returns_negative_when_over_sold()
    {
        StockEntry::create([
            'product_id' => $this->product->id,
            'type' => 'in',
            'quantity' => 5,
            'entry_date' => now(),
        ]);

        Sale::create([
            'product_id' => $this->product->id,
            'quantity_sold' => 10,
            'total_price' => 100000,
            'sale_date' => now(),
            'source' => 'offline',
            'status' => 'completed',
            'payment_method' => 'tunai',
            'transaction_group' => 'TRX-TEST-001',
        ]);

        $this->assertEquals(-5, $this->product->total_stok);
    }

    public function test_product_belongs_to_category()
    {
        $this->assertNotNull($this->product->category);
        $this->assertEquals('Test Category', $this->product->category->name);
    }

    public function test_product_has_many_stock_entries()
    {
        StockEntry::create([
            'product_id' => $this->product->id,
            'type' => 'in',
            'quantity' => 5,
            'entry_date' => now(),
        ]);
        StockEntry::create([
            'product_id' => $this->product->id,
            'type' => 'in',
            'quantity' => 3,
            'entry_date' => now(),
        ]);

        $this->assertCount(2, $this->product->stockEntries);
    }

    public function test_product_has_many_sales()
    {
        Sale::create([
            'product_id' => $this->product->id,
            'quantity_sold' => 2,
            'total_price' => 20000,
            'sale_date' => now(),
            'source' => 'offline',
            'status' => 'completed',
            'payment_method' => 'tunai',
            'transaction_group' => 'TRX-TEST-001',
        ]);

        $this->assertCount(1, $this->product->sales);
    }

    public function test_product_belongs_to_store()
    {
        Province::create(['id' => '99', 'name' => 'PROVINSI TEST']);
        Regency::create(['id' => '9901', 'province_id' => '99', 'name' => 'KABUPATEN TEST']);
        District::create(['id' => '9901010', 'regency_id' => '9901', 'name' => 'KECAMATAN TEST']);

        $store = \App\Models\Store::create([
            'name' => 'Test Store',
            'slug' => 'test-store-' . uniqid(),
            'district_id' => '9901010',
            'is_active' => true,
        ]);

        $product = Product::create([
            'name' => 'Produk dengan Store',
            'price' => 15000,
            'sku' => 'SKU-' . uniqid(),
            'category_id' => $this->product->category_id,
            'store_id' => $store->id,
            'weight' => 200,
        ]);

        $this->assertInstanceOf(\App\Models\Store::class, $product->store);
        $this->assertEquals($store->id, $product->store->id);
    }
}
