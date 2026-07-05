<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class MidtransWebhookTest extends TestCase
{
    use DatabaseTransactions;

    private Order $order;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();

        $user = User::factory()->create();
        $this->order = Order::create([
            'user_id' => $user->id,
            'invoice' => 'INV-TEST-WEBHOOK',
            'subtotal' => 50000,
            'shipping_cost' => 5000,
            'cod_fee' => 0,
            'total' => 55000,
            'payment_method' => 'midtrans',
            'payment_status' => 'pending',
            'order_status' => 'menunggu_pembayaran',
            'snap_token' => 'dummy-snap-token',
        ]);
    }

    private function postWebhook(array $overrides = []): \Illuminate\Testing\TestResponse
    {
        $payload = array_merge([
            'order_id' => $this->order->invoice,
            'transaction_id' => 'TRX-MIDTRANS-' . uniqid(),
            'transaction_status' => 'settlement',
            'fraud_status' => 'accept',
            'status_code' => '200',
            'gross_amount' => '55000.00',
            'payment_type' => 'bank_transfer',
            'transaction_time' => now()->toIso8601String(),
        ], $overrides);

        return $this->postJson('/api/midtrans/callback', $payload);
    }

    public function test_settlement_updates_order_to_paid()
    {
        $response = $this->postWebhook([
            'transaction_status' => 'settlement',
            'fraud_status' => 'accept',
        ]);

        $response->assertOk();
        $response->assertJson(['message' => 'Success']);

        $this->assertDatabaseHas('orders', [
            'id' => $this->order->id,
            'payment_status' => 'paid',
            'order_status' => 'menunggu_diproses',
        ]);
    }

    public function test_capture_updates_order_to_paid()
    {
        $response = $this->postWebhook([
            'transaction_status' => 'capture',
            'fraud_status' => 'accept',
        ]);

        $response->assertOk();

        $this->assertDatabaseHas('orders', [
            'id' => $this->order->id,
            'payment_status' => 'paid',
            'order_status' => 'menunggu_diproses',
        ]);
    }

    public function test_capture_with_challenge_leads_pending()
    {
        $response = $this->postWebhook([
            'transaction_status' => 'capture',
            'fraud_status' => 'challenge',
        ]);

        $response->assertOk();

        $this->assertDatabaseHas('orders', [
            'id' => $this->order->id,
            'payment_status' => 'pending',
            'order_status' => 'menunggu_pembayaran',
        ]);
    }

    public function test_cancel_updates_order_to_failed()
    {
        $response = $this->postWebhook([
            'transaction_status' => 'cancel',
        ]);

        $response->assertOk();

        $this->assertDatabaseHas('orders', [
            'id' => $this->order->id,
            'payment_status' => 'failed',
            'order_status' => 'dibatalkan',
        ]);
    }

    public function test_deny_updates_order_to_failed()
    {
        $response = $this->postWebhook([
            'transaction_status' => 'deny',
        ]);

        $response->assertOk();

        $this->assertDatabaseHas('orders', [
            'id' => $this->order->id,
            'payment_status' => 'failed',
            'order_status' => 'dibatalkan',
        ]);
    }

    public function test_expire_updates_order_to_failed()
    {
        $response = $this->postWebhook([
            'transaction_status' => 'expire',
        ]);

        $response->assertOk();

        $this->assertDatabaseHas('orders', [
            'id' => $this->order->id,
            'payment_status' => 'failed',
            'order_status' => 'dibatalkan',
        ]);
    }

    public function test_pending_status_keeps_order_pending()
    {
        $response = $this->postWebhook([
            'transaction_status' => 'pending',
        ]);

        $response->assertOk();

        $this->assertDatabaseHas('orders', [
            'id' => $this->order->id,
            'payment_status' => 'pending',
            'order_status' => 'menunggu_pembayaran',
        ]);
    }

    public function test_returns_404_for_unknown_order()
    {
        $response = $this->postJson('/api/midtrans/callback', [
            'order_id' => 'INV-NONEXISTENT',
            'transaction_id' => 'TRX-TEST',
            'transaction_status' => 'settlement',
        ]);

        $response->assertNotFound();
        $response->assertJson(['message' => 'Order not found']);
    }

    public function test_returns_400_for_invalid_json()
    {
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

        $response = $this->post('/api/midtrans/callback', [
            'transaction_id' => 'TRX-TEST',
        ], ['Content-Type' => 'application/json']);

        $response->assertStatus(400);
    }

    public function test_settlement_saves_transaction_id()
    {
        $transactionId = 'TRX-BANK-' . uniqid();

        $response = $this->postWebhook([
            'transaction_status' => 'settlement',
            'fraud_status' => 'accept',
            'transaction_id' => $transactionId,
        ]);

        $response->assertOk();

        $this->assertDatabaseHas('orders', [
            'id' => $this->order->id,
            'transaction_id' => $transactionId,
        ]);
    }

    public function test_webhook_is_accessible_without_auth()
    {
        $response = $this->postJson('/api/midtrans/callback', [
            'order_id' => 'INV-NONEXISTENT',
            'transaction_id' => 'TRX-TEST',
            'transaction_status' => 'settlement',
        ]);

        $this->assertNotEquals(401, $response->getStatusCode());
        $this->assertNotEquals(403, $response->getStatusCode());
    }
}
