<?php

namespace Tests\Feature;

use App\Models\Product;
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

        // Mock session cart
        $cart = [
            1 => [
                'id' => 1,
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
        
        // This product doesn't have to exist in DB for cart to work since CheckoutController doesn't requery it
        $cart = [
            1 => [
                'id' => 1,
                'name' => 'Produk A',
                'quantity' => 2,
                'price' => 10000,
                'image' => null
            ]
        ];

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
            'payment_method' => 'cod',
            'order_status' => 'menunggu_diproses',
        ]);

        $this->assertDatabaseHas('order_items', [
            'product_id' => 1,
            'quantity' => 2,
            'subtotal' => 20000,
        ]);

        $this->assertNull(session('cart'));
    }
}
