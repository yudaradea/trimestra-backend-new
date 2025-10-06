<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // membuat user admin dan profilenya
        // User::factory()->create([
        //     'name' => 'Admin',
        //     'email' => 'admin@example.com',
        //     'password' => bcrypt('password'),
        //     'role' => 'admin'
        // ]);

        // buat user tanpa faktory
        User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin'
        ]);

        $this->call([
            ProvinceSeeder::class,
            RegencySeeder::class,
            DistrictSeeder::class,
            VillageSeeder::class,
            NutritionRequirementSeeder::class,
            FoodCategorySeeder::class,
            FoodSeeder::class,
            ExerciseSeeder::class,
            AllergySeeder::class
        ]);
    }
}
