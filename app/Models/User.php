<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'balance',
        'currency',
        'is_active',
        'last_login_at',
        'last_login_ip',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'balance' => 'decimal:2',
        'last_login_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function games()
    {
        return $this->hasMany(GameSession::class);
    }

    public function deposits()
    {
        return $this->hasMany(Transaction::class)->where('type', 'deposit');
    }

    public function withdrawals()
    {
        return $this->hasMany(Transaction::class)->where('type', 'withdrawal');
    }
}