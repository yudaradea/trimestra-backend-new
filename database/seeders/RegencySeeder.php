<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Regency;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class RegencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // ✅ CEK APAKAH DATA SUDAH ADA
        if (Regency::count() > 0) {
            $this->command->info('Regencies already seeded. Skipping...');
            return;
        }

        $this->command->info('Seeding regencies...');

        // ✅ BACA CSV FILE
        $csvPath = database_path('csv/regencies.csv');

        if (!File::exists($csvPath)) {
            $this->command->error('CSV file not found: ' . $csvPath);
            return;
        }

        $csv = fopen($csvPath, 'r');

        // ✅ SKIP HEADER ROW
        fgetcsv($csv);

        $batch = [];
        $count = 0;

        while (($row = fgetcsv($csv)) !== FALSE) {
            $batch[] = [
                'id' => $row[0],
                'province_id' => $row[1],
                'name' => trim($row[2]),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $count++;

            // ✅ BATCH INSERT SETIAP 1000 ROWS
            if (count($batch) >= 1000) {
                Regency::insert($batch);
                $this->command->info("Inserted {$count} regencies...");
                $batch = [];
            }
        }

        // ✅ INSERT SISA BATCH
        if (!empty($batch)) {
            Regency::insert($batch);
        }

        fclose($csv);

        $this->command->info("✅ Successfully seeded {$count} regencies!");
    }
}
