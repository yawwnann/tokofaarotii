<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class WilayahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Importing wilayah data from CSV files...');

        // 1. Provinsi
        $this->command->info('Importing provinces...');
        $csvPath = database_path('data-wilayah/provinces.csv');
        if (!File::exists($csvPath)) {
            $this->command->error('File provinces.csv not found!');
            return;
        }
        $this->importCsv($csvPath, 'indonesia_provinces', ['id', 'name']);

        // 2. Kabupaten/Kota
        $this->command->info('Importing regencies...');
        $csvPath = database_path('data-wilayah/regencies.csv');
        if (!File::exists($csvPath)) {
            $this->command->error('File regencies.csv not found!');
            return;
        }
        $this->importCsv($csvPath, 'indonesia_regencies', ['id', 'province_id', 'name']);

        // 3. Kecamatan
        $this->command->info('Importing districts...');
        $csvPath = database_path('data-wilayah/districts.csv');
        if (!File::exists($csvPath)) {
            $this->command->error('File districts.csv not found!');
            return;
        }
        $this->importCsv($csvPath, 'indonesia_districts', ['id', 'regency_id', 'name']);

        // 4. Desa/Kelurahan
        $this->command->info('Importing villages...');
        $csvPath = database_path('data-wilayah/villages.csv');
        if (!File::exists($csvPath)) {
            $this->command->error('File villages.csv not found!');
            return;
        }
        $this->importCsv($csvPath, 'indonesia_villages', ['id', 'district_id', 'name']);

        $this->command->info('All wilayah data imported successfully!');
    }

    /**
     * Import a CSV file into a database table.
     */
    private function importCsv(string $csvPath, string $table, array $columns): void
    {
        $file = File::get($csvPath);
        $lines = explode("\n", trim($file));

        $chunks = array_chunk($lines, 500);
        $total = count($lines);
        $inserted = 0;

        DB::disableQueryLog();
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        foreach ($chunks as $chunkIndex => $chunk) {
            $records = [];
            foreach ($chunk as $line) {
                $line = trim($line);
                if (empty($line)) continue;

                $data = str_getcsv($line);
                if (count($data) < count($columns)) continue;

                $record = [];
                foreach ($columns as $i => $col) {
                    $record[$col] = $data[$i] ?? '';
                }
                $records[] = $record;
                $inserted++;
            }

            if (!empty($records)) {
                DB::table($table)->insertOrIgnore($records);
            }

            $progress = min(100, round(($chunkIndex + 1) / count($chunks) * 100));
            if ($chunkIndex % 5 === 0) {
                $this->command->info("  Progress: {$progress}% ({$inserted}/{$total} records)");
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        DB::enableQueryLog();

        $this->command->info("  Done: {$inserted} records imported into {$table}");
    }
}
