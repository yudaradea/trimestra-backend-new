<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\FoodDiary\StoreUpdateRequest;
use App\Http\Resources\FoodDiaryResource;
use App\Http\Resources\PaginateResource;
use App\Interfaces\FoodDiaryRepositoryInterface;
use App\Models\ExerciseLog;
use App\Models\FoodDiary;
use App\Models\NutritionTarget;
use App\Services\NotificationService;
use App\Services\NutritionCalculationService;
use App\Services\NutritionService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FoodDiaryController extends Controller
{
    private FoodDiaryRepositoryInterface $foodDiaryRepository;
    private NutritionCalculationService $nutritionCalculationService;
    private NotificationService $notificationService;

    public function __construct(FoodDiaryRepositoryInterface $foodDiaryRepository, NutritionCalculationService $nutritionCalculationService, NotificationService $notificationService)
    {
        $this->foodDiaryRepository = $foodDiaryRepository;
        $this->nutritionCalculationService = $nutritionCalculationService;
        $this->notificationService = $notificationService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return ResponseHelper::jsonResponse(false, 'Unauthorized', null, 401);
        }



        try {
            $foodDiaries = $this->foodDiaryRepository->getAll(
                $user->id,
                $request->search,
                $request->limit ?? 10,
                $request->date ?? now()->toDateString(), // kasih default
                true
            );

            return ResponseHelper::jsonResponse(
                true,
                'Data Food Diary berhasil diambil',
                FoodDiaryResource::collection($foodDiaries),
                200
            );
        } catch (\Throwable $e) {
            Log::error('FoodDiary index error', [
                'user_id' => $user->id ?? null,
                'error'   => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return ResponseHelper::jsonResponse(false, 'Internal Server Error', null, 500);
        }
    }

    public function getAllPaginated(Request $request)
    {
        $user = $request->user();
        $request->validate([
            'search' => 'nullable|string',
            'row_per_page' => 'required|integer'
        ]);
        try {
            $foodDiaries = $this->foodDiaryRepository->getAllPaginated(
                $user->id,
                $request->search,
                $request->row_per_page,
                $request->date ?? now()->toDateString()
            );
            return ResponseHelper::jsonResponse(true, 'Data Food Diary berhasil diambil', PaginateResource::make($foodDiaries, FoodDiaryResource::class), 200);
        } catch (Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUpdateRequest $request)
    {
        $user = $request->user();
        $request = $request->validated();
        $date = $request['date'] ?? now()->toDateString();

        try {
            $foodDiary = $this->foodDiaryRepository->create($request);

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

            // percentage
            $caloriesPercentage = ($nutritionTarget && $nutritionTarget->calories > 0)
                ? ($summary['calories_balance'] / $nutritionTarget->calories) * 100 : 0;
            $proteinPercentage = ($nutritionTarget && $nutritionTarget->protein > 0)
                ? ($summary['protein_intake'] / $nutritionTarget->protein) * 100 : 0;
            $carbohydratesPercentage = ($nutritionTarget && $nutritionTarget->carbohydrates > 0)
                ? ($summary['carbohydrates_intake'] / $nutritionTarget->carbohydrates) * 100 : 0;
            $fatPercentage = ($nutritionTarget && $nutritionTarget->fat > 0)
                ? ($summary['fat_intake'] / $nutritionTarget->fat) * 100 : 0;

            $percentage = [
                'calories' => round($caloriesPercentage, 2),
                'protein' => round($proteinPercentage, 2),
                'carbohydrates' => round($carbohydratesPercentage, 2),
                'fat' => round($fatPercentage, 2),
            ];

            $this->notificationService->checkDailyAchievement($user, $percentage);

            return ResponseHelper::jsonResponse(true, 'Food Diary Berhasil Dibuat', FoodDiaryResource::make($foodDiary), 200);
        } catch (Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id, Request $request)
    {
        $user = $request->user();
        try {
            $foodDiary = $this->foodDiaryRepository->getById($id, $user->id);

            if (!$foodDiary) {
                return ResponseHelper::jsonResponse(false, 'Data Food Diary tidak ditemukan', null, 404);
            }

            return ResponseHelper::jsonResponse(true, 'Data Food Diary berhasil diambil', FoodDiaryResource::make($foodDiary), 200);
        } catch (Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreUpdateRequest $request, string $id)
    {
        $user = $request->user();
        $request = $request->validated();
        $date = $request['date'] ?? now()->toDateString();

        try {
            $foodDiary = $this->foodDiaryRepository->getById($id, $user->id);

            if (!$foodDiary) {
                return ResponseHelper::jsonResponse(false, 'Data Food Diary tidak ditemukan', null, 404);
            }

            $foodDiary = $this->foodDiaryRepository->update($id, $request, $user->id);

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

            // percentage
            $caloriesPercentage = ($nutritionTarget && $nutritionTarget->calories > 0)
                ? ($summary['calories_balance'] / $nutritionTarget->calories) * 100 : 0;
            $proteinPercentage = ($nutritionTarget && $nutritionTarget->protein > 0)
                ? ($summary['protein_intake'] / $nutritionTarget->protein) * 100 : 0;
            $carbohydratesPercentage = ($nutritionTarget && $nutritionTarget->carbohydrates > 0)
                ? ($summary['carbohydrates_intake'] / $nutritionTarget->carbohydrates) * 100 : 0;
            $fatPercentage = ($nutritionTarget && $nutritionTarget->fat > 0)
                ? ($summary['fat_intake'] / $nutritionTarget->fat) * 100 : 0;

            $percentage = [
                'calories' => round($caloriesPercentage, 2),
                'protein' => round($proteinPercentage, 2),
                'carbohydrates' => round($carbohydratesPercentage, 2),
                'fat' => round($fatPercentage, 2),
            ];

            $this->notificationService->checkDailyAchievement($user, $percentage);

            return ResponseHelper::jsonResponse(true, 'Food Diary Berhasil Diupdate', FoodDiaryResource::make($foodDiary), 200);
        } catch (Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id, Request $request)
    {
        $user = $request->user();
        try {
            $foodDiary = $this->foodDiaryRepository->getById($id, $user->id);

            if (!$foodDiary) {
                return ResponseHelper::jsonResponse(false, 'Data Food Diary tidak ditemukan', null, 404);
            }

            $this->foodDiaryRepository->delete($id, $user->id);

            return ResponseHelper::jsonResponse(true, 'Food Diary Berhasil Dihapus', null, 200);
        } catch (Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }
}
