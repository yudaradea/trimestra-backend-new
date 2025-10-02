<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Profile extends Model
{
    use UUID, SoftDeletes;

    protected $fillable = [
        'user_id',
        'birth_date',
        'height',
        'weight',
        'foto_profile',
        'no_hp',
        'sleep_duration',
        'food_allergies',
        'bmi',
        'bmi_category',
        'is_pregnant',
        'trimester',
        'weeks',
        'hpht',
        'province_id',
        'regency_id',
        'district_id',
        'village_id',

    ];


    protected $casts = [
        'is_pregnant' => 'boolean',
        'birt_date' => 'date',

    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function regency()
    {
        return $this->belongsTo(Regency::class);
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function village()
    {
        return $this->belongsTo(Village::class);
    }
}
