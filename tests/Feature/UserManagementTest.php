<?php

namespace Tests\Feature;

use App\Models\District;
use App\Models\Province;
use App\Models\Regency;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use DatabaseTransactions;

    private User $admin;
    private Store $store;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

        $this->admin = User::factory()->create(['role' => 'admin_master']);

        Province::create(['id' => '99', 'name' => 'PROVINSI TEST']);
        Regency::create(['id' => '9901', 'province_id' => '99', 'name' => 'KABUPATEN TEST']);
        District::create(['id' => '9901010', 'regency_id' => '9901', 'name' => 'KECAMATAN TEST']);

        $this->store = Store::create([
            'name' => 'Toko Test',
            'slug' => 'toko-test-' . uniqid(),
            'district_id' => '9901010',
            'address' => 'Test Address',
            'is_active' => true,
        ]);
    }

    public function test_admin_can_access_user_management_page()
    {
        $response = $this->actingAs($this->admin)->get(route('kelola-user.index'));
        $response->assertStatus(200);
    }

    public function test_kasir_cannot_access_user_management_page()
    {
        $kasir = User::factory()->create(['role' => 'kasir']);

        $response = $this->actingAs($kasir)->get(route('kelola-user.index'));
        $response->assertStatus(403);
    }

    public function test_admin_can_create_admin_master_user()
    {
        $response = $this->actingAs($this->admin)->post(route('kelola-user.store'), [
            'name' => 'New Admin',
            'email' => 'newadmin@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'admin_master',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('users', [
            'name' => 'New Admin',
            'email' => 'newadmin@test.com',
            'role' => 'admin_master',
            'store_id' => null,
        ]);
    }

    public function test_admin_can_create_pemilik_user()
    {
        $response = $this->actingAs($this->admin)->post(route('kelola-user.store'), [
            'name' => 'Pemilik Toko',
            'email' => 'pemilik@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'pemilik',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('users', [
            'name' => 'Pemilik Toko',
            'email' => 'pemilik@test.com',
            'role' => 'pemilik',
            'store_id' => null,
        ]);
    }

    public function test_admin_can_create_kasir_user_with_store()
    {
        $response = $this->actingAs($this->admin)->post(route('kelola-user.store'), [
            'name' => 'Kasir Baru',
            'email' => 'kasir@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'kasir',
            'store_id' => $this->store->id,
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('users', [
            'name' => 'Kasir Baru',
            'email' => 'kasir@test.com',
            'role' => 'kasir',
            'store_id' => $this->store->id,
        ]);
    }

    public function test_kasir_user_requires_store_id()
    {
        $response = $this->actingAs($this->admin)->post(route('kelola-user.store'), [
            'name' => 'Kasir Tanpa Toko',
            'email' => 'kasirnotoko@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'kasir',
        ]);

        $response->assertSessionHas('error');
        $this->assertStringContainsString('Store ID wajib', session('error'));
    }

    public function test_cannot_create_user_with_invalid_role()
    {
        $response = $this->actingAs($this->admin)->post(route('kelola-user.store'), [
            'name' => 'Invalid Role',
            'email' => 'invalid@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'pegawai',
        ]);

        $response->assertSessionHasErrors('role');
    }

    public function test_admin_can_update_user_role()
    {
        $user = User::factory()->create(['role' => 'pelanggan']);

        $response = $this->actingAs($this->admin)->put(route('kelola-user.update', $user), [
            'name' => $user->name,
            'email' => $user->email,
            'role' => 'kasir',
            'store_id' => $this->store->id,
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'role' => 'kasir',
            'store_id' => $this->store->id,
        ]);
    }

    public function test_admin_can_delete_user()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($this->admin)->delete(route('kelola-user.destroy', $user));
        $response->assertRedirect();

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_admin_cannot_delete_self()
    {
        $response = $this->actingAs($this->admin)->delete(route('kelola-user.destroy', $this->admin));
        $response->assertRedirect();

        $this->assertDatabaseHas('users', ['id' => $this->admin->id]);
    }

    public function test_store_id_is_null_for_non_kasir_roles_on_create()
    {
        $roles = ['admin_master', 'pemilik'];

        foreach ($roles as $role) {
            $email = "test_{$role}@test.com";
            $this->actingAs($this->admin)->post(route('kelola-user.store'), [
                'name' => "User $role",
                'email' => $email,
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'role' => $role,
                'store_id' => $this->store->id,
            ]);

            $this->assertDatabaseHas('users', [
                'email' => $email,
                'role' => $role,
                'store_id' => null,
            ]);
        }
    }

    public function test_email_must_be_unique()
    {
        User::factory()->create(['email' => 'duplicate@test.com']);

        $response = $this->actingAs($this->admin)->post(route('kelola-user.store'), [
            'name' => 'Duplicate Email',
            'email' => 'duplicate@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'pemilik',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_password_confirmation_is_required()
    {
        $response = $this->actingAs($this->admin)->post(route('kelola-user.store'), [
            'name' => 'No Confirm',
            'email' => 'noconfirm@test.com',
            'password' => 'password123',
            'password_confirmation' => 'different',
            'role' => 'pemilik',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_index_page_shows_all_users()
    {
        User::factory()->create(['name' => 'User A', 'role' => 'pelanggan']);
        User::factory()->create(['name' => 'User B', 'role' => 'kasir']);

        $response = $this->actingAs($this->admin)->get(route('kelola-user.index'));

        $response->assertStatus(200);
        $response->assertSee('User A');
        $response->assertSee('User B');
    }

    public function test_email_change_does_not_conflict_with_own_email_on_update()
    {
        $user = User::factory()->create([
            'email' => 'myemail@test.com',
            'role' => 'pelanggan',
        ]);

        $response = $this->actingAs($this->admin)->put(route('kelola-user.update', $user), [
            'name' => $user->name,
            'email' => 'myemail@test.com',
            'role' => 'pelanggan',
        ]);

        $response->assertRedirect();
    }
}
