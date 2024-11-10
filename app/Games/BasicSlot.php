<?php

namespace App\Games;

use App\Models\GameSession;
use Illuminate\Support\Arr;

class BasicSlot extends AbstractGame
{
    protected array $symbols = ['7', 'BAR', 'CHERRY', 'LEMON', 'ORANGE', 'PLUM'];
    protected array $payTable = [
        '7' => 100,
        'BAR' => 50,
        'CHERRY' => 25,
        'LEMON' => 15,
        'ORANGE' => 10,
        'PLUM' => 5,
    ];

    public function placeBet(GameSession $session, float $amount, array $betData = []): array
    {
        if (!$this->validateBet($amount, $betData)) {
            throw new \InvalidArgumentException('Invalid bet amount or bet data');
        }

        // Deduct bet amount
        $user = $session->user;
        $user->decrement('balance', $amount);
        $this->createTransaction($session, 'bet', $amount);

        // Generate result
        $result = $this->generateSpinResult();
        
        // Calculate winnings
        $winAmount = $this->calculateWinAmount($result, $amount);
        
        if ($winAmount > 0) {
            $user->increment('balance', $winAmount);
            $this->createTransaction($session, 'win', $winAmount);
        }

        // Update session stats
        $session->increment('total_bet', $amount);
        $session->increment('total_win', $winAmount);

        // Update game state
        $this->updateGameState($session, [
            'lastSpin' => [
                'symbols' => $result,
                'bet' => $amount,
                'win' => $winAmount,
            ],
        ]);

        return [
            'symbols' => $result,
            'bet' => $amount,
            'win' => $winAmount,
            'newBalance' => $user->balance,
        ];
    }

    public function getGameState(GameSession $session): array
    {
        return [
            'gameData' => $session->game_data,
            'totalBet' => $session->total_bet,
            'totalWin' => $session->total_win,
            'currentBalance' => $session->user->balance,
        ];
    }

    protected function getInitialState(): array
    {
        return [
            'lastSpin' => null,
            'statistics' => [
                'spins' => 0,
                'totalBet' => 0,
                'totalWin' => 0,
            ],
        ];
    }

    protected function validateBetData(array $betData): bool
    {
        // Basic slot doesn't require additional bet data
        return true;
    }

    private function generateSpinResult(): array
    {
        $result = [];
        for ($i = 0; $i < 3; $i++) {
            $result[] = Arr::random($this->symbols);
        }
        return $result;
    }

    private function calculateWinAmount(array $symbols, float $betAmount): float
    {
        // All symbols match
        if (count(array_unique($symbols)) === 1) {
            $multiplier = $this->payTable[$symbols[0]];
            return $betAmount * $multiplier;
        }

        // Two matching symbols
        $symbolCounts = array_count_values($symbols);
        $maxCount = max($symbolCounts);
        if ($maxCount === 2) {
            $symbol = array_search(2, $symbolCounts);
            $multiplier = $this->payTable[$symbol] * 0.2; // 20% of full win for two matching symbols
            return $betAmount * $multiplier;
        }

        return 0;
    }
}