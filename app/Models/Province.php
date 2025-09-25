<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'name',
    ];

    // relasi
    public function regencies()
    {
        return $this->hasMany(Regency::class, 'province_id', 'id');
    }

    // accesor
    public function getNameAttribute($value)
    {
        return ucwords(strtolower($value));
    }
}
