<?php

namespace App\Http\Controllers\Api;

use App\Contracts\GameInterface;
use App\Http\Requests\GamePlayRequest;
use App\Http\Requests\InitializeGameRequest;
use App\Models\Game;
use App\Models\GameSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class GameController extends ApiController
{
    public function index(): JsonResponse
    {
        $games = Game::active()
            ->select(['id', 'name', 'slug', 'description', 'type', 'provider', 'min_bet', 'max_bet', 'thumbnail'])
            ->get();

        return $this->successResponse($games);
    }

    public function show(Game $game): JsonResponse
    {
        return $this->successResponse($game);
    }

    public function initialize(InitializeGameRequest $request, Game $game): JsonResponse
    {
        try {
            if (!$game->isAvailable()) {
                return $this->errorResponse('This game is currently not available.');
            }

            $user = $request->user();
            
            // Check for existing active session
            $existingSession = GameSession::where('user_id', $user->id)
                ->where('status', 'active')
                ->first();

            if ($existingSession) {
                return $this->errorResponse('You have an active game session. Please end it before starting a new one.');
            }

            // Create game instance
            $gameInstance = app()->make(GameInterface::class)($game);
            
            // Initialize session
            $session = $gameInstance->initializeSession($user, $request->validated('config', []));

            return $this->successResponse([
                'session_id' => $session->id,
                'game_state' => $gameInstance->getGameState($session),
            ]);

        } catch (\Exception $e) {
            return $this->errorResponse('Failed to initialize game session: ' . $e->getMessage());
        }
    }

    public function play(GamePlayRequest $request, GameSession $session): JsonResponse
    {
        try {
            if ($session->status !== 'active') {
                return $this->errorResponse('This game session is not active.');
            }

            $user = $request->user();
            if ($session->user_id !== $user->id) {
                return $this->errorResponse('You do not have access to this game session.');
            }

            $game = $session->game;
            $gameInstance = app()->make(GameInterface::class)($game);

            $validated = $request->validated();
            $action = $validated['action'];
            
            return DB::transaction(function () use ($action, $validated, $session, $gameInstance) {
                switch ($action) {
                    case 'bet':
                        $amount = $validated['amount'];
                        $betData = $validated['bet_data'] ?? [];

                        if ($amount > $session->user->balance) {
                            return $this->errorResponse('Insufficient balance.');
                        }

                        if ($amount < $session->game->min_bet || $amount > $session->game->max_bet) {
                            return $this->errorResponse(
                                sprintf(
                                    'Bet amount must be between %s and %s.',
                                    $session->game->min_bet,
                                    $session->game->max_bet
                                )
                            );
                        }

                        $result = $gameInstance->placeBet($session, $amount, $betData);
                        return $this->successResponse($result);

                    case 'state':
                        $state = $gameInstance->getGameState($session);
                        return $this->successResponse($state);

                    default:
                        return $this->errorResponse('Invalid action.');
                }
            });

        } catch (\Exception $e) {
            return $this->errorResponse('Failed to process game action: ' . $e->getMessage());
        }
    }

    public function end(GameSession $session): JsonResponse
    {
        try {
            if ($session->status !== 'active') {
                return $this->errorResponse('This game session is not active.');
            }

            $user = request()->user();
            if ($session->user_id !== $user->id) {
                return $this->errorResponse('You do not have access to this game session.');
            }

            $game = $session->game;
            $gameInstance = app()->make(GameInterface::class)($game);

            $endedSession = $gameInstance->endSession($session);

            return $this->successResponse([
                'session_id' => $endedSession->id,
                'final_balance' => $endedSession->final_balance,
                'total_bet' => $endedSession->total_bet,
                'total_win' => $endedSession->total_win,
                'net_result' => $endedSession->total_win - $endedSession->total_bet,
                'duration' => $endedSession->created_at->diffInSeconds($endedSession->ended_at),
            ]);

        } catch (\Exception $e) {
            return $this->errorResponse('Failed to end game session: ' . $e->getMessage());
        }
    }
}

