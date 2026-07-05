<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Panggil seeder secara berurutan — Store harus ada sebelum User & Product
        // Urutan penting: Wilayah → Store → Produk → User → Data dummy
        $this->call([
            WilayahSeeder::class,      // 1. Data provinsi/kabupaten/kecamatan
            StoreSeeder::class,         // 2. Toko (membutuhkan district_id dari wilayah)
            ProductSeeder::class,       // 3. Produk (membutuhkan store_id)
            UserSeeder::class,          // 4. User (membutuhkan store_id)
            DummyDataSeeder::class,     // 5. Data dummy (membutuhkan produk & user)
        ]);
    }
}