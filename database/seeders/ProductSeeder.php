<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Bersihkan tabel untuk menghindari duplikasi
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Product::truncate();
        Category::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 2. Buat Kategori dan simpan ID-nya
        $frozen = Category::create([
            'name' => 'Frozen',
            // 'slug' => 'frozen'
        ]);

        $bakery = Category::create([
            'name' => 'Bakery',
            // 'slug' => 'bakery'
        ]);

        // 3. Daftar Produk
        $products = [
            // Produk Frozen
            ['cat_id' => $frozen->id, 'name' => 'BAKSO AYAM A 1/2 KG', 'price' => 30000, 'unit' => '1/2 KG', 'sku' => 'BAA-05'],
            ['cat_id' => $frozen->id, 'name' => 'BAKSO AYAM A 1 KG', 'price' => 60000, 'unit' => 'KG', 'sku' => 'BAA-01'],
            ['cat_id' => $frozen->id, 'name' => 'BAKSO AYAM B 1/2 KG', 'price' => 25000, 'unit' => '1/2 KG', 'sku' => 'BAB-05'],
            ['cat_id' => $frozen->id, 'name' => 'BAKSO AYAM B 1 KG', 'price' => 50000, 'unit' => 'KG', 'sku' => 'BAB-01'],
            ['cat_id' => $frozen->id, 'name' => 'NUGGET IKAN 17 STIK', 'price' => 12000, 'unit' => '17 STIK', 'sku' => 'NI-17'],
            ['cat_id' => $frozen->id, 'name' => 'NUGGET AYAM 200 GR', 'price' => 12000, 'unit' => '200 GR', 'sku' => 'NA-200'],
            ['cat_id' => $frozen->id, 'name' => 'BAKSO SAPI + AYAM GRADE A 1/2 KG', 'price' => 40000, 'unit' => '1/2 KG', 'sku' => 'BSAA-05'],
            ['cat_id' => $frozen->id, 'name' => 'BAKSO SAPI + AYAM GRADE A 1 KG', 'price' => 80000, 'unit' => 'KG', 'sku' => 'BSAA-01'],
            ['cat_id' => $frozen->id, 'name' => 'BAKSO SAPI + AYAM GRADE B 1/2 KG', 'price' => 30000, 'unit' => '1/2 KG', 'sku' => 'BSAB-05'],
            ['cat_id' => $frozen->id, 'name' => 'BAKSO SAPI + AYAM GRADE B 1 KG', 'price' => 60000, 'unit' => 'KG', 'sku' => 'BSAB-01'],
            ['cat_id' => $frozen->id, 'name' => 'SOSIS AYAM 15 STIK', 'price' => 16000, 'unit' => '15 STIK', 'sku' => 'SA-15'],
            ['cat_id' => $frozen->id, 'name' => 'SEMPOL 10 STIK', 'price' => 13000, 'unit' => '10 STIK', 'sku' => 'SMP-10'],
            ['cat_id' => $frozen->id, 'name' => 'BAKSO TAHU IKAN 1/2 KG', 'price' => 15000, 'unit' => '1/2 KG', 'sku' => 'BTI-05'],
            ['cat_id' => $frozen->id, 'name' => 'BAKSO TAHU AYAM 1/2 KG', 'price' => 25000, 'unit' => '1/2 KG', 'sku' => 'BTA-05'],
            ['cat_id' => $frozen->id, 'name' => 'BAKSO AYAM ISI TELUR PUYUH', 'price' => 20000, 'unit' => '13 BH', 'sku' => 'BATP-13'],
            ['cat_id' => $frozen->id, 'name' => 'BAKSO AYAM ISI DAGING 1/2 KG', 'price' => 30000, 'unit' => '1/2 KG', 'sku' => 'BAID-05'],
            ['cat_id' => $frozen->id, 'name' => 'BAKSO SAPI 200 GR', 'price' => 18000, 'unit' => '200 GR', 'sku' => 'BS-200'],
            ['cat_id' => $frozen->id, 'name' => 'BAKSO AYAM 170 GR', 'price' => 10000, 'unit' => '170 GR', 'sku' => 'BA-170'],
            ['cat_id' => $frozen->id, 'name' => 'SOSIS AYAM 8 STIK', 'price' => 8000, 'unit' => '8 STIK', 'sku' => 'SA-08'],

            // Produk Bakery
            ['cat_id' => $bakery->id, 'name' => 'ROTI ALL VARIAN', 'price' => 5000, 'unit' => 'PCS', 'sku' => 'R-VAR'],
            ['cat_id' => $bakery->id, 'name' => 'ROTI TAWAR', 'price' => 8000, 'unit' => 'PCS', 'sku' => 'RT-REG'],
            ['cat_id' => $bakery->id, 'name' => 'ROTI TAWAR BAKAR', 'price' => 8000, 'unit' => 'PCS', 'sku' => 'RT-BKR'],
            ['cat_id' => $bakery->id, 'name' => 'ROTI BURGER ISI 4', 'price' => 6000, 'unit' => 'ISI 4', 'sku' => 'RB-I4'],
            ['cat_id' => $bakery->id, 'name' => 'ROTI UNYIL', 'price' => 1000, 'unit' => 'PCS', 'sku' => 'RU-01'],
        ];s

        // 4. Masukkan data ke Database
        foreach ($products as $product) {
            Product::create([
                'category_id' => $product['cat_id'],
                'sku'         => $product['sku'],
                'name'        => $product['name'],
                'price'       => $product['price'],
                'unit'        => $product['unit'],
                'description' => 'Produk ' . $product['name'],
            ]);
        }
    }
}