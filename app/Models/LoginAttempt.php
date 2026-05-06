<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginAttempt extends Model
{
    const UPDATED_AT = null;

    protected $fillable = [
        'username', 'ip_address', 'user_agent', 'success',
    ];

    protected $casts = [
        'success'    => 'boolean',
        'created_at' => 'datetime',
    ];
}
