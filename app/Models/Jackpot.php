<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Jackpot extends Model
{
    protected $fillable = [
        'game_id',
        'name',
        'current_amount',
        'seed_amount',
        'contribution_rate',
        'trigger_probability',
        'last_won_at',
        'last_winner_id',
    ];

    protected $casts = [
        'current_amount' => 'decimal:2',
        'seed_amount' => 'decimal:2',
        'contribution_rate' => 'decimal:4',
        'trigger_probability' => 'decimal:6',
        'last_won_at' => 'datetime',
    ];

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function lastWinner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_winner_id');
    }

    public function incrementAmount(float $betAmount): void
    {
        $contribution = $betAmount * $this->contribution_rate;
        $this->increment('current_amount', $contribution);
    }

    public function checkTrigger(): bool
    {
        return rand(1, 1000000) / 1000000 <= $this->trigger_probability;
    }

    public function reset(): void
    {
        $this->update([
            'current_amount' => $this->seed_amount,
            'last_won_at' => now(),
        ]);
    }
}