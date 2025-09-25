<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Village extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'name',
        'district_id',
    ];

    // relasi
    public function district()
    {
        return $this->belongsTo(District::class, 'district_id', 'id');
    }

    // accesor
    public function getNameAttribute($value)
    {
        return ucwords(strtolower($value));
    }
}
