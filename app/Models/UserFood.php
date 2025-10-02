<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Model;

class UserFood extends Model
{
    use UUID;

    protected $fillable = [
        'user_id',
        'name',
        'calories',
        'protein',
        'fat',
        'carbohydrates',
        'ukuran_satuan',
        'ukuran_satuan_nama'
    ];

    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', '%' . $search . '%');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function foodDiaryItems()
    {
        return $this->hasMany(FoodDiaryItem::class, 'user_food_id');
    }
}
