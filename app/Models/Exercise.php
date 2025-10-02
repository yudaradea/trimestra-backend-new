<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Model;

class Exercise extends Model
{
    use UUID;

    protected $fillable = [
        'name',
        'calories_burned_per_minute',
        'jenis',
        'video_url',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', "%{$search}%");
    }

    public function userExercises()
    {
        return $this->hasMany(UserExercise::class);
    }
}
