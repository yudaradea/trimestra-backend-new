<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{
    protected $fillable = ['email', 'pin', 'expires_at'];
    protected $dates = ['expires_at'];
}
