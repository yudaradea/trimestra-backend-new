<?php

namespace App\Http\Controllers;

use App\Http\Resources\ExerciseLogResource;
use App\Http\Resources\FoodDiaryResource;
use App\Http\Resources\NutritionTargetResource;
use App\Http\Resources\WeightLogResource;
use App\Models\ExerciseLog;
use App\Models\FoodDiary;
use App\Models\NutritionTarget;
use App\Models\WeightLog;
use App\Services\NutritionCalculationService;
use Illuminate\Http\Request;

use function Pest\Laravel\json;

class DiaryController extends Controller
{
    private $nutritionCalculationService;

    public function __construct(NutritionCalculationService $nutritionCalculationService)
    {
        $this->nutritionCalculationService = $nutritionCalculationService;
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $date = $request->date ?? now()->toDateString();

        // ambil atau buat nutrition target untuk tanggal ini
        $nutritionTarget = NutritionTarget::where('user_id', $user->id)->where('date', $date)->first();


        if (!$nutritionTarget) {
            $nutritionTarget = $this->nutritionCalculationService->createNutritionTargetForDate($user->id, $date);
        }


        // hitung summary harian
        $summary = $this->nutritionCalculationService->calculateDailySummary($user->id, $date);

        // ambil data food diary
        $foodDiary = FoodDiary::where('user_id', $user->id)
            ->where('date', $date)
            ->with('foodDiaryItem.food', 'foodDiaryItem.userFood')
            ->get();

        // ambil exercise log
        $exerciseLog = ExerciseLog::where('user_id', $user->id)
            ->where('date', $date)
            ->with(['exercise', 'userExercise'])
            ->get();

        // ambil weightLog
        $weightLog = WeightLog::where('user_id', $user->id)
            ->where('date', '<=', $date)
            ->orderBy('created_at', 'desc')
            ->first();

        // ambil beberapa weight log untuk grafik
        $weightLogsForChart = WeightLog::where('user_id', $user->id)
            ->where('date', '<=', $date)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // differen nutrition target and summary
        $caloriesDiff = ($nutritionTarget ? $nutritionTarget->calories : 0) - $summary['calories_balance'];
        $proteinDiff = ($nutritionTarget ? $nutritionTarget->protein : 0) - $summary['protein_intake'];
        $carbohydratesDiff = ($nutritionTarget ? $nutritionTarget->carbohydrates : 0) - $summary['carbohydrates_intake'];
        $fatDiff = ($nutritionTarget ? $nutritionTarget->fat : 0) - $summary['fat_intake'];

        // percentage nutrition target and summary - dengan pengecekan null
        $caloriesPercentage = 0;
        $proteinPercentage = 0;
        $carbohydratesPercentage = 0;
        $fatPercentage = 0;

        if ($nutritionTarget) {
            if ($nutritionTarget->calories > 0) {
                $caloriesPercentage = ($summary['calories_balance'] / $nutritionTarget->calories) * 100;
            }
            if ($nutritionTarget->protein > 0) {
                $proteinPercentage = ($summary['protein_intake'] / $nutritionTarget->protein) * 100;
            }
            if ($nutritionTarget->carbohydrates > 0) {
                $carbohydratesPercentage = ($summary['carbohydrates_intake'] / $nutritionTarget->carbohydrates) * 100;
            }
            if ($nutritionTarget->fat > 0) {
                $fatPercentage = ($summary['fat_intake'] / $nutritionTarget->fat) * 100;
            }
        }

        // tampilkan pada json
        return response()->json([
            'date' => $date,
            'target' => $nutritionTarget ? new NutritionTargetResource($nutritionTarget) : null,
            'summary' => $summary,
            'diff' => [
                'calories' => round($caloriesDiff, 2),
                'protein' => round($proteinDiff, 2),
                'carbohydrates' => round($carbohydratesDiff, 2),
                'fat' => round($fatDiff, 2),
            ],
            // persentase selisih nutrition target dan summary
            'percentage' => [
                'calories' => round($caloriesPercentage, 2),
                'protein' => round($proteinPercentage, 2),
                'carbohydrates' => round($carbohydratesPercentage, 2),
                'fat' => round($fatPercentage, 2),
            ],
            'food_diary' => FoodDiaryResource::collection($foodDiary),
            'exercise_log' => ExerciseLogResource::collection($exerciseLog),
            'weight_log' => $weightLog ? new WeightLogResource($weightLog) : null,
            'weight_logs_for_chart' => WeightLogResource::collection($weightLogsForChart),
        ]);
    }
}
