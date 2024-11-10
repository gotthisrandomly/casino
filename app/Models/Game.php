<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'type',
        'provider',
        'min_bet',
        'max_bet',
        'rtp',
        'volatility',
        'features',
        'status',
        'thumbnail',
        'config',
    ];

    protected $casts = [
        'min_bet' => 'decimal:2',
        'max_bet' => 'decimal:2',
        'rtp' => 'decimal:2',
        'features' => 'array',
        'config' => 'array',
        'is_active' => 'boolean',
    ];

    public function sessions()
    {
        return $this->hasMany(GameSession::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByProvider($query, $provider)
    {
        return $query->where('provider', $provider);
    }

    public function getThumbnailUrl()
    {
        return asset('storage/games/' . $this->thumbnail);
    }

    public function isAvailable()
    {
        return $this->status === 'active';
    }
}