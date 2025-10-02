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
        Schema::create('exercise_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->uuid('exercise_id')->nullable();
            $table->foreign('exercise_id')->references('id')->on('exercises')->onDelete('cascade');

            $table->uuid('user_exercise_id')->nullable();
            $table->foreign('user_exercise_id')->references('id')->on('user_exercises')->onDelete('cascade');

            $table->integer('duration');
            $table->integer('calories_burned');
            $table->date('date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exercise_logs');
    }
};
