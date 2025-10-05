<?php

namespace App\Providers;

use App\Interfaces\AllergyRepositoryInterface;
use App\Interfaces\AuthRepositoryInterface;
use App\Interfaces\ExerciseRepositoryInterface;
use App\Interfaces\FoodCategoryRepositoryInterfaces;
use App\Interfaces\FoodDiaryRepositoryInterface;
use App\Interfaces\FoodRepositoryInterface;
use App\Interfaces\NutritionRequirementsRepositoryInterfaces;
use App\Interfaces\UserExerciseRepositoryInterface;
use App\Interfaces\UserFoodRepositoryInterface;
use App\Interfaces\UserRepositoryInterfaces;
use App\Repositories\AllergyRepository;
use App\Repositories\AuthRepository;
use App\Repositories\ExerciseRepository;
use App\Repositories\FoodCategoryRepository;
use App\Repositories\FoodDiaryRepository;
use App\Repositories\FoodRepository;
use App\Repositories\NutritionRequirementsRepository;
use App\Repositories\UserExerciseRepository;
use App\Repositories\UserFoodRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(AuthRepositoryInterface::class, AuthRepository::class);
        $this->app->bind(UserRepositoryInterfaces::class, UserRepository::class);
        $this->app->bind(NutritionRequirementsRepositoryInterfaces::class, NutritionRequirementsRepository::class);
        $this->app->bind(FoodCategoryRepositoryInterfaces::class, FoodCategoryRepository::class);
        $this->app->bind(FoodRepositoryInterface::class, FoodRepository::class);
        $this->app->bind(ExerciseRepositoryInterface::class, ExerciseRepository::class);
        $this->app->bind(FoodDiaryRepositoryInterface::class, FoodDiaryRepository::class);
        $this->app->bind(UserFoodRepositoryInterface::class, UserFoodRepository::class);
        $this->app->bind(UserExerciseRepositoryInterface::class, UserExerciseRepository::class);
        $this->app->bind(AllergyRepositoryInterface::class, AllergyRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
