<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use UUID;

    protected $fillable = [
        'user_id',
        'device_code',
        'device_name',
        'is_active',
        'registered_at',
        'last_sync_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'registered_at' => 'datetime',
        'last_sync_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
