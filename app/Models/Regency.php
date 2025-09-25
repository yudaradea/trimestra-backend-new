<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Regency extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'province_id',
        'name',
    ];

    // relasi
    public function province()
    {
        return $this->belongsTo(Province::class, 'province_id', 'id');
    }

    public function districts()
    {
        return $this->hasMany(District::class, 'regency_id', 'id');
    }



    // accesor
    public function getNameAttribute($value)
    {
        return ucwords(strtolower($value));
    }
}
