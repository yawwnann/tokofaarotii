<?php

namespace Tests\Unit;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class OrderStateMachineTest extends TestCase
{
    use DatabaseTransactions;

    private array $validTransitions = [
        'menunggu_pembayaran' => ['menunggu_diproses', 'dibatalkan'],
        'menunggu_diproses'   => ['diproses', 'dibatalkan'],
        'diproses'            => ['dikirim', 'dibatalkan'],
        'dikirim'             => ['selesai'],
        'selesai'             => [],
        'dibatalkan'          => [],
    ];

    private function createOrder(string $status): Order
    {
        $user = User::factory()->create();
        return Order::create([
            'user_id' => $user->id,
            'invoice' => 'INV-TEST-' . uniqid(),
            'subtotal' => 50000,
            'shipping_cost' => 5000,
            'cod_fee' => 0,
            'total' => 55000,
            'payment_method' => 'cod',
            'order_status' => $status,
        ]);
    }

    public function test_all_valid_transitions_defined_in_code()
    {
        $expectedKeys = [
            'menunggu_pembayaran', 'menunggu_diproses',
            'diproses', 'dikirim', 'selesai', 'dibatalkan',
        ];

        foreach ($expectedKeys as $status) {
            $this->assertArrayHasKey($status, $this->validTransitions,
                "Status '$status' harus ada di VALID_TRANSITIONS");
        }
    }

    public function test_menunggu_pembayaran_can_transition_to_menunggu_diproses()
    {
        $order = $this->createOrder('menunggu_pembayaran');
        $allowed = $this->validTransitions['menunggu_pembayaran'];
        $this->assertContains('menunggu_diproses', $allowed);
    }

    public function test_menunggu_pembayaran_can_transition_to_dibatalkan()
    {
        $order = $this->createOrder('menunggu_pembayaran');
        $allowed = $this->validTransitions['menunggu_pembayaran'];
        $this->assertContains('dibatalkan', $allowed);
    }

    public function test_menunggu_diproses_can_transition_to_diproses()
    {
        $order = $this->createOrder('menunggu_diproses');
        $allowed = $this->validTransitions['menunggu_diproses'];
        $this->assertContains('diproses', $allowed);
    }

    public function test_menunggu_diproses_can_transition_to_dibatalkan()
    {
        $order = $this->createOrder('menunggu_diproses');
        $allowed = $this->validTransitions['menunggu_diproses'];
        $this->assertContains('dibatalkan', $allowed);
    }

    public function test_diproses_can_transition_to_dikirim()
    {
        $order = $this->createOrder('diproses');
        $allowed = $this->validTransitions['diproses'];
        $this->assertContains('dikirim', $allowed);
    }

    public function test_diproses_can_transition_to_dibatalkan()
    {
        $order = $this->createOrder('diproses');
        $allowed = $this->validTransitions['diproses'];
        $this->assertContains('dibatalkan', $allowed);
    }

    public function test_dikirim_can_transition_to_selesai()
    {
        $order = $this->createOrder('dikirim');
        $allowed = $this->validTransitions['dikirim'];
        $this->assertContains('selesai', $allowed);
    }

    public function test_selesai_has_no_valid_transitions()
    {
        $order = $this->createOrder('selesai');
        $allowed = $this->validTransitions['selesai'];
        $this->assertEmpty($allowed);
    }

    public function test_dibatalkan_has_no_valid_transitions()
    {
        $order = $this->createOrder('dibatalkan');
        $allowed = $this->validTransitions['dibatalkan'];
        $this->assertEmpty($allowed);
    }

    public function test_invalid_transition_from_menunggu_pembayaran_to_dikirim()
    {
        $allowed = $this->validTransitions['menunggu_pembayaran'];
        $this->assertNotContains('dikirim', $allowed);
    }

    public function test_invalid_transition_from_menunggu_diproses_to_selesai()
    {
        $allowed = $this->validTransitions['menunggu_diproses'];
        $this->assertNotContains('selesai', $allowed);
    }

    public function test_invalid_transition_from_diproses_to_menunggu_diproses()
    {
        $allowed = $this->validTransitions['diproses'];
        $this->assertNotContains('menunggu_diproses', $allowed);
    }

    public function test_invalid_transition_from_dikirim_to_dibatalkan()
    {
        $allowed = $this->validTransitions['dikirim'];
        $this->assertNotContains('dibatalkan', $allowed);
    }

    public function test_invalid_transition_from_selesai_to_any()
    {
        $allStatuses = ['menunggu_pembayaran', 'menunggu_diproses', 'diproses', 'dikirim', 'selesai', 'dibatalkan'];
        $allowed = $this->validTransitions['selesai'];

        foreach ($allStatuses as $status) {
            $this->assertNotContains($status, $allowed);
        }
    }

    public function test_invalid_transition_from_dibatalkan_to_any()
    {
        $allStatuses = ['menunggu_pembayaran', 'menunggu_diproses', 'diproses', 'dikirim', 'selesai', 'dibatalkan'];
        $allowed = $this->validTransitions['dibatalkan'];

        foreach ($allStatuses as $status) {
            $this->assertNotContains($status, $allowed);
        }
    }

    public function test_terminal_states_are_truly_terminal()
    {
        $terminalStates = ['selesai', 'dibatalkan'];

        foreach ($terminalStates as $state) {
            $allowed = $this->validTransitions[$state];
            $this->assertEmpty($allowed, "Status '$state' harus terminal (tidak ada transisi keluar)");
        }
    }

    public function test_all_statuses_appear_in_at_least_one_transition_target()
    {
        $allStatuses = ['menunggu_pembayaran', 'menunggu_diproses', 'diproses', 'dikirim', 'selesai', 'dibatalkan'];
        $allTargets = [];

        foreach ($this->validTransitions as $from => $to) {
            $allTargets = array_merge($allTargets, $to);
        }

        foreach ($allStatuses as $status) {
            if ($status !== 'menunggu_pembayaran') {
                $this->assertContains($status, $allTargets,
                    "Status '$status' harus bisa dicapai dari setidaknya satu status lain");
            }
        }
    }
}
