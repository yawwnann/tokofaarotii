<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AddressCustomerTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_user_can_view_their_addresses()
    {
        $user = User::factory()->create();
        UserAddress::factory()->create(['user_id' => $user->id, 'label' => 'Rumahku']);

        $response = $this->actingAs($user)->get('/alamat-saya');

        $response->assertStatus(200);
        $response->assertSee('Rumahku');
    }

    public function test_user_can_store_new_address()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/alamat-saya', [
            'label' => 'Kantor',
            'receiver_name' => 'John Doe',
            'phone' => '08123456789',
            'province' => 'DKI Jakarta',
            'city' => 'Jakarta Selatan',
            'district' => 'Tebet',
            'postal_code' => '12810',
            'address' => 'Jl. Sudirman No 1',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('user_addresses', [
            'user_id' => $user->id,
            'label' => 'Kantor',
            'is_default' => 1, // first address should be default
        ]);
    }

    public function test_user_can_update_address()
    {
        $user = User::factory()->create();
        $address = UserAddress::factory()->create(['user_id' => $user->id, 'label' => 'Old Label']);

        $response = $this->actingAs($user)->put("/alamat-saya/{$address->id}", [
            'label' => 'New Label',
            'receiver_name' => 'John Doe',
            'phone' => '08123456789',
            'province' => 'DKI Jakarta',
            'city' => 'Jakarta Selatan',
            'district' => 'Tebet',
            'postal_code' => '12810',
            'address' => 'Jl. Sudirman No 1',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('user_addresses', [
            'id' => $address->id,
            'label' => 'New Label',
        ]);
    }

    public function test_user_can_delete_address()
    {
        $user = User::factory()->create();
        $address = UserAddress::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->delete("/alamat-saya/{$address->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('user_addresses', [
            'id' => $address->id,
        ]);
    }

    public function test_user_can_set_default_address()
    {
        $user = User::factory()->create();
        $address1 = UserAddress::factory()->create(['user_id' => $user->id, 'is_default' => true]);
        $address2 = UserAddress::factory()->create(['user_id' => $user->id, 'is_default' => false]);

        $response = $this->actingAs($user)->post("/alamat-saya/{$address2->id}/default");

        $response->assertRedirect();
        
        $this->assertDatabaseHas('user_addresses', [
            'id' => $address1->id,
            'is_default' => 0,
        ]);
        
        $this->assertDatabaseHas('user_addresses', [
            'id' => $address2->id,
            'is_default' => 1,
        ]);
    }
}
