<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wallet extends Model
{
    protected $fillable = [
        'user_id',
        'currency',
        'balance',
        'bonus_balance',
        'is_frozen',
        'last_transaction_at',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'bonus_balance' => 'decimal:2',
        'is_frozen' => 'boolean',
        'last_transaction_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function addBalance(float $amount, string $type = 'deposit'): bool
    {
        if ($this->is_frozen) {
            return false;
        }

        $this->balance += $amount;
        $this->last_transaction_at = now();
        
        return $this->save();
    }

    public function subtractBalance(float $amount, string $type = 'withdrawal'): bool
    {
        if ($this->is_frozen || $this->balance < $amount) {
            return false;
        }

        $this->balance -= $amount;
        $this->last_transaction_at = now();
        
        return $this->save();
    }

    public function addBonusBalance(float $amount): bool
    {
        if ($this->is_frozen) {
            return false;
        }

        $this->bonus_balance += $amount;
        $this->last_transaction_at = now();
        
        return $this->save();
    }

    public function subtractBonusBalance(float $amount): bool
    {
        if ($this->is_frozen || $this->bonus_balance < $amount) {
            return false;
        }

        $this->bonus_balance -= $amount;
        $this->last_transaction_at = now();
        
        return $this->save();
    }

    public function freeze(): bool
    {
        $this->is_frozen = true;
        return $this->save();
    }

    public function unfreeze(): bool
    {
        $this->is_frozen = false;
        return $this->save();
    }

    public function getTotalBalance(): float
    {
        return $this->balance + $this->bonus_balance;
    }
}