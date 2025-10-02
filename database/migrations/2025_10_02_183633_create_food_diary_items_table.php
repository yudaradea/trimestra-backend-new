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
        Schema::create('food_diary_items', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('food_diary_id');
            $table->foreign('food_diary_id')->references('id')->on('food_diaries')->onDelete('cascade');

            $table->uuid('food_id')->nullable();
            $table->foreign('food_id')->references('id')->on('food')->onDelete('cascade');

            $table->uuid('user_food_id')->nullable();
            $table->foreign('user_food_id')->references('id')->on('user_food')->onDelete('cascade');

            $table->float('quantity', 8, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('food_diary_items');
    }
};
