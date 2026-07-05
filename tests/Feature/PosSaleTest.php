<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\District;
use App\Models\Product;
use App\Models\Province;
use App\Models\Regency;
use App\Models\Sale;
use App\Models\StockEntry;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PosSaleTest extends TestCase
{
    use DatabaseTransactions;

    private User $admin;
    private User $kasir;
    private Store $store;
    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

        Province::create(['id' => '99', 'name' => 'PROVINSI TEST']);
        Regency::create(['id' => '9901', 'province_id' => '99', 'name' => 'KABUPATEN TEST']);
        District::create(['id' => '9901010', 'regency_id' => '9901', 'name' => 'KECAMATAN 1']);
        District::create(['id' => '9901020', 'regency_id' => '9901', 'name' => 'KECAMATAN 2']);

        $this->store = Store::create([
            'name' => 'Toko Test',
            'slug' => 'toko-test-' . uniqid(),
            'district_id' => '9901010',
            'address' => 'Test Address',
            'phone' => '08123456789',
            'is_active' => true,
        ]);

        $this->admin = User::factory()->create(['role' => 'admin_master']);
        $this->kasir = User::factory()->create([
            'role' => 'kasir',
            'store_id' => $this->store->id,
        ]);

        $category = Category::create(['name' => 'Kategori Test']);

        $this->product = Product::create([
            'name' => 'Produk POS Test',
            'price' => 25000,
            'sku' => 'SKU-POS-' . uniqid(),
            'category_id' => $category->id,
            'weight' => 100,
            'store_id' => $this->store->id,
        ]);

        StockEntry::create([
            'product_id' => $this->product->id,
            'type' => 'in',
            'quantity' => 50,
            'entry_date' => now(),
        ]);
    }

    /** @test */
    public function admin_can_access_pos_create_page()
    {
        $response = $this->actingAs($this->admin)->get(route('sales.create'));
        $response->assertStatus(200);
    }

    /** @test */
    public function kasir_can_access_pos_create_page()
    {
        $response = $this->actingAs($this->kasir)->get(route('sales.create'));
        $response->assertStatus(200);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_pos()
    {
        $response = $this->get(route('sales.create'));
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function kasir_only_sees_products_from_their_store()
    {
        $otherStore = Store::create([
            'name' => 'Toko Lain',
            'slug' => 'toko-lain-' . uniqid(),
            'district_id' => '9901020',
            'address' => 'Other Address',
            'is_active' => true,
        ]);

        $otherProduct = Product::create([
            'name' => 'Produk Toko Lain',
            'price' => 10000,
            'sku' => 'SKU-OTHER-' . uniqid(),
            'category_id' => $this->product->category_id,
            'weight' => 100,
            'store_id' => $otherStore->id,
        ]);

        $response = $this->actingAs($this->kasir)->get(route('sales.create'));
        $response->assertStatus(200);
        $response->assertSee($this->product->name);
        $response->assertDontSee($otherProduct->name);
    }

    /** @test */
    public function admin_sees_all_products_at_pos()
    {
        $otherStore = Store::create([
            'name' => 'Toko Lain',
            'slug' => 'toko-lain-' . uniqid(),
            'district_id' => '9901020',
            'address' => 'Other Address',
            'is_active' => true,
        ]);

        $otherProduct = Product::create([
            'name' => 'Produk Toko Lain',
            'price' => 10000,
            'sku' => 'SKU-OTHER-' . uniqid(),
            'category_id' => $this->product->category_id,
            'weight' => 100,
            'store_id' => $otherStore->id,
        ]);

        $response = $this->actingAs($this->admin)->get(route('sales.create'));
        $response->assertStatus(200);
        $response->assertSee($this->product->name);
        $response->assertSee($otherProduct->name);
    }

    /** @test */
    public function pos_store_creates_sale_with_completed_status_for_cash()
    {
        $response = $this->actingAs($this->kasir)->post(route('sales.pos.store'), [
            'items' => [
                ['id' => $this->product->id, 'qty' => 2, 'price' => 25000],
            ],
            'payment_method' => 'tunai',
            'customer_name' => 'John Doe',
        ]);

        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('sales', [
            'product_id' => $this->product->id,
            'quantity_sold' => 2,
            'total_price' => 50000,
            'customer_name' => 'John Doe',
            'payment_method' => 'tunai',
            'status' => 'completed',
            'source' => 'offline',
            'store_id' => $this->kasir->store_id,
        ]);
    }

    /** @test */
    public function pos_store_creates_sale_with_pending_status_for_transfer()
    {
        $response = $this->actingAs($this->kasir)->post(route('sales.pos.store'), [
            'items' => [
                ['id' => $this->product->id, 'qty' => 1, 'price' => 25000],
            ],
            'payment_method' => 'transfer',
            'customer_name' => 'Jane Doe',
        ]);

        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('sales', [
            'product_id' => $this->product->id,
            'quantity_sold' => 1,
            'total_price' => 25000,
            'customer_name' => 'Jane Doe',
            'payment_method' => 'transfer',
            'status' => 'pending',
            'source' => 'offline',
        ]);
    }

    /** @test */
    public function pos_store_fails_with_insufficient_stock()
    {
        $response = $this->actingAs($this->kasir)->post(route('sales.pos.store'), [
            'items' => [
                ['id' => $this->product->id, 'qty' => 999, 'price' => 25000],
            ],
            'payment_method' => 'tunai',
        ]);

        $response->assertJson(['success' => false]);
        $this->assertStringContainsString('Stok produk', $response->json('message'));
    }

    /** @test */
    public function pos_store_fails_with_empty_cart()
    {
        $response = $this->actingAs($this->kasir)->post(route('sales.pos.store'), [
            'items' => [],
            'payment_method' => 'tunai',
        ]);

        $response->assertJson(['success' => false, 'message' => 'Keranjang kosong']);
    }

    /** @test */
    public function pos_store_returns_transaction_id()
    {
        $response = $this->actingAs($this->kasir)->post(route('sales.pos.store'), [
            'items' => [
                ['id' => $this->product->id, 'qty' => 1, 'price' => 25000],
            ],
            'payment_method' => 'tunai',
        ]);

        $response->assertJson(['success' => true]);
        $this->assertNotNull($response->json('transaction_id'));
    }

    /** @test */
    public function pos_store_sets_store_id_from_authenticated_user()
    {
        $response = $this->actingAs($this->kasir)->post(route('sales.pos.store'), [
            'items' => [
                ['id' => $this->product->id, 'qty' => 3, 'price' => 25000],
            ],
            'payment_method' => 'tunai',
        ]);

        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('sales', [
            'product_id' => $this->product->id,
            'store_id' => $this->kasir->store_id,
        ]);
    }

    /** @test */
    public function confirm_route_updates_sale_status_to_completed()
    {
        $sale = Sale::create([
            'product_id' => $this->product->id,
            'quantity_sold' => 2,
            'total_price' => 50000,
            'sale_date' => now(),
            'source' => 'offline',
            'status' => 'pending',
            'payment_method' => 'transfer',
            'transaction_group' => 'TRX-CONFIRM-TEST',
            'store_id' => $this->kasir->store_id,
        ]);

        $response = $this->actingAs($this->admin)->post(route('sales.confirm', $sale));
        $response->assertRedirect();

        $this->assertDatabaseHas('sales', [
            'id' => $sale->id,
            'status' => 'completed',
        ]);
    }

    /** @test */
    public function sale_store_web_route_creates_sale()
    {
        $response = $this->actingAs($this->kasir)->post(route('sales.store'), [
            'product_id' => $this->product->id,
            'quantity_sold' => 2,
            'total_price' => 50000,
            'source' => 'offline',
            'payment_method' => 'tunai',
        ]);

        $response->assertRedirect(route('sales.index'));

        $this->assertDatabaseHas('sales', [
            'product_id' => $this->product->id,
            'quantity_sold' => 2,
            'total_price' => 50000,
            'store_id' => $this->kasir->store_id,
            'status' => 'completed',
        ]);
    }

    /** @test */
    public function sale_store_route_validates_stock_before_creating()
    {
        $response = $this->actingAs($this->kasir)->post(route('sales.store'), [
            'product_id' => $this->product->id,
            'quantity_sold' => 999,
            'total_price' => 999 * 25000,
            'source' => 'offline',
            'payment_method' => 'tunai',
        ]);

        $response->assertSessionHasErrors('quantity_sold');
    }

    /** @test */
    public function sale_index_page_accessible_by_admin()
    {
        Sale::create([
            'product_id' => $this->product->id,
            'quantity_sold' => 1,
            'total_price' => 25000,
            'sale_date' => now(),
            'source' => 'offline',
            'status' => 'completed',
            'payment_method' => 'tunai',
            'transaction_group' => 'TRX-INDEX-TEST',
            'store_id' => $this->kasir->store_id,
        ]);

        $response = $this->actingAs($this->admin)->get(route('sales.index'));
        $response->assertStatus(200);
    }

    /** @test */
    public function pos_store_returns_snap_token_for_midtrans_payment()
    {
        $response = $this->actingAs($this->kasir)->post(route('sales.pos.store'), [
            'items' => [
                ['id' => $this->product->id, 'qty' => 1, 'price' => 25000],
            ],
            'payment_method' => 'transfer',
            'customer_name' => 'Midtrans User',
        ]);

        $response->assertJson(['success' => true, 'is_transfer' => true]);
    }

    /** @test */
    public function sale_destroy_handles_legacy_and_transaction_group_ids()
    {
        $sale = Sale::create([
            'product_id' => $this->product->id,
            'quantity_sold' => 1,
            'total_price' => 25000,
            'sale_date' => now(),
            'source' => 'offline',
            'status' => 'completed',
            'payment_method' => 'tunai',
            'transaction_group' => 'TRX-DELETE-TEST',
            'store_id' => $this->kasir->store_id,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('sales.destroy', 'TRX-DELETE-TEST'));
        $response->assertRedirect();

        $this->assertDatabaseMissing('sales', [
            'transaction_group' => 'TRX-DELETE-TEST',
        ]);
    }
}
