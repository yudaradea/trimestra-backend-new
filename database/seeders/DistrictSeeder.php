<?php

namespace Database\Seeders;

use App\Models\District;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class DistrictSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // ✅ CEK APAKAH DATA SUDAH ADA
        if (District::count() > 0) {
            $this->command->info('Districts already seeded. Skipping...');
            return;
        }

        $this->command->info('Seeding districts...');

        // ✅ BACA CSV FILE
        $csvPath = database_path('csv/districts.csv');

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
                'regency_id' => $row[1],
                'name' => trim($row[2]),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $count++;

            // ✅ BATCH INSERT SETIAP 1000 ROWS
            if (count($batch) >= 1000) {
                District::insert($batch);
                $this->command->info("Inserted {$count} districts...");
                $batch = [];
            }
        }

        // ✅ INSERT SISA BATCH
        if (!empty($batch)) {
            District::insert($batch);
        }

        fclose($csv);

        $this->command->info("✅ Successfully seeded {$count} districts!");
    }
}
