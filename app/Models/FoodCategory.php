<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Model;

class FoodCategory extends Model
{
    use UUID;

    protected $fillable = [
        'name',
        'order',
        'icon',
    ];

    public function foods()
    {
        return $this->hasMany(Food::class);
    }
}
