<?php

namespace Database\Seeders;

use App\Models\Food;
use App\Models\FoodCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FoodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // buat food seeder dengan kategori makanan berat
        $foodMakananBerat = [
            [
                'name' => 'Nasi Putih',
                'description' => 'Nasi putih',
                'image' => 'https://source.unsplash.com/200x200/?food',
                'calories' => '129',
                'carbohydrates' => '27.9',
                'fat' => '0.28',
                'protein' => '2.66',
                'ukuran_satuan' => '100',
                'ukuran_satuan_nama' => 'gram',
                'is_active' => true
            ],
            [
                'name' => 'Nasi Merah',
                'description' => 'Nasi Merah Begizi',
                'image' => 'https://source.unsplash.com/200x200/?food',
                'calories' => '110',
                'carbohydrates' => '22.78',
                'fat' => '0.89',
                'protein' => '2.56',
                'ukuran_satuan' => '100',
                'ukuran_satuan_nama' => 'gram',
                'is_active' => true
            ],
            [
                'name' => 'Kentang Rebus',
                'description' => 'Kentang Rebus',
                'image' => 'https://source.unsplash.com/200x200/?food',
                'calories' => '87',
                'carbohydrates' => '20.13',
                'fat' => '0.1',
                'protein' => '1.87',
                'ukuran_satuan' => '100',
                'ukuran_satuan_nama' => 'gram',
                'is_active' => true
            ]
        ];

        // memasukan food makanan berat ke id kategori makanan berat
        foreach ($foodMakananBerat as $food) {
            $food['food_category_id'] = FoodCategory::where('name', 'Makanan Berat')->first()->id;
            Food::create($food);
        }

        $foodLaukPauk = [
            [
                'name' => 'Dada Ayam Goreng (kulit)',
                'description' => '',
                'image' => 'https://source.unsplash.com/200x200/?food',
                'calories' => '216',
                'carbohydrates' => '0',
                'fat' => '9.1',
                'protein' => '31.67',
                'ukuran_satuan' => '100',
                'ukuran_satuan_nama' => 'gram',
                'is_active' => true
            ],
            [
                'name' => 'Daging Ayam Goreng (tanpa kulit)',
                'description' => '',
                'image' => 'https://source.unsplash.com/200x200/?food',
                'calories' => '184',
                'carbohydrates' => '0',
                'fat' => '4.68',
                'protein' => '33.34',
                'ukuran_satuan' => '100',
                'ukuran_satuan_nama' => 'gram',
                'is_active' => true
            ]
        ];

        // memasukan food lauk pauk ke id kategori lauk pauk
        foreach ($foodLaukPauk as $food) {
            $food['food_category_id'] = FoodCategory::where('name', 'Lauk Pauk')->first()->id;
            Food::create($food);
        }
    }
}
