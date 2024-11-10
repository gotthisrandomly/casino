<?php

namespace App\Contracts;

use App\Models\GameSession;
use App\Models\User;

interface GameInterface
{
    /**
     * Initialize a new game session
     */
    public function initializeSession(User $user, array $config = []): GameSession;

    /**
     * Process a player's bet
     */
    public function placeBet(GameSession $session, float $amount, array $betData = []): array;

    /**
     * Get the current state of the game
     */
    public function getGameState(GameSession $session): array;

    /**
     * End the current game session
     */
    public function endSession(GameSession $session): GameSession;

    /**
     * Get game configuration
     */
    public function getConfig(): array;

    /**
     * Validate a bet amount and betting data
     */
    public function validateBet(float $amount, array $betData = []): bool;
}