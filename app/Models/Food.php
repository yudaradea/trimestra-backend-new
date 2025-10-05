<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Model;

class Food extends Model
{
    use UUID;

    protected $fillable = [
        'food_category_id',
        'name',
        'description',
        'image',
        'allergies',
        'calories',
        'protein',
        'carbohydrates',
        'fat',
        'ukuran_satuan',
        'ukuran_satuan_nama',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'allergies' => 'array',
    ];

    public function foodCategory()
    {
        return $this->belongsTo(FoodCategory::class);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', "%{$search}%");
    }

    // public function getAllergiesAttribute()
    // {
    //     return Allergy::whereIn('name', $this->allergies ?? [])->get();
    // }
}
