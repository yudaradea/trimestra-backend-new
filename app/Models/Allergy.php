<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Model;

class Allergy extends Model
{
    use UUID;

    protected $fillable = [
        'name',
        'description',
    ];
}
