<?php

namespace Database\Seeders;

use App\Models\NutritionRequirement;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NutritionRequirementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $requirements = [
            // ===================================================================
            // Ibu Hamil Trimester 1
            // Kebutuhan mirip non-hamil, sedikit peningkatan pada protein.
            // ===================================================================
            ['bmi_category' => 'Underweight', 'is_pregnant' => true, 'trimester' => 1, 'calories' => 2380, 'carbohydrates' => 327, 'fat' => 79, 'protein' => 90],
            ['bmi_category' => 'Normal', 'is_pregnant' => true, 'trimester' => 1, 'calories' => 2180, 'carbohydrates' => 299, 'fat' => 73, 'protein' => 80],
            ['bmi_category' => 'Overweight', 'is_pregnant' => true, 'trimester' => 1, 'calories' => 1980, 'carbohydrates' => 272, 'fat' => 66, 'protein' => 75],
            ['bmi_category' => 'Obese', 'is_pregnant' => true, 'trimester' => 1, 'calories' => 1780, 'carbohydrates' => 245, 'fat' => 59, 'protein' => 70],

            // ===================================================================
            // Ibu Hamil Trimester 2
            // Tambahan sekitar 300 kkal dari baseline. Peningkatan protein signifikan.
            // ===================================================================
            ['bmi_category' => 'Underweight', 'is_pregnant' => true, 'trimester' => 2, 'calories' => 2500, 'carbohydrates' => 344, 'fat' => 83, 'protein' => 95],
            ['bmi_category' => 'Normal', 'is_pregnant' => true, 'trimester' => 2, 'calories' => 2300, 'carbohydrates' => 316, 'fat' => 77, 'protein' => 85],
            ['bmi_category' => 'Overweight', 'is_pregnant' => true, 'trimester' => 2, 'calories' => 2100, 'carbohydrates' => 289, 'fat' => 70, 'protein' => 80],
            ['bmi_category' => 'Obese', 'is_pregnant' => true, 'trimester' => 2, 'calories' => 1900, 'carbohydrates' => 261, 'fat' => 63, 'protein' => 75],

            // ===================================================================
            // Ibu Hamil Trimester 3
            // Tambahan sekitar 450-500 kkal dari baseline. Kebutuhan protein tertinggi.
            // ===================================================================
            ['bmi_category' => 'Underweight', 'is_pregnant' => true, 'trimester' => 3, 'calories' => 2700, 'carbohydrates' => 371, 'fat' => 90, 'protein' => 100],
            ['bmi_category' => 'Normal', 'is_pregnant' => true, 'trimester' => 3, 'calories' => 2500, 'carbohydrates' => 344, 'fat' => 83, 'protein' => 95],
            ['bmi_category' => 'Overweight', 'is_pregnant' => true, 'trimester' => 3, 'calories' => 2300, 'carbohydrates' => 316, 'fat' => 77, 'protein' => 90],
            ['bmi_category' => 'Obese', 'is_pregnant' => true, 'trimester' => 3, 'calories' => 2100, 'carbohydrates' => 289, 'fat' => 70, 'protein' => 85],

            // ===================================================================
            // Non-Hamil (Data Anda sudah cukup baik)
            // ===================================================================
            ['bmi_category' => 'Underweight', 'is_pregnant' => false, 'trimester' => null, 'calories' => 2200, 'carbohydrates' => 300, 'fat' => 73, 'protein' => 85],
            ['bmi_category' => 'Normal', 'is_pregnant' => false, 'trimester' => null, 'calories' => 2000, 'carbohydrates' => 275, 'fat' => 67, 'protein' => 75],
            ['bmi_category' => 'Overweight', 'is_pregnant' => false, 'trimester' => null, 'calories' => 1800, 'carbohydrates' => 248, 'fat' => 60, 'protein' => 65],
            ['bmi_category' => 'Obese', 'is_pregnant' => false, 'trimester' => null, 'calories' => 1600, 'carbohydrates' => 220, 'fat' => 53, 'protein' => 60],
        ];


        foreach ($requirements as $requirement) {
            NutritionRequirement::create($requirement);
        }
    }
}
