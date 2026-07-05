<?php

namespace Tests\Feature;

use App\Models\Province;
use App\Models\Regency;
use App\Models\District;
use App\Models\Village;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class WilayahApiTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_get_provinces_returns_provinces_as_json()
    {
        $user = User::factory()->create();

        Province::create(['id' => '99', 'name' => 'PROVINSI TEST A']);
        Province::create(['id' => '98', 'name' => 'PROVINSI TEST B']);

        $response = $this->actingAs($user)->getJson('/api/wilayah/provinces');

        $response->assertStatus(200);
        $response->assertJsonFragment(['id' => '99', 'name' => 'PROVINSI TEST A']);
        $response->assertJsonFragment(['id' => '98', 'name' => 'PROVINSI TEST B']);
    }

    public function test_get_provinces_returns_ordered_by_name()
    {
        $user = User::factory()->create();

        Province::create(['id' => '99', 'name' => 'ZZZ TEST']);
        Province::create(['id' => '98', 'name' => 'AAA TEST']);

        $response = $this->actingAs($user)->getJson('/api/wilayah/provinces');

        $response->assertStatus(200);
        $data = $response->json();
        
        // Find our test items to verify ordering
        $zzzIndex = collect($data)->search(fn($item) => $item['id'] === '99');
        $aaaIndex = collect($data)->search(fn($item) => $item['id'] === '98');
        
        $this->assertNotFalse($zzzIndex);
        $this->assertNotFalse($aaaIndex);
        $this->assertLessThan($zzzIndex, $aaaIndex); // AAA should come before ZZZ
    }

    public function test_get_regencies_returns_regencies_for_province()
    {
        $user = User::factory()->create();

        $province = Province::create(['id' => '99', 'name' => 'TEST PROVINCE']);
        Regency::create(['id' => '9901', 'province_id' => '99', 'name' => 'KOTA A']);
        Regency::create(['id' => '9902', 'province_id' => '99', 'name' => 'KOTA B']);

        $response = $this->actingAs($user)->getJson('/api/wilayah/regencies/99');

        $response->assertStatus(200);
        $response->assertJsonFragment(['id' => '9901', 'name' => 'KOTA A']);
        $response->assertJsonFragment(['id' => '9902', 'name' => 'KOTA B']);
    }

    public function test_get_regencies_excludes_other_provinces()
    {
        $user = User::factory()->create();

        $province1 = Province::create(['id' => '99', 'name' => 'PROVINSI 1']);
        $province2 = Province::create(['id' => '98', 'name' => 'PROVINSI 2']);
        Regency::create(['id' => '9901', 'province_id' => '99', 'name' => 'KOTA A']);
        Regency::create(['id' => '9801', 'province_id' => '98', 'name' => 'KOTA B']);

        $response = $this->actingAs($user)->getJson('/api/wilayah/regencies/99');

        $response->assertStatus(200);
        $response->assertJsonFragment(['id' => '9901', 'name' => 'KOTA A']);
        $response->assertJsonMissing(['id' => '9801']);
    }

    public function test_get_districts_returns_districts_for_regency()
    {
        $user = User::factory()->create();

        $province = Province::create(['id' => '99', 'name' => 'TEST']);
        $regency = Regency::create(['id' => '9901', 'province_id' => '99', 'name' => 'KOTA A']);
        District::create(['id' => '9901010', 'regency_id' => '9901', 'name' => 'KECAMATAN A']);
        District::create(['id' => '9901020', 'regency_id' => '9901', 'name' => 'KECAMATAN B']);

        $response = $this->actingAs($user)->getJson('/api/wilayah/districts/9901');

        $response->assertStatus(200);
        $response->assertJsonFragment(['id' => '9901010', 'name' => 'KECAMATAN A']);
    }

    public function test_get_villages_returns_villages_for_district()
    {
        $user = User::factory()->create();

        $province = Province::create(['id' => '99', 'name' => 'TEST']);
        $regency = Regency::create(['id' => '9901', 'province_id' => '99', 'name' => 'KOTA A']);
        $district = District::create(['id' => '9901010', 'regency_id' => '9901', 'name' => 'KECAMATAN A']);
        Village::create(['id' => '9901010001', 'district_id' => '9901010', 'name' => 'DESA A']);
        Village::create(['id' => '9901010002', 'district_id' => '9901010', 'name' => 'DESA B']);

        $response = $this->actingAs($user)->getJson('/api/wilayah/villages/9901010');

        $response->assertStatus(200);
        $response->assertJsonFragment(['id' => '9901010001', 'name' => 'DESA A']);
    }

    public function test_unauthenticated_user_cannot_access_wilayah_api()
    {
        $response = $this->getJson('/api/wilayah/provinces');
        // JSON API requests return 401 for unauthenticated
        $response->assertUnauthorized();
    }
}
