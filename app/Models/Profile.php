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
        'birt_date',
        'height',
        'weight',
        'foto_profil',
        'no_hp',
        'sleep_duration',
        'is_pregnant',
        'trimester',
        'weeks',
        'hpht',
        'province_id',
        'regency_id',
        'district_id',
        'village_id',
        'imt',
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
