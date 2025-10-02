<?php

namespace Database\Seeders;

use App\Models\Exercise;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExerciseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $exercise = [
            [
                'name' => 'Lari',
                'calories_burned_per_minute' => 5,
                'jenis' => 'Lari',
                'video_url' => 'https://www.youtube.com/watch?v=9bZkp7q19f0',
                'description' => 'Lari merupakan olahraga yang dapat membakar kalori dengan cepat.',
                'is_active' => true
            ],
            [
                'name' => 'Jalan Kaki',
                'calories_burned_per_minute' => 3,
                'jenis' => 'Jalan Kaki',
                'video_url' => 'https://www.youtube.com/watch?v=9bZkp7q19f0',
                'description' => 'Jalan kaki merupakan olahraga yang dapat membakar kalori dengan cepat.',
                'is_active' => true
            ],
            [
                'name' => 'Sepeda',
                'calories_burned_per_minute' => 2,
                'jenis' => 'Sepeda',
                'video_url' => 'https://www.youtube.com/watch?v=9bZkp7q19f0',
                'description' => 'Sepeda merupakan olahraga yang dapat membakar kalori dengan cepat.',
                'is_active' => true
            ]
        ];

        foreach ($exercise as $item) {
            Exercise::create($item);
        }
    }
}