namespace App\Http\Controllers\Api;

use App\Contracts\GameInterface;
use App\Models\Game;
use App\Models\GameSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class GameController extends ApiController
{
    public function index(): JsonResponse
    {
        $games = Game::active()
            ->select(['id', 'name', 'slug', 'description', 'type', 'provider', 'min_bet', 'max_bet', 'thumbnail'])
            ->get();

        return $this->successResponse($games);
    }

    public function show(Game $game): JsonResponse
    {
        return $this->successResponse($game);
    }

    public function initialize(Request $request, Game $game): JsonResponse
    {
        try {
            $request->validate([
                'config' => 'sometimes|array',
            ]);

            if (!$game->isAvailable()) {
                return $this->errorResponse('This game is currently not available.');
            }

            $user = $request->user();
            
            // Check for existing active session
            $existingSession = GameSession::where('user_id', $user->id)
                ->where('status', 'active')
                ->first();

            if ($existingSession) {
                return $this->errorResponse('You have an active game session. Please end it before starting a new one.');
            }

            // Create game instance
            $gameInstance = app()->make(GameInterface::class)($game);
            
            // Initialize session
            $session = $gameInstance->initializeSession($user, $request->input('config', []));

            return $this->successResponse([
                'session_id' => $session->id,
                'game_state' => $gameInstance->getGameState($session),
            ]);

        } catch (ValidationException $e) {
            return $this->errorResponse($e->getMessage());
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to initialize game session: ' . $e->getMessage());
        }
    }

    public function play(Request $request, GameSession $session): JsonResponse
    {
        try {
            $request->validate([
                'action' => 'required|string',
                'amount' => 'required_if:action,bet|numeric|min:0',
                'bet_data' => 'sometimes|array',
            ]);

            if ($session->status !== 'active') {
                return $this->errorResponse('This game session is not active.');
            }

            $user = $request->user();
            if ($session->user_id !== $user->id) {
                return $this->errorResponse('You do not have access to this game session.');
            }

            $game = $session->game;
            $gameInstance = app()->make(GameInterface::class)($game);

            $action = $request->input('action');
            
            return DB::transaction(function () use ($action, $request, $session, $gameInstance) {
                switch ($action) {
                    case 'bet':
                        $amount = $request->input('amount');
                        $betData = $request->input('bet_data', []);

                        if ($amount > $session->user->balance) {
                            return $this->errorResponse('Insufficient balance.');
                        }

                        $result = $gameInstance->placeBet($session, $amount, $betData);
                        return $this->successResponse($result);

                    case 'state':
                        $state = $gameInstance->getGameState($session);
                        return $this->successResponse($state);

                    default:
                        return $this->errorResponse('Invalid action.');
                }
            });

        } catch (ValidationException $e) {
            return $this->errorResponse($e->getMessage());
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to process game action: ' . $e->getMessage());
        }
    }

    public function end(GameSession $session): JsonResponse
    {
        try {
            if ($session->status !== 'active') {
                return $this->errorResponse('This game session is not active.');
            }

            $user = request()->user();
            if ($session->user_id !== $user->id) {
                return $this->errorResponse('You do not have access to this game session.');
            }

            $game = $session->game;
            $gameInstance = app()->make(GameInterface::class)($game);

            $endedSession = $gameInstance->endSession($session);

            return $this->successResponse([
                'session_id' => $endedSession->id,
                'final_balance' => $endedSession->final_balance,
                'total_bet' => $endedSession->total_bet,
                'total_win' => $endedSession->total_win,
                'net_result' => $endedSession->total_win - $endedSession->total_bet,
            ]);

        } catch (\Exception $e) {
            return $this->errorResponse('Failed to end game session: ' . $e->getMessage());
        }
    }
}