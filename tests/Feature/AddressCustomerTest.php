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
        // Only disable CSRF middleware so auth/session still work
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);
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
            'province_id' => '31',
            'province' => 'DKI JAKARTA',
            'city_id' => '3174',
            'city' => 'KOTA JAKARTA BARAT',
            'district_id' => '3174040',
            'district' => 'KEBON JERUK',
            'village_id' => '3174040001',
            'village' => 'KEBON JERUK',
            'postal_code' => '11530',
            'address' => 'Jl. Sudirman No 1',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('user_addresses', [
            'user_id' => $user->id,
            'label' => 'Kantor',
            'province_id' => '31',
            'city_id' => '3174',
            'district_id' => '3174040',
            'village_id' => '3174040001',
            'is_default' => 1,
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
            'province_id' => '31',
            'province' => 'DKI JAKARTA',
            'city_id' => '3174',
            'city' => 'KOTA JAKARTA BARAT',
            'district_id' => '3174040',
            'district' => 'KEBON JERUK',
            'postal_code' => '11530',
            'address' => 'Jl. Sudirman No 1',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('user_addresses', [
            'id' => $address->id,
            'label' => 'New Label',
            'province_id' => '31',
            'city_id' => '3174',
            'district_id' => '3174040',
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

    public function test_user_cannot_access_others_address()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $address = UserAddress::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user)->delete("/alamat-saya/{$address->id}");
        $response->assertStatus(403);
    }
}
