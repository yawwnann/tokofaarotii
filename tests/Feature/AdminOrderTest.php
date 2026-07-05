<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AdminOrderTest extends TestCase
{
    use DatabaseTransactions;

    private User $admin;
    private User $kasir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

        $this->admin = User::factory()->create(['role' => 'admin_master']);
        $this->kasir = User::factory()->create(['role' => 'kasir']);
    }

    private function createOrder(string $status, string $paymentMethod = 'cod', ?User $user = null): Order
    {
        $user ??= User::factory()->create();
        return Order::create([
            'user_id' => $user->id,
            'invoice' => 'INV-TEST-' . uniqid(),
            'user_address_id' => null,
            'subtotal' => 50000,
            'shipping_cost' => 5000,
            'cod_fee' => $paymentMethod === 'cod' ? 1000 : 0,
            'total' => $paymentMethod === 'cod' ? 56000 : 55000,
            'payment_method' => $paymentMethod,
            'order_status' => $status,
        ]);
    }

    /** @test */
    public function admin_can_view_orders_list()
    {
        $this->createOrder('menunggu_diproses');

        $response = $this->actingAs($this->admin)->get(route('admin.orders.index'));
        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_view_single_order()
    {
        $order = $this->createOrder('menunggu_diproses');

        $response = $this->actingAs($this->admin)->get(route('admin.orders.show', $order));
        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_update_order_status_to_valid_next_state()
    {
        $order = $this->createOrder('menunggu_diproses');

        $response = $this->actingAs($this->admin)->put(route('admin.orders.update', $order), [
            'order_status' => 'diproses',
        ]);

        $response->assertRedirect(route('admin.orders.index'));

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'order_status' => 'diproses',
        ]);
    }

    /** @test */
    public function admin_cannot_update_order_status_to_invalid_state()
    {
        $order = $this->createOrder('menunggu_diproses');

        $response = $this->actingAs($this->admin)->put(route('admin.orders.update', $order), [
            'order_status' => 'selesai',
        ]);

        $response->assertSessionHasErrors('order_status');

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'order_status' => 'menunggu_diproses',
        ]);
    }

    /** @test */
    public function full_happy_path_transition_chain()
    {
        $order = $this->createOrder('menunggu_pembayaran', 'midtrans');

        $transitions = [
            'menunggu_diproses',
            'diproses',
            'dikirim',
            'selesai',
        ];

        foreach ($transitions as $newStatus) {
            $response = $this->actingAs($this->admin)->put(route('admin.orders.update', $order), [
                'order_status' => $newStatus,
            ]);
            $response->assertRedirect();
            $this->assertDatabaseHas('orders', [
                'id' => $order->id,
                'order_status' => $newStatus,
            ]);
        }
    }

    /** @test */
    public function cancellation_chain_from_any_active_state()
    {
        $activeStatuses = ['menunggu_pembayaran', 'menunggu_diproses', 'diproses'];

        foreach ($activeStatuses as $status) {
            $order = $this->createOrder($status);

            $response = $this->actingAs($this->admin)->put(route('admin.orders.update', $order), [
                'order_status' => 'dibatalkan',
            ]);

            $response->assertRedirect();
            $this->assertDatabaseHas('orders', [
                'id' => $order->id,
                'order_status' => 'dibatalkan',
            ]);
        }
    }

    /** @test */
    public function cannot_cancel_order_after_shipped()
    {
        $order = $this->createOrder('dikirim');

        $response = $this->actingAs($this->admin)->put(route('admin.orders.update', $order), [
            'order_status' => 'dibatalkan',
        ]);

        $response->assertSessionHasErrors('order_status');

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'order_status' => 'dikirim',
        ]);
    }

    /** @test */
    public function cannot_change_status_of_completed_order()
    {
        $order = $this->createOrder('selesai');

        $response = $this->actingAs($this->admin)->put(route('admin.orders.update', $order), [
            'order_status' => 'diproses',
        ]);

        $response->assertSessionHasErrors('order_status');

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'order_status' => 'selesai',
        ]);
    }

    /** @test */
    public function cannot_change_status_of_cancelled_order()
    {
        $order = $this->createOrder('dibatalkan');

        $response = $this->actingAs($this->admin)->put(route('admin.orders.update', $order), [
            'order_status' => 'diproses',
        ]);

        $response->assertSessionHasErrors('order_status');

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'order_status' => 'dibatalkan',
        ]);
    }

    /** @test */
    public function kasir_can_access_orders()
    {
        $response = $this->actingAs($this->kasir)->get(route('admin.orders.index'));
        $response->assertStatus(200);
    }

    /** @test */
    public function cod_cancellation_increments_rejection_count()
    {
        $user = User::factory()->create(['cod_rejection_count' => 0]);
        $order = $this->createOrder('menunggu_diproses', 'cod', $user);

        $this->actingAs($this->admin)->put(route('admin.orders.update', $order), [
            'order_status' => 'dibatalkan',
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'cod_rejection_count' => 1,
        ]);
    }

    /** @test */
    public function cod_cancellation_blocks_user_after_3_rejections()
    {
        $user = User::factory()->create(['cod_rejection_count' => 2, 'cod_blocked_until' => null]);

        $order = $this->createOrder('menunggu_diproses', 'cod', $user);

        $this->actingAs($this->admin)->put(route('admin.orders.update', $order), [
            'order_status' => 'dibatalkan',
        ]);

        $user->refresh();
        $this->assertEquals(3, $user->cod_rejection_count);
        $this->assertNotNull($user->cod_blocked_until);
    }

    /** @test */
    public function non_cod_cancellation_does_not_increment_rejection()
    {
        $user = User::factory()->create(['cod_rejection_count' => 0]);
        $order = $this->createOrder('menunggu_pembayaran', 'midtrans', $user);

        $this->actingAs($this->admin)->put(route('admin.orders.update', $order), [
            'order_status' => 'dibatalkan',
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'cod_rejection_count' => 0,
        ]);
    }

    /** @test */
    public function update_can_set_courier_and_tracking_number()
    {
        $order = $this->createOrder('diproses');

        $response = $this->actingAs($this->admin)->put(route('admin.orders.update', $order), [
            'order_status' => 'dikirim',
            'courier' => 'JNE',
            'tracking_number' => 'TRK123456789',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'order_status' => 'dikirim',
            'courier' => 'JNE',
            'tracking_number' => 'TRK123456789',
        ]);
    }
}
