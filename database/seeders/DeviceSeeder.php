<?php

namespace Database\Seeders;

use App\Models\Device;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DeviceSeeder extends Seeder
{
    public function run()
    {
        $path = database_path('csv/device_list.csv');

        if (!file_exists($path)) {
            $this->command->error("CSV file not found at: {$path}");
            return;
        }

        $file = fopen($path, 'r');
        $header = fgetcsv($file); // baca header: device_code, device_name

        while (($row = fgetcsv($file)) !== false) {
            $data = array_combine($header, $row);

            Device::updateOrCreate(
                ['device_code' => $data['device_code']], // unik berdasarkan kode
                ['device_name' => $data['device_name']]
            );
        }

        fclose($file);

        $this->command->info('Devices seeded from CSV successfully!');
    }
}
