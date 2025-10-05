<?php

namespace App\Services;

use App\Models\NutritionRequirement;
use App\Models\NutritionTarget;
use App\Models\Profile;
use App\Models\WeightTarget;

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

    public function calculateTargetWeight($profile)
    {
        if ($profile->is_pregnant) {
            $initialWeight = $profile->initial_weight ?? $profile->weight;
            $weeks = $profile->weeks ?? 0;
            $gain = 0;

            if ($weeks <= 13) {
                // Trimester 1
                $gain = 1.75 * ($weeks / 13); // proporsional dari total 1.75 kg
            } elseif ($weeks <= 27) {
                // Trimester 2
                $gain = 1.75 + (0.375 * ($weeks - 13)); // lanjut dari trimester 1
            } elseif ($weeks <= 40) {
                // Trimester 3
                $gain = 1.75 + (0.375 * 14) + (0.25 * ($weeks - 27));
            } else {
                // jika lebih dari 40 minggu, anggap maksimum
                $gain = 1.75 + (0.375 * 14) + (0.25 * 13);
            }

            $expectedWeight = $initialWeight + $gain;
            return round($expectedWeight, 1);
        }

        // === Jalur untuk non-hamil ===
        if ($profile->height) {
            $heightM = $profile->height / 100;
            $targetWeight = 21.7 * ($heightM ** 2); // BMI ideal rata-rata 21.7
            return round($targetWeight, 1);
        }

        return null;
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

        if ($profile->weight && $profile->height) {
            $expectedWeight = $this->calculateTargetWeight($profile);

            if ($expectedWeight) {
                WeightTarget::updateOrCreate(
                    [
                        'user_id' => $profile->user_id,
                        'week' => $profile->is_pregnant ? $profile->weeks : null,
                    ],
                    [
                        'expected_weight' => $expectedWeight,
                    ]
                );
            }
        }

        return $profile;
    }
}
