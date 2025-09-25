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
        Schema::create('profiles', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('user_id')->unique();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->date('birt_date');
            $table->float('height', 8, 2);
            $table->float('weight', 8, 2);
            $table->string('foto_profil')->nullable();
            $table->string('no_hp');
            $table->enum('sleep_duration', ['<7', '7-9', '9-11']);
            $table->boolean('is_pregnant')->default(false);
            $table->integer('trimester')->nullable();
            $table->integer('weeks')->nullable();
            $table->date('hpht')->nullable();
            $table->string('imt')->nullable();

            $table->uuid('province_id')->nullable();
            $table->foreign('province_id')->references('id')->on('provinces');

            $table->uuid('regency_id')->nullable();
            $table->foreign('regency_id')->references('id')->on('regencies');

            $table->uuid('district_id')->nullable();
            $table->foreign('district_id')->references('id')->on('districts');

            $table->uuid('village_id')->nullable();
            $table->foreign('village_id')->references('id')->on('villages');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
