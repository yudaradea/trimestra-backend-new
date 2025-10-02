<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Exercisecontroller;
use App\Http\Controllers\FoodCategoryController;
use App\Http\Controllers\FoodController;
use App\Http\Controllers\NutritionRequirementController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
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

// sanctum route
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);

    // user
    Route::apiResource('user', UserController::class);

    // Profile
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);

    // Weight logs
    Route::apiResource('weight-logs', WeightLogController::class);

    // food category
    Route::apiResource('food-category', FoodCategoryController::class);

    // food
    Route::apiResource('food', FoodController::class);
    Route::get('/food/all/paginated', [FoodController::class, 'getAllPaginated']);

    // exercise
    Route::apiResource('exercise', Exercisecontroller::class);
    Route::get('/exercise/all/paginated', [Exercisecontroller::class, 'getAllPaginated']);

    // nutrition requirement
    Route::apiResource('nutrition-requirement', NutritionRequirementController::class);
});

// // Protected routes for users
// Route::middleware(['auth:sanctum'])->group(function () {
//     Route::post('/logout', [AuthController::class, 'logout']);
//     Route::post('/change-password', [AuthController::class, 'changePassword']);

//     // Profile (user only)
//     Route::get('/profile', [ProfileController::class, 'show']);
//     Route::put('/profile', [ProfileController::class, 'update']);

//     // Weight logs (user only)
//     Route::apiResource('weight-logs', WeightLogController::class);

//     // Food Diary (user only)
//     // Route::apiResource('food-diary', FoodDiaryController::class);

//     // Foods (user bisa lihat, tapi tidak bisa edit/delete)
//     Route::get('/food-category', [FoodCategoryController::class, 'index']);
//     Route::get('/food-category/{id}', [FoodCategoryController::class, 'show']);
//     Route::get('/food', [FoodController::class, 'index']);
//     Route::get('/food/{id}', [FoodController::class, 'show']);
//     Route::get('/food/all/paginated', [FoodController::class, 'getAllPaginated']);

//     // // Exercises (user bisa lihat, tapi tidak bisa edit/delete)
//     // Route::get('/exercises', [ExerciseController::class, 'index']);
//     // Route::get('/exercises/{id}', [ExerciseController::class, 'show']);

//     // // Dashboard
//     // Route::get('/dashboard', [DashboardController::class, 'index']);
// });

// // Admin only routes
// Route::middleware(['auth:sanctum', 'admin'])->group(function () {
//     // Users (admin only)
//     Route::apiResource('user', UserController::class);
//     Route::get('/user/all/paginated', [UserController::class, 'getAllPaginated']);
//     Route::apiResource('food-category', FoodCategoryController::class);

//     // // Foods (admin only: create, update, delete)
//     Route::apiResource('food', FoodController::class);


//     // // Exercises (admin only: create, update, delete)
//     // Route::apiResource('exercises', AdminExerciseController::class);

//     // Nutrition Requirements (admin only)
//     Route::apiResource('nutrition-requirement', NutritionRequirementController::class);
// });
