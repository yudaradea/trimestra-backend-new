<?php

namespace App\Services;

use App\Models\NutritionRequirement;
use App\Models\NutritionTarget;
use App\Models\Profile;

class NutritionService
{
    public function calculateBMI($height, $weight)
    {
        $height = $height / 100;

        return round($weight / ($height * $height), 2);
    }

    public function getBMICategory($bmi)
    {
        if ($bmi < 18.5) {
            return 'Underweight';
        } elseif ($bmi >= 18.5 && $bmi <= 24.9) {
            return 'Normal';
        } elseif ($bmi >= 25 && $bmi <= 29.9) {
            return 'Overweight';
        } else {
            return 'Obese';
        }
    }

    public function calculateTrimester($weeks)
    {
        if ($weeks >= 1 && $weeks <= 13) {
            return 1;
        } elseif ($weeks >= 14 && $weeks <= 27) {
            return 2;
        } elseif ($weeks >= 28 && $weeks <= 42) {
            return 3;
        }
    }

    public function getNutritionRequirements($bmiCategory, $isPregnant, $trimester = null)
    {
        $query = NutritionRequirement::where('bmi_category', $bmiCategory)
            ->where('is_pregnant', $isPregnant);

        if ($isPregnant && $trimester) {
            $query->where('trimester', $trimester);
        } else {
            $query->whereNull('trimester');
        }

        return $query->first();
    }

    public function updateProfileAndNutrition($id)
    {
        $profile = Profile::where('user_id', $id)->first();

        if (!$profile) {
            throw new \Exception('Profile Tidak Ditemukan');
        }


        // hitung BMI dan update profile
        if ($profile->height && $profile->weight) {
            $bmi = $this->calculateBMI($profile->height, $profile->weight);
            $profile->bmi = $bmi;
            $profile->bmi_category = $this->getBMICategory($bmi);
            $profile->save();
        }

        // hitung trimester
        if ($profile->is_pregnant && $profile->weeks) {
            $trimester = $this->calculateTrimester($profile->weeks);
            $profile->trimester = $trimester;
            $profile->save();
        }

        // tentukan kategori IMT dan trimester
        $bmiCategory = $this->getBMICategory($profile->bmi);
        $trimester = $profile->is_pregnant ? $this->calculateTrimester($profile->weeks) : null;

        // hitung kebutuhan nutrisi
        $nutritionRequirements = $this->getNutritionRequirements($bmiCategory, $profile->is_pregnant, $trimester);

        if ($nutritionRequirements) {
            // buat atau update nutrition target hari ini
            NutritionTarget::updateOrCreate(
                [
                    'user_id' => $profile->user_id,
                    'date' => now()->toDateString(),
                ],
                [
                    'calories' => $nutritionRequirements->calories,
                    'protein' => $nutritionRequirements->protein,
                    'carbohydrates' => $nutritionRequirements->carbohydrates,
                    'fat' => $nutritionRequirements->fat
                ]

            );
        }

        return $profile;
    }
}
