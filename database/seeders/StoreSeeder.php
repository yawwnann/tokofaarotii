<?php

namespace Database\Seeders;

use App\Models\Store;
use Illuminate\Database\Seeder;

class StoreSeeder extends Seeder
{
    public function run(): void
    {
        Store::firstOrCreate(
            ['slug' => 'toko-faa-pusat'],
            [
                'name' => 'Toko FAA Pusat',
                'district_id' => '1901091',
                'address' => 'Pemali, Bangka',
                'phone' => '08123456789',
                'email' => 'pusat@tokofaa.com',
                'is_active' => true,
            ]
        );

        Store::firstOrCreate(
            ['slug' => 'toko-faa-cabang-sungailiat'],
            [
                'name' => 'Toko FAA Cabang Sungailiat',
                'district_id' => '1901090',
                'address' => 'Sungailiat, Bangka',
                'phone' => '08123456790',
                'email' => 'cabang@tokofaa.com',
                'is_active' => true,
            ]
        );
    }
}
