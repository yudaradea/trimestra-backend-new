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

    protected $casts = [
        'date' => 'date',
    ];

    public function getDateAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->timezone('Asia/Jakarta')->toDateString();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
