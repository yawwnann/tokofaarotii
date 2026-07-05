<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Category;
use App\Models\StockEntry;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\Store;
use App\Models\ShippingZone;
use App\Models\Province;
use App\Models\Regency;
use App\Models\District;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use DatabaseTransactions;

    private Province $province;
    private Regency $regency;
    private District $district;
    private Store $store;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

        $this->province = Province::firstOrCreate(
            ['id' => '31'],
            ['name' => 'DKI JAKARTA', 'island' => 'Jawa']
        );
        $this->regency = Regency::firstOrCreate(
            ['id' => '3171'],
            ['province_id' => '31', 'name' => 'KOTA JAKARTA SELATAN']
        );
        $this->district = District::firstOrCreate(
            ['id' => '3171010'],
            ['regency_id' => '3171', 'name' => 'JAGAKARSA']
        );

        $this->store = Store::create([
            'name' => 'Toko Test',
            'slug' => 'toko-test',
            'district_id' => $this->district->id,
            'address' => 'Jl. Test',
            'is_active' => true,
        ]);

        foreach (ShippingZone::levels() as $level => $label) {
            ShippingZone::create([
                'store_id' => $this->store->id,
                'zone_level' => $level,
                'rate' => 5000,
            ]);
        }
    }

    public function test_checkout_index_redirects_if_cart_empty()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/checkout');

        $response->assertRedirect(route('cart.index'));
        $response->assertSessionHasErrors('cart');
    }

    public function test_checkout_index_displays_address_and_cart()
    {
        $user = User::factory()->create();
        $address = UserAddress::factory()->create([
            'user_id' => $user->id,
            'is_default' => true,
            'province_id' => $this->province->id,
            'city_id' => $this->regency->id,
            'district_id' => $this->district->id,
        ]);

        $cart = [
            99 => [
                'id' => 99,
                'name' => 'Produk A',
                'quantity' => 2,
                'price' => 10000,
                'image' => null
            ]
        ];

        $response = $this->actingAs($user)
                         ->withSession(['cart' => $cart])
                         ->get('/checkout');

        $response->assertStatus(200);
        $response->assertSee('Produk A');
        $response->assertSee($address->receiver_name);
    }

    public function test_checkout_process_creates_order_and_clears_cart()
    {
        $user = User::factory()->create();
        $address = UserAddress::factory()->create([
            'user_id' => $user->id,
            'is_default' => true,
            'province_id' => $this->province->id,
            'city_id' => $this->regency->id,
            'district_id' => $this->district->id,
        ]);

        $category = Category::create(['name' => 'Test Category']);
        $product = Product::create([
            'name' => 'Produk A',
            'price' => 10000,
            'sku' => 'SKU-' . uniqid(),
            'category_id' => $category->id,
            'weight' => 100,
            'store_id' => $this->store->id,
        ]);

        StockEntry::create([
            'product_id' => $product->id,
            'type' => 'in',
            'quantity' => 10,
            'entry_date' => now(),
        ]);

        $cart = [
            $product->id => [
                'id' => $product->id,
                'name' => $product->name,
                'quantity' => 2,
                'price' => $product->price,
                'image' => null
            ]
        ];

        $shippingCost = 5000; // same_district rate
        $codFee = round(20000 * 0.02, 2); // 400
        $total = 20000 + $shippingCost + $codFee;

        $response = $this->actingAs($user)
                         ->withSession(['cart' => $cart])
                         ->post('/checkout/process', [
                             'address_id' => $address->id,
                             'payment_method' => 'cod',
                         ]);

        $response->assertRedirect(route('welcome'));

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'user_address_id' => $address->id,
            'subtotal' => 20000,
            'shipping_cost' => $shippingCost,
            'cod_fee' => $codFee,
            'total' => $total,
            'payment_method' => 'cod',
            'order_status' => 'menunggu_diproses',
        ]);

        $this->assertDatabaseHas('order_items', [
            'product_id' => $product->id,
            'quantity' => 2,
            'subtotal' => 20000,
        ]);

        $this->assertNull(session('cart'));
    }

    public function test_checkout_process_rejects_without_shipping_rates()
    {
        $store2 = Store::create([
            'name' => 'Toko Tanpa Tarif',
            'slug' => 'toko-tanpa-tarif',
            'district_id' => $this->district->id,
            'address' => 'Jl. Lain',
            'is_active' => true,
        ]);

        $user = User::factory()->create();
        $address = UserAddress::factory()->create([
            'user_id' => $user->id,
            'is_default' => true,
            'province_id' => $this->province->id,
            'city_id' => $this->regency->id,
            'district_id' => $this->district->id,
        ]);

        $category = Category::create(['name' => 'Test Category']);
        $product = Product::create([
            'name' => 'Produk Tanpa Ongkir',
            'price' => 15000,
            'sku' => 'SKU-' . uniqid(),
            'category_id' => $category->id,
            'weight' => 100,
            'store_id' => $store2->id,
        ]);

        StockEntry::create([
            'product_id' => $product->id,
            'type' => 'in',
            'quantity' => 10,
            'entry_date' => now(),
        ]);

        $cart = [
            $product->id => [
                'id' => $product->id,
                'name' => $product->name,
                'quantity' => 1,
                'price' => $product->price,
                'image' => null
            ]
        ];

        $response = $this->actingAs($user)
                         ->withSession(['cart' => $cart])
                         ->post('/checkout/process', [
                             'address_id' => $address->id,
                             'payment_method' => 'cod',
                         ]);

        $response->assertRedirect(route('checkout.index'));
        $response->assertSessionHasErrors('shipping');
    }
}
