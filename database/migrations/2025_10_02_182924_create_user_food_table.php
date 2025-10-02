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
        Schema::create('user_food', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->string('name');
            $table->float('calories', 8, 2);
            $table->float('protein', 8, 2);
            $table->float('fat', 8, 2);
            $table->float('carbohydrates', 8, 2);
            $table->float('ukuran_satuan', 8, 2)->default('100');
            $table->string('ukuran_satuan_nama')->default('gram');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_food');
    }
};
