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
            // Ibu Hamil Trimester 1
            ['bmi_category' => 'Underweight', 'is_pregnant' => true, 'trimester' => 1, 'calories' => 2000, 'carbohydrates' => 30, 'fat' => 2.5, 'protein' => 2],
            ['bmi_category' => 'Normal', 'is_pregnant' => true, 'trimester' => 1, 'calories' => 1800, 'carbohydrates' => 25, 'fat' => 2.3, 'protein' => 1],
            ['bmi_category' => 'Overweight', 'is_pregnant' => true, 'trimester' => 1, 'calories' => 1700, 'carbohydrates' => 22, 'fat' => 2.0, 'protein' => 1],
            ['bmi_category' => 'Obese', 'is_pregnant' => true, 'trimester' => 1, 'calories' => 1600, 'carbohydrates' => 20, 'fat' => 1.8, 'protein' => 1],

            // Ibu Hamil Trimester 2
            ['bmi_category' => 'Underweight', 'is_pregnant' => true, 'trimester' => 2, 'calories' => 3200, 'carbohydrates' => 45, 'fat' => 2.5, 'protein' => 12],
            ['bmi_category' => 'Normal', 'is_pregnant' => true, 'trimester' => 2, 'calories' => 3000, 'carbohydrates' => 40, 'fat' => 2.3, 'protein' => 10],
            ['bmi_category' => 'Overweight', 'is_pregnant' => true, 'trimester' => 2, 'calories' => 2800, 'carbohydrates' => 35, 'fat' => 2.0, 'protein' => 12],
            ['bmi_category' => 'Obese', 'is_pregnant' => true, 'trimester' => 2, 'calories' => 2600, 'carbohydrates' => 30, 'fat' => 1.8, 'protein' => 15],

            // Ibu Hamil Trimester 3
            ['bmi_category' => 'Underweight', 'is_pregnant' => true, 'trimester' => 3, 'calories' => 3200, 'carbohydrates' => 45, 'fat' => 2.5, 'protein' => 35],
            ['bmi_category' => 'Normal', 'is_pregnant' => true, 'trimester' => 3, 'calories' => 3000, 'carbohydrates' => 40, 'fat' => 2.3, 'protein' => 30],
            ['bmi_category' => 'Overweight', 'is_pregnant' => true, 'trimester' => 3, 'calories' => 2800, 'carbohydrates' => 35, 'fat' => 2.0, 'protein' => 32],
            ['bmi_category' => 'Obese', 'is_pregnant' => true, 'trimester' => 3, 'calories' => 2600, 'carbohydrates' => 30, 'fat' => 1.8, 'protein' => 34],

            // Non-Hamil
            ['bmi_category' => 'Underweight', 'is_pregnant' => false, 'trimester' => null, 'calories' => 2200, 'carbohydrates' => 280, 'fat' => 70, 'protein' => 60],
            ['bmi_category' => 'Normal', 'is_pregnant' => false, 'trimester' => null, 'calories' => 2000, 'carbohydrates' => 250, 'fat' => 67, 'protein' => 50],
            ['bmi_category' => 'Overweight', 'is_pregnant' => false, 'trimester' => null, 'calories' => 1800, 'carbohydrates' => 220, 'fat' => 60, 'protein' => 55],
            ['bmi_category' => 'Obese', 'is_pregnant' => false, 'trimester' => null, 'calories' => 1600, 'carbohydrates' => 200, 'fat' => 55, 'protein' => 60],
        ];


        foreach ($requirements as $requirement) {
            NutritionRequirement::create($requirement);
        }
    }
}
