<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Model;

class WeightLog extends Model
{
    use UUID;

    protected $fillable = [
        'user_id',
        'weight',
        'date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
