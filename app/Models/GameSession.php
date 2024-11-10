<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'game_id',
        'status',
        'initial_balance',
        'final_balance',
        'total_bet',
        'total_win',
        'currency',
        'game_data',
        'ended_at',
    ];

    protected $casts = [
        'initial_balance' => 'decimal:2',
        'final_balance' => 'decimal:2',
        'total_bet' => 'decimal:2',
        'total_win' => 'decimal:2',
        'game_data' => 'array',
        'ended_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function getNetResult()
    {
        return $this->total_win - $this->total_bet;
    }

    public function isWinning()
    {
        return $this->getNetResult() > 0;
    }
}