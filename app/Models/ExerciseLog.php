<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Model;

class ExerciseLog extends Model
{
    use UUID;

    protected $fillable = [
        'user_id',
        'exercise_id',
        'user_exercise_id',
        'duration',
        'calories_burned',
        'date',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function exercise()
    {
        return $this->belongsTo(Exercise::class);
    }

    public function userExercise()
    {
        return $this->belongsTo(UserExercise::class);
    }
}
