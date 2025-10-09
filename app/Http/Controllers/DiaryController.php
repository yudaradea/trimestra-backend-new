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
use Carbon\Carbon;
use Carbon\CarbonPeriod;
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

        $nutritionTarget = NutritionTarget::where('user_id', $user->id)
            ->where('date', $date)
            ->first();

        if (!$nutritionTarget) {
            $nutritionTarget = $this->nutritionCalculationService
                ->createNutritionTargetForDate($user->id, $date);
        }

        // Pastikan summary selalu array dengan default
        $summary = $this->nutritionCalculationService->calculateDailySummary($user->id, $date) ?? [
            'calories_balance' => 0,
            'protein_intake' => 0,
            'carbohydrates_intake' => 0,
            'fat_intake' => 0,
        ];
        $exerciseLog = ExerciseLog::where('user_id', $user->id)
            ->where('date', $date)
            ->with(['exercise', 'userExercise'])
            ->get();

        $foodDiary = FoodDiary::where('user_id', $user->id)
            ->where('date', $date)
            ->with(['foodDiaryItem', 'foodDiaryItem.food', 'foodDiaryItem.userFood'])
            ->get();

        // hitung diff dengan aman
        $caloriesDiff = ($nutritionTarget->calories ?? 0) - ($summary['calories_balance'] ?? 0);
        $proteinDiff = ($nutritionTarget->protein ?? 0) - ($summary['protein_intake'] ?? 0);
        $carbohydratesDiff = ($nutritionTarget->carbohydrates ?? 0) - ($summary['carbohydrates_intake'] ?? 0);
        $fatDiff = ($nutritionTarget->fat ?? 0) - ($summary['fat_intake'] ?? 0);

        // percentage
        $caloriesPercentage = ($nutritionTarget && $nutritionTarget->calories > 0)
            ? ($summary['calories_balance'] / $nutritionTarget->calories) * 100 : 0;
        $proteinPercentage = ($nutritionTarget && $nutritionTarget->protein > 0)
            ? ($summary['protein_intake'] / $nutritionTarget->protein) * 100 : 0;
        $carbohydratesPercentage = ($nutritionTarget && $nutritionTarget->carbohydrates > 0)
            ? ($summary['carbohydrates_intake'] / $nutritionTarget->carbohydrates) * 100 : 0;
        $fatPercentage = ($nutritionTarget && $nutritionTarget->fat > 0)
            ? ($summary['fat_intake'] / $nutritionTarget->fat) * 100 : 0;

        app(\App\Services\NotificationService::class)->checkDailyAchievement($user, (object)[
            'summary' => $summary,
            'target' => $nutritionTarget,
        ]);

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
            'percentage' => [
                'calories' => round($caloriesPercentage, 2),
                'protein' => round($proteinPercentage, 2),
                'carbohydrates' => round($carbohydratesPercentage, 2),
                'fat' => round($fatPercentage, 2),
            ],
            'food_diary' => FoodDiaryResource::collection($foodDiary), // isi query kalau perlu

            'exercise_log' => ExerciseLogResource::collection($exerciseLog),

            'weight_log' => null,
            'weight_logs_for_chart' => [],
        ]);
    }

    public function nutritionReport(Request $request)
    {
        $user = $request->user();
        $start = Carbon::parse($request->query('start_date'));
        $end = Carbon::parse($request->query('end_date'));

        $days = CarbonPeriod::create($start, $end);
        $report = [];

        foreach ($days as $day) {
            $date = $day->toDateString();

            $nutritionTarget = NutritionTarget::where('user_id', $user->id)
                ->where('date', $date)
                ->first();

            if (!$nutritionTarget) {
                $nutritionTarget = app(NutritionCalculationService::class)
                    ->createNutritionTargetForDate($user->id, $date);
            }

            $summary = app(NutritionCalculationService::class)
                ->calculateDailySummary($user->id, $date) ?? [
                    'calories_balance' => 0,
                    'protein_intake' => 0,
                    'carbohydrates_intake' => 0,
                    'fat_intake' => 0,
                ];

            $percentage = [
                'calories' => $nutritionTarget && $nutritionTarget->calories > 0
                    ? round($summary['calories_balance'] / $nutritionTarget->calories * 100, 2)
                    : 0,
                'protein' => $nutritionTarget && $nutritionTarget->protein > 0
                    ? round($summary['protein_intake'] / $nutritionTarget->protein * 100, 2)
                    : 0,
                'carbohydrates' => $nutritionTarget && $nutritionTarget->carbohydrates > 0
                    ? round($summary['carbohydrates_intake'] / $nutritionTarget->carbohydrates * 100, 2)
                    : 0,
                'fat' => $nutritionTarget && $nutritionTarget->fat > 0
                    ? round($summary['fat_intake'] / $nutritionTarget->fat * 100, 2)
                    : 0,
            ];

            $report[] = [
                'date' => $date,
                'summary' => $summary,
                'target' => [
                    'calories' => $nutritionTarget->calories ?? 0,
                    'protein' => $nutritionTarget->protein ?? 0,
                    'carbohydrates' => $nutritionTarget->carbohydrates ?? 0,
                    'fat' => $nutritionTarget->fat ?? 0,
                ],
                'percentage' => $percentage,
            ];
        }

        return response()->json($report);
    }
}
