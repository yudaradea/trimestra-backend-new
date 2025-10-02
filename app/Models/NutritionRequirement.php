<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NutritionRequirement extends Model
{
    use UUID, SoftDeletes;

    protected $fillable = [
        'bmi_category',
        'is_pregnant',
        'trimester',
        'calories',
        'protein',
        'carbohydrates',
        'fat',
    ];

    protected $casts = [
        'is_pregnant' => 'boolean',
    ];
}
