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
        Schema::create('food', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('food_category_id');
            $table->foreign('food_category_id')->references('id')->on('food_categories')->onDelete('cascade');

            $table->string('name');
            $table->string('description')->nullable();
            $table->string('image')->nullable();
            $table->json('allergies')->nullable();
            $table->float('calories', 8, 2);
            $table->float('protein', 8, 2);
            $table->float('carbohydrates', 8, 2);
            $table->float('fat', 8, 2);
            $table->float('ukuran_satuan', 8, 2)->default('100');
            $table->string('ukuran_satuan_nama')->default('gram');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('food');
    }
};
