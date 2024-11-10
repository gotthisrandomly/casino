<?php

namespace App\Games;

use App\Contracts\GameInterface;
use App\Models\Game;
use App\Models\GameSession;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Str;

abstract class AbstractGame implements GameInterface
{
    protected Game $gameModel;
    protected array $config;

    public function __construct(Game $gameModel)
    {
        $this->gameModel = $gameModel;
        $this->config = $gameModel->config;
    }

    public function initializeSession(User $user, array $config = []): GameSession
    {
        return GameSession::create([
            'user_id' => $user->id,
            'game_id' => $this->gameModel->id,
            'status' => 'active',
            'initial_balance' => $user->balance,
            'currency' => $user->currency,
            'game_data' => [
                'config' => array_merge($this->config, $config),
                'state' => $this->getInitialState(),
            ],
        ]);
    }

    public function validateBet(float $amount, array $betData = []): bool
    {
        if ($amount < $this->gameModel->min_bet || $amount > $this->gameModel->max_bet) {
            return false;
        }

        return $this->validateBetData($betData);
    }

    public function endSession(GameSession $session): GameSession
    {
        $session->update([
            'status' => 'completed',
            'final_balance' => $session->user->balance,
            'ended_at' => now(),
        ]);

        return $session->fresh();
    }

    public function getConfig(): array
    {
        return array_merge(
            [
                'min_bet' => $this->gameModel->min_bet,
                'max_bet' => $this->gameModel->max_bet,
                'type' => $this->gameModel->type,
                'provider' => $this->gameModel->provider,
            ],
            $this->config
        );
    }

    protected function createTransaction(GameSession $session, string $type, float $amount): Transaction
    {
        return Transaction::create([
            'user_id' => $session->user_id,
            'game_session_id' => $session->id,
            'type' => $type,
            'amount' => $amount,
            'currency' => $session->currency,
            'status' => 'completed',
            'reference' => Str::uuid(),
            'description' => "Game {$type} for session #{$session->id}",
        ]);
    }

    protected function updateGameState(GameSession $session, array $newState): void
    {
        $gameData = $session->game_data;
        $gameData['state'] = array_merge($gameData['state'] ?? [], $newState);
        
        $session->update([
            'game_data' => $gameData,
        ]);
    }

    abstract protected function getInitialState(): array;
    
    abstract protected function validateBetData(array $betData): bool;
}