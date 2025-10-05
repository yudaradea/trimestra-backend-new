<?php

namespace App\Services;

use App\Models\ExerciseLog;
use App\Models\FoodDiaryItem;
use App\Models\NutritionRequirement;
use App\Models\NutritionTarget;
use App\Models\User;

class NutritionCalculationService
{
    public function calculateDailySummary($userId, $date)
    {
        // hitung dari food diary
        $foodNutrition = $this->calculateFoodNutrition($userId, $date);

        // hitung dari exercise
        $exerciseCalories = $this->calculateExerciseCalories($userId, $date);

        return [
            'calories_intake' => $foodNutrition['calories'],
            'calories_burned' => $exerciseCalories,
            'calories_balance' => $foodNutrition['calories'] - $exerciseCalories,
            'protein_intake' => $foodNutrition['protein'],
            'carbohydrates_intake' => $foodNutrition['carbohydrates'],
            'fat_intake' => $foodNutrition['fat']
        ];
    }

    private function calculateFoodNutrition($userId, $date)
    {
        $items = FoodDiaryItem::whereHas('foodDiary', function ($query) use ($userId, $date) {
            $query->where('user_id', $userId)->where('date', $date);
        })
            ->with(['food', 'userFood'])
            ->get();

        $totalCalories = 0;
        $totalProtein = 0;
        $totalCarbohydrates = 0;
        $totalFat = 0;

        foreach ($items as $item) {
            $food = $item->food ?? $item->userFood;

            if ($food) {
                // hitung berdasarkan jumlah satuan
                // $facator = $item->quantity / $food->ukuran_satuan;

                $totalCalories += $food->calories * $item->quantity;
                $totalProtein += $food->protein * $item->quantity;
                $totalCarbohydrates += $food->carbohydrates * $item->quantity;
                $totalFat += $food->fat * $item->quantity;
            }
        }
        return [
            'calories' => round($totalCalories, 2),
            'protein' => round($totalProtein, 2),
            'carbohydrates' => round($totalCarbohydrates, 2),
            'fat' => round($totalFat, 2)
        ];
    }

    private function calculateExerciseCalories($userId, $date)
    {
        return ExerciseLog::where('user_id', $userId)->where('date', $date)->sum('calories_burned');
    }

    public function createNutritionTargetForDate($userId, $date)
    {
        $user = User::with('profile')->findOrFail($userId);
        $profile = $user->profile;

        if (!$profile) {
            throw new \Exception('Profile Tidak Ditemukan');
        }

        $nutrition =  $this->getNutritionRequirements($profile->bmi_category, $profile->is_pregnant, $profile->trimester);

        return NutritionTarget::updateOrCreate(
            [
                'user_id' => $userId,
                'date' => $date
            ],
            [
                'calories' => $nutrition->calories,
                'protein' => $nutrition->protein,
                'carbohydrates' => $nutrition->carbohydrates,
                'fat' => $nutrition->fat
            ]
        );

        return null;
    }

    private function getNutritionRequirements($bmiCategory, $isPregnant, $trimester = null)
    {
        $query = NutritionRequirement::where('bmi_category', $bmiCategory)->where('is_pregnant', $isPregnant);

        if ($isPregnant && $trimester) {
            $query->where('trimester', $trimester);
        } else {
            $query->whereNull('trimester');
        }

        return $query->first();
    }
}
