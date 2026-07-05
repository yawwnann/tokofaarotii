<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Category;
use App\Models\StockEntry;
use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        // Only disable CSRF middleware so auth/session still work
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);
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
        $address = UserAddress::factory()->create(['user_id' => $user->id, 'is_default' => true]);

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
        $address = UserAddress::factory()->create(['user_id' => $user->id, 'is_default' => true]);

        $category = Category::create(['name' => 'Test Category']);
        $product = Product::create([
            'name' => 'Produk A',
            'price' => 10000,
            'sku' => 'SKU-' . uniqid(),
            'category_id' => $category->id,
            'weight' => 100,
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

        $codFee = round(20000 * 0.02, 2); // 400
        $total = 20000 + 0 + $codFee; // shipping_cost = 0 when no store_district_id set

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
            'shipping_cost' => 0,
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

    public function test_checkout_process_with_midtrans_works()
    {
        $user = User::factory()->create();
        $address = UserAddress::factory()->create(['user_id' => $user->id, 'is_default' => true]);

        $category = Category::create(['name' => 'Test Category']);
        $product = Product::create([
            'name' => 'Produk B',
            'price' => 15000,
            'sku' => 'SKU-' . uniqid(),
            'category_id' => $category->id,
            'weight' => 100,
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
                             'payment_method' => 'midtrans',
                         ]);

        // Midtrans flow should create order with no COD fee and redirect to payment
        $response->assertRedirect();

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'subtotal' => 15000,
            'shipping_cost' => 0,
            'cod_fee' => 0,
            'total' => 15000,
            'payment_method' => 'midtrans',
            'order_status' => 'menunggu_pembayaran',
        ]);
    }
}
