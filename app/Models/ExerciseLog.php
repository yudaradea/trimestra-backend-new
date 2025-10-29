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
        'from_device',
        'activity_name',
    ];

    protected $casts = [
        // buat date dengan jam juga
        'date' => 'date',
        'from_device' => 'boolean',
    ];

    public function scopeSearch($query, $search)
    {
        // mencari dengan nama user dan exercise

        if ($search) {
            $query->whereHas('user', function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            })
                ->orWhereHas('exercise', function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('userExercise', function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%");
                });
        }


        return $query;
    }

    public function getDateAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->timezone('Asia/Jakarta')->toDateString();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function exercise()
    {
        return $this->belongsTo(Exercise::class, 'exercise_id');
    }

    public function userExercise()
    {
        return $this->belongsTo(UserExercise::class, 'user_exercise_id');
    }
}
