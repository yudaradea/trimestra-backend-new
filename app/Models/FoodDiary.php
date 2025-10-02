<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Model;

class FoodDiary extends Model
{
    use UUID;

    protected $fillable = [
        'user_id',
        'date',
        'type',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function scopeSearch($query, $search)
    {
        return $query->where('date', 'like', "%{$search}%");
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function foodDiaryItem()
    {
        return $this->hasMany(FoodDiaryItem::class, 'food_diary_id');
    }
}
