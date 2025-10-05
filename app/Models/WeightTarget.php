<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Model;

class WeightTarget extends Model
{
    use UUID;

    protected $fillable = [
        'user_id',
        'week',
        'expected_weight',
    ];
}
