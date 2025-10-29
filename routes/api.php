<?php

use App\Http\Controllers\AllergyController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\DiaryController;
use App\Http\Controllers\Exercisecontroller;
use App\Http\Controllers\ExerciseLogController;
use App\Http\Controllers\FoodCategoryController;
use App\Http\Controllers\FoodController;
use App\Http\Controllers\FoodDiaryController;
use App\Http\Controllers\FoodDiaryItemController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\NotificationController;
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
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendPin']);
Route::post('/verify-pin', [ForgotPasswordController::class, 'verifyPin']);
Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword']);
Route::get('/provinces', [LocationController::class, 'provinces']);
Route::get('/regencies', [LocationController::class, 'regencies']);
Route::get('/districts', [LocationController::class, 'districts']);
Route::get('/villages', [LocationController::class, 'villages']);

// sanctum route
Route::middleware(['auth:sanctum', 'update-trimester'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);

    // user
    Route::apiResource('user', UserController::class);
    Route::get('/user/all/paginated', [UserController::class, 'getAllPaginated']);

    // Profile
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::post('/profile', [ProfileController::class, 'store']);
    Route::post('/ganti-profile', [ProfileController::class, 'update']);
    // Route::put('/profile', [ProfileController::class, 'update']);

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

    // from device
    Route::post('/exercise-logs/device', [ExerciseLogController::class, 'storeFromDevice']);
    Route::get('/exercise-logs/activity', [ExerciseLogController::class, 'activityLogs']);

    // Diary
    Route::get('/diary', [DiaryController::class, 'index']);
    Route::get('/diary/report', [DiaryController::class, 'nutritionReport']);


    // alergy
    Route::apiResource('allergy', AllergyController::class);

    // notification
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/notifications/unread', [NotificationController::class, 'unread']);
    Route::patch('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications', [NotificationController::class, 'store']);
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy']);
    Route::post('/notifications/check-achievement', [NotificationController::class, 'checkAchievement']);

    // device
    Route::prefix('device')->group(function () {
        // custom endpoints (letakkan duluan)
        Route::post('/link', [DeviceController::class, 'link']);
        Route::post('/{device}/unlink', [DeviceController::class, 'unlink']);
        Route::get('/status', [DeviceController::class, 'status']);
        Route::get('/linked-devices', [DeviceController::class, 'linkedDevices']);
        Route::post('/{device}/sync', [DeviceController::class, 'sync']);

        // resource routes (letakkan paling bawah)
        Route::get('/', [DeviceController::class, 'index']);
        Route::post('/', [DeviceController::class, 'store']);
        Route::get('/{device}', [DeviceController::class, 'show']);
        Route::put('/{device}', [DeviceController::class, 'update']);
        Route::delete('/{device}', [DeviceController::class, 'destroy']);
    });
});
