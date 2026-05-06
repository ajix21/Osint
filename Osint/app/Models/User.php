<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'username', 'email', 'password',
        'role', 'is_active', 'api_token',
        'last_login_at', 'last_login_ip',
    ];

    protected $hidden = [
        'password', 'remember_token', 'api_token',
    ];

    protected $casts = [
        'is_active'     => 'boolean',
        'last_login_at' => 'datetime',
    ];

    public function searchLogs()
    {
        return $this->hasMany(SearchLog::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isOperator(): bool
    {
        return $this->role === 'operator';
    }

    public function canSearch(): bool
    {
        return in_array($this->role, ['admin', 'operator']);
    }
}
