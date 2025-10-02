<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NutritionTarget extends Model
{
    use UUID, SoftDeletes;

    protected $fillable = [
        'user_id',
        'date',
        'calories',
        'protein',
        'carbohydrates',
        'fat',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
