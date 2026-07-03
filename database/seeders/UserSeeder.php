<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Akun Admin Master
        User::updateOrCreate(
            ['email' => 'admin@faa.com'],
            [
                'name' => 'Admin Master Toko FAA',
                'password' => Hash::make('password123'),
                'role' => 'admin_master',
            ]
        );

        // 2. Akun Pemilik Toko
        User::updateOrCreate(
            ['email' => 'pemilik@faa.com'],
            [
                'name' => 'Pemilik Toko FAA',
                'password' => Hash::make('password123'),
                'role' => 'pemilik',
            ]
        );

        // 3. Akun Kasir
        User::updateOrCreate(
            ['email' => 'kasir@faa.com'],
            [
                'name' => 'Kasir Toko FAA',
                'password' => Hash::make('password123'),
                'role' => 'kasir',
            ]
        );

        // 4. Akun Customer / Pelanggan Umum
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
