<?php

namespace Database\Seeders;

use App\Models\Province;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;


class ProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // ✅ CEK APAKAH DATA SUDAH ADA
        if (Province::count() > 0) {
            $this->command->info('Provinces already seeded. Skipping...');
            return;
        }

        $this->command->info('Seeding provinces...');

        // ✅ BACA CSV FILE
        $csvPath = database_path('csv/provinces.csv');

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
                'name' => trim($row[1]),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $count++;

            // ✅ BATCH INSERT SETIAP 1000 ROWS
            if (count($batch) >= 1000) {
                Province::insert($batch);
                $this->command->info("Inserted {$count} provinces...");
                $batch = [];
            }
        }

        // ✅ INSERT SISA BATCH
        if (!empty($batch)) {
            Province::insert($batch);
        }

        fclose($csv);

        $this->command->info("✅ Successfully seeded {$count} provinces!");
    }
}
