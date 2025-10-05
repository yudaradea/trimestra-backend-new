<?php

namespace Database\Seeders;

use App\Models\Allergy;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AllergySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $alergy = [
            [
                'name' => 'Laktase',
                'description' => 'alergi laktase'
            ],
            [
                'name' => 'Gluten',
            ],
            [
                'name' => 'Kacang',
            ],
            [
                'name' => 'Susu',
            ],
            [
                'name' => 'Kacang',
            ]
        ];

        foreach ($alergy as $alergy) {
            Allergy::create($alergy);
        }
    }
}
