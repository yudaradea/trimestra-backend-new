<?php

use App\Http\Controllers\AllergyController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DiaryController;
use App\Http\Controllers\Exercisecontroller;
use App\Http\Controllers\ExerciseLogController;
use App\Http\Controllers\FoodCategoryController;
use App\Http\Controllers\FoodController;
use App\Http\Controllers\FoodDiaryController;
use App\Http\Controllers\FoodDiaryItemController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\NutritionRequirementController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserExerciseController;
use App\Http\Controllers\UserFoodController;
use App\Http\Controllers\WeightLogController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/provinces', [LocationController::class, 'provinces']);
Route::get('/regencies', [LocationController::class, 'regencies']);
Route::get('/districts', [LocationController::class, 'districts']);
Route::get('/villages', [LocationController::class, 'villages']);

// sanctum route
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);

    // user
    Route::apiResource('user', UserController::class);

    // Profile
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::post('/profile', [ProfileController::class, 'store']);
    Route::post('/profile/update', [ProfileController::class, 'update']);
    Route::put('/profile', [ProfileController::class, 'update']);

    // Weight logs
    Route::apiResource('weight-logs', WeightLogController::class);

    // nutrition requirement
    Route::apiResource('nutrition-requirement', NutritionRequirementController::class);

    // food category
    Route::apiResource('food-category', FoodCategoryController::class);

    // food
    Route::apiResource('food', FoodController::class);
    Route::get('/food/all/paginated', [FoodController::class, 'getAllPaginated']);

    // user food
    Route::apiResource('user-food', UserFoodController::class);
    Route::get('/user-food/all/paginated', [UserFoodController::class, 'getAllPaginated']);

    // food diary item
    Route::apiResource('food-diary-item', FoodDiaryItemController::class);

    // food diary
    Route::apiResource('food-diary', FoodDiaryController::class);
    Route::get('/food-diary/all/paginated', [FoodDiaryController::class, 'getAllPaginated']);
    // foodrecomended
    Route::get('/food-recomended', [FoodController::class, 'getRecomendedFoods']);

    // exercise
    Route::apiResource('exercise', Exercisecontroller::class);
    Route::get('/exercise/all/paginated', [Exercisecontroller::class, 'getAllPaginated']);

    // user exercise
    Route::apiResource('user-exercise', UserExerciseController::class);
    Route::get('/user-exercise/all/paginated', [UserExerciseController::class, 'getAllPaginated']);

    // exerciseLog
    Route::apiResource('exercise-log', ExerciseLogController::class);
    Route::get('/exercise-log/all/paginated', [ExerciseLogController::class, 'getAllPaginated']);

    // Diary
    Route::get('/diary', [DiaryController::class, 'index']);

    // alergy
    Route::apiResource('allergy', AllergyController::class);
});
