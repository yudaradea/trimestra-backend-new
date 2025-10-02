<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Model;

class FoodDiaryItem extends Model
{
    use UUID;

    protected $fillable = [
        'food_diary_id',
        'food_id',
        'user_food_id',
        'quantity',
    ];

    public function foodDiary()
    {
        return $this->belongsTo(FoodDiary::class);
    }

    public function food()
    {
        return $this->belongsTo(Food::class);
    }

    public function userFood()
    {
        return $this->belongsTo(UserFood::class);
    }
}
