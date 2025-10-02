<?php

namespace Database\Seeders;

use App\Models\FoodCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FoodCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // buat 10 kategori makanan
        $foodCategories = [
            [
                'name' => 'Makanan Berat',
                // fake image icon 
                'icon' => 'https://source.unsplash.com/200x200/?food',
            ],
            [
                'name' => 'Lauk Pauk',
                // fake image icon 
                'icon' => 'https://source.unsplash.com/200x200/?food',
            ],
            [
                'name' => 'Sayuran',
                // fake image icon
                'icon' => 'https://source.unsplash.com/200x200/?food',
            ],
            [
                'name' => 'buah',
                // fake image icon buah
                'icon' => 'https://source.unsplash.com/200x200/?food',
            ],
            [
                'name' => 'pelengkap',
                // fake image icon pelengkap
                'icon' => 'https://source.unsplash.com/200x200/?food',
            ]
        ];

        foreach ($foodCategories as $foodCategory) {
            FoodCategory::create($foodCategory);
        }
    }
}
