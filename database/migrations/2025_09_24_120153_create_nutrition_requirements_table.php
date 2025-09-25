<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('nutrition_requirements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->enum('bmi_category', ['underweight', 'normal', 'overweight', 'obese']);
            $table->boolean('is_pregnant')->default(false);
            $table->integer('trimester')->nullable();
            $table->integer('calories');
            $table->float('protein', 8, 2);
            $table->float('carbohydrates', 8, 2);
            $table->float('fat', 8, 2);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nutrition_requirements');
    }
};
