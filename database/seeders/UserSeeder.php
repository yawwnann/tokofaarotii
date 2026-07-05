<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Store;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil referensi toko (StoreSeeder harus sudah jalan sebelumnya)
        $pusatId = Store::where('slug', 'toko-faa-pusat')->value('id');
        $cabangId = Store::where('slug', 'toko-faa-cabang-sungailiat')->value('id');

        // 1. Akun Admin Master (tidak terikat toko)
        User::updateOrCreate(
            ['email' => 'admin@faa.com'],
            [
                'name' => 'Admin Master Toko FAA',
                'password' => Hash::make('password123'),
                'role' => 'admin_master',
            ]
        );

        // 2. Akun Pemilik Toko (terikat ke toko pusat)
        User::updateOrCreate(
            ['email' => 'pemilik@faa.com'],
            [
                'name' => 'Pemilik Toko FAA',
                'password' => Hash::make('password123'),
                'role' => 'pemilik',
                'store_id' => $pusatId,
            ]
        );

        // 3. Akun Kasir (terikat ke toko cabang)
        User::updateOrCreate(
            ['email' => 'kasir@faa.com'],
            [
                'name' => 'Kasir Toko FAA',
                'password' => Hash::make('password123'),
                'role' => 'kasir',
                'store_id' => $cabangId,
            ]
        );

        // 4. Akun Customer / Pelanggan Umum (tidak terikat toko)
        User::updateOrCreate(
            ['email' => 'customer@faa.com'],
            [
                'name' => 'Pelanggan Toko FAA',
                'password' => Hash::make('password123'),
                'role' => 'customer',
            ]
        );
    }
}
