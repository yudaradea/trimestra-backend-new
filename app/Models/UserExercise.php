<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Model;

class UserExercise extends Model
{
    use UUID;

    protected $fillable = [
        'user_id',
        'name',
        'calories_burned_per_minute',
        'jenis',
    ];

    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', '%' . $search . '%');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function exerciseLogs()
    {
        return $this->hasMany(ExerciseLog::class, 'user_exercise_id');
    }
}
