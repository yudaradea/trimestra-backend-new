<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use UUID, HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'message',
        'icon',
        'date',
        'time',
        'read',
    ];

    // Relasi ke user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
