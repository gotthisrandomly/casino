<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BonusRound extends Model
{
    protected $fillable = [
        'game_session_id',
        'type',
        'multiplier',
        'free_spins',
        'spins_remaining',
        'total_win',
        'status',
        'config',
    ];

    protected $casts = [
        'multiplier' => 'decimal:2',
        'total_win' => 'decimal:2',
        'config' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function gameSession(): BelongsTo
    {
        return $this->belongsTo(GameSession::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function hasSpinsRemaining(): bool
    {
        return $this->spins_remaining > 0;
    }

    public function decrementSpins(): void
    {
        if ($this->hasSpinsRemaining()) {
            $this->decrement('spins_remaining');
            
            if ($this->spins_remaining === 0) {
                $this->complete();
            }
        }
    }

    public function complete(): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }
}