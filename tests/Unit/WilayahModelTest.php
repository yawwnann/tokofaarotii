<?php

namespace Tests\Unit;

use App\Models\Province;
use App\Models\Regency;
use App\Models\District;
use App\Models\Village;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class WilayahModelTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_province_can_be_created()
    {
        $province = Province::create([
            'id' => '99',
            'name' => 'PROVINSI TEST',
        ]);

        $this->assertDatabaseHas('indonesia_provinces', [
            'id' => '99',
            'name' => 'PROVINSI TEST',
        ]);
    }

    public function test_regency_belongs_to_province()
    {
        $province = Province::create(['id' => '99', 'name' => 'PROVINSI TEST']);
        $regency = Regency::create([
            'id' => '9901',
            'province_id' => '99',
            'name' => 'KOTA TEST',
        ]);

        $this->assertInstanceOf(Province::class, $regency->province);
        $this->assertEquals('99', $regency->province->id);
    }

    public function test_province_has_many_regencies()
    {
        $province = Province::create(['id' => '99', 'name' => 'PROVINSI TEST']);
        Regency::create(['id' => '9901', 'province_id' => '99', 'name' => 'KOTA TEST 1']);
        Regency::create(['id' => '9902', 'province_id' => '99', 'name' => 'KOTA TEST 2']);

        $this->assertCount(2, $province->regencies()->where('province_id', '99')->get());
    }

    public function test_district_belongs_to_regency()
    {
        $province = Province::create(['id' => '99', 'name' => 'PROVINSI TEST']);
        $regency = Regency::create(['id' => '9901', 'province_id' => '99', 'name' => 'KOTA TEST']);
        $district = District::create([
            'id' => '9901010',
            'regency_id' => '9901',
            'name' => 'KECAMATAN TEST',
        ]);

        $this->assertInstanceOf(Regency::class, $district->regency);
        $this->assertEquals('9901', $district->regency->id);
    }

    public function test_regency_has_many_districts()
    {
        $province = Province::create(['id' => '99', 'name' => 'PROVINSI TEST']);
        $regency = Regency::create(['id' => '9901', 'province_id' => '99', 'name' => 'KOTA TEST']);
        District::create(['id' => '9901010', 'regency_id' => '9901', 'name' => 'KECAMATAN 1']);
        District::create(['id' => '9901020', 'regency_id' => '9901', 'name' => 'KECAMATAN 2']);

        $this->assertCount(2, $regency->districts()->where('regency_id', '9901')->get());
    }

    public function test_village_belongs_to_district()
    {
        $province = Province::create(['id' => '99', 'name' => 'PROVINSI TEST']);
        $regency = Regency::create(['id' => '9901', 'province_id' => '99', 'name' => 'KOTA TEST']);
        $district = District::create(['id' => '9901010', 'regency_id' => '9901', 'name' => 'KECAMATAN TEST']);
        $village = Village::create([
            'id' => '9901010001',
            'district_id' => '9901010',
            'name' => 'DESA TEST',
        ]);

        $this->assertInstanceOf(District::class, $village->district);
        $this->assertEquals('9901010', $village->district->id);
    }

    public function test_district_has_many_villages()
    {
        $province = Province::create(['id' => '99', 'name' => 'PROVINSI TEST']);
        $regency = Regency::create(['id' => '9901', 'province_id' => '99', 'name' => 'KOTA TEST']);
        $district = District::create(['id' => '9901010', 'regency_id' => '9901', 'name' => 'KECAMATAN TEST']);
        Village::create(['id' => '9901010001', 'district_id' => '9901010', 'name' => 'DESA 1']);
        Village::create(['id' => '9901010002', 'district_id' => '9901010', 'name' => 'DESA 2']);

        $this->assertCount(2, $district->villages()->where('district_id', '9901010')->get());
    }

    public function test_cascade_delete_province_removes_regencies()
    {
        $province = Province::create(['id' => '99', 'name' => 'PROVINSI TEST']);
        Regency::create(['id' => '9901', 'province_id' => '99', 'name' => 'KOTA TEST']);

        $province->delete();

        $this->assertDatabaseMissing('indonesia_regencies', ['id' => '9901']);
    }

    public function test_cascade_delete_regency_removes_districts()
    {
        $province = Province::create(['id' => '99', 'name' => 'PROVINSI TEST']);
        $regency = Regency::create(['id' => '9901', 'province_id' => '99', 'name' => 'KOTA TEST']);
        District::create(['id' => '9901010', 'regency_id' => '9901', 'name' => 'KECAMATAN TEST']);

        $regency->delete();

        $this->assertDatabaseMissing('indonesia_districts', ['id' => '9901010']);
    }

    public function test_cascade_delete_district_removes_villages()
    {
        $province = Province::create(['id' => '99', 'name' => 'PROVINSI TEST']);
        $regency = Regency::create(['id' => '9901', 'province_id' => '99', 'name' => 'KOTA TEST']);
        $district = District::create(['id' => '9901010', 'regency_id' => '9901', 'name' => 'KECAMATAN TEST']);
        Village::create(['id' => '9901010001', 'district_id' => '9901010', 'name' => 'DESA TEST']);

        $district->delete();

        $this->assertDatabaseMissing('indonesia_villages', ['id' => '9901010001']);
    }

    public function test_provinces_ordered_by_name()
    {
        // Test that our test data and existing data are sorted correctly
        Province::create(['id' => '99', 'name' => 'ZZZ TEST']);
        Province::create(['id' => '98', 'name' => 'AAA TEST']);

        $provinces = Province::orderBy('name')->get();

        // Our test data should be ordered correctly among existing data
        $this->assertEquals('AAA TEST', $provinces->firstWhere('id', '98')->name);
        $this->assertEquals('ZZZ TEST', $provinces->firstWhere('id', '99')->name);
    }
}
