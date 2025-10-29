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
        Schema::table('exercise_logs', function (Blueprint $table) {
            $table->boolean('from_device')->default(false)->after('user_exercise_id');
            $table->string('activity_name')->nullable()->after('from_device');
        });
    }

    public function down(): void
    {
        Schema::table('exercise_logs', function (Blueprint $table) {
            $table->dropColumn(['from_device', 'activity_name']);
        });
    }
};
