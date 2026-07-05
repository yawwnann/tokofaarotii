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
        // Panggil ProductSeeder yang berisi logika pembuatan kategori dan produk lengkap
        $this->call([
            ProductSeeder::class,
            UserSeeder::class,
            WilayahSeeder::class,
            DummyDataSeeder::class,
        ]);
    }
}