<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, UUID, SoftDeletes, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'role',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function scopeSearch($query, $search)
    {
        // tambahkan provinsi, kabupaten, kecamatan, dan kelurahan
        return $query->where('name', 'like', "%{$search}%")
            ->orWhere('email', 'like', "%{$search}%");
    }

    protected static function booted()
    {
        static::deleting(function ($user) {
            $user->profile()->delete(); // Hapus profile saat user dihapus
        });
    }

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function nutritionTargets()
    {
        return $this->hasMany(NutritionTarget::class);
    }

    public function WeightLog()
    {
        return $this->hasMany(WeightLog::class);
    }

    public function foodDiary()
    {
        return $this->hasMany(FoodDiary::class);
    }

    public function userFoods()
    {
        return $this->hasMany(UserFood::class);
    }

    public function userExercises()
    {
        return $this->hasMany(UserExercise::class);
    }

    public function exerciseLogs()
    {
        return $this->hasMany(ExerciseLog::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
}
