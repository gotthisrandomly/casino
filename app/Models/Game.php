<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Game extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'type',
        'provider',
        'thumbnail',
        'min_bet',
        'max_bet',
        'rtp',
        'volatility',
        'features',
        'status',
    ];

    protected $casts = [
        'min_bet' => 'decimal:2',
        'max_bet' => 'decimal:2',
        'rtp' => 'decimal:2',
        'features' => 'array',
    ];

    public function sessions(): HasMany
    {
        return $this->hasMany(GameSession::class);
    }

    public function jackpots(): HasMany
    {
        return $this->hasMany(Jackpot::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function isAvailable(): bool
    {
        return $this->status === 'active';
    }

    public function validateBet(float $amount): bool
    {
        return $amount >= $this->min_bet && $amount <= $this->max_bet;
    }
}