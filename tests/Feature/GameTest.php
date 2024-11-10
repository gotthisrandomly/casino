<?php

namespace Tests\Feature;

use App\Models\Game;
use App\Models\User;
use App\Models\Jackpot;
use App\Models\GameSession;
use App\Models\BonusRound;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GameTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $game;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'balance' => 1000.00
        ]);

        $this->game = Game::factory()->create([
            'min_bet' => 1.00,
            'max_bet' => 100.00,
            'rtp' => 96.50
        ]);
    }

    public function test_user_can_start_game_session()
    {
        $response = $this->actingAs($this->user)
            ->postJson("/api/games/{$this->game->id}/initialize");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'session_id',
                    'game_id',
                    'user_id',
                    'status'
                ]
            ]);

        $this->assertDatabaseHas('game_sessions', [
            'game_id' => $this->game->id,
            'user_id' => $this->user->id,
            'status' => 'active'
        ]);
    }

    public function test_user_cannot_bet_below_minimum()
    {
        $session = GameSession::factory()->create([
            'user_id' => $this->user->id,
            'game_id' => $this->game->id
        ]);

        $response = $this->actingAs($this->user)
            ->postJson("/api/sessions/{$session->id}/play", [
                'action' => 'bet',
                'amount' => 0.50
            ]);

        $response->assertStatus(422);
    }

    public function test_user_cannot_bet_above_maximum()
    {
        $session = GameSession::factory()->create([
            'user_id' => $this->user->id,
            'game_id' => $this->game->id
        ]);

        $response = $this->actingAs($this->user)
            ->postJson("/api/sessions/{$session->id}/play", [
                'action' => 'bet',
                'amount' => 150.00
            ]);

        $response->assertStatus(422);
    }

    public function test_user_cannot_bet_more_than_balance()
    {
        $session = GameSession::factory()->create([
            'user_id' => $this->user->id,
            'game_id' => $this->game->id
        ]);

        $response = $this->actingAs($this->user)
            ->postJson("/api/sessions/{$session->id}/play", [
                'action' => 'bet',
                'amount' => 2000.00
            ]);

        $response->assertStatus(422);
    }

    public function test_jackpot_contribution()
    {
        $jackpot = Jackpot::factory()->create([
            'game_id' => $this->game->id,
            'current_amount' => 1000.00,
            'contribution_rate' => 0.01
        ]);

        $session = GameSession::factory()->create([
            'user_id' => $this->user->id,
            'game_id' => $this->game->id
        ]);

        $betAmount = 100.00;
        $expectedContribution = $betAmount * 0.01;

        $response = $this->actingAs($this->user)
            ->postJson("/api/sessions/{$session->id}/play", [
                'action' => 'bet',
                'amount' => $betAmount
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('jackpots', [
            'id' => $jackpot->id,
            'current_amount' => 1000.00 + $expectedContribution
        ]);
    }

    public function test_bonus_round_activation()
    {
        $session = GameSession::factory()->create([
            'user_id' => $this->user->id,
            'game_id' => $this->game->id
        ]);

        // Mock the bonus round trigger
        $this->mock(GameService::class, function ($mock) {
            $mock->shouldReceive('shouldTriggerBonus')->andReturn(true);
            $mock->shouldReceive('createBonusRound')->andReturn(
                BonusRound::factory()->create([
                    'game_session_id' => $session->id,
                    'free_spins' => 10,
                    'multiplier' => 2.00
                ])
            );
        });

        $response = $this->actingAs($this->user)
            ->postJson("/api/sessions/{$session->id}/play", [
                'action' => 'bet',
                'amount' => 10.00
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'bonus_round' => [
                        'free_spins',
                        'multiplier'
                    ]
                ]
            ]);

        $this->assertDatabaseHas('bonus_rounds', [
            'game_session_id' => $session->id,
            'free_spins' => 10,
            'multiplier' => 2.00
        ]);
    }

    public function test_user_balance_updates_after_win()
    {
        $initialBalance = $this->user->balance;
        $session = GameSession::factory()->create([
            'user_id' => $this->user->id,
            'game_id' => $this->game->id
        ]);

        // Mock a winning spin
        $this->mock(GameService::class, function ($mock) {
            $mock->shouldReceive('calculateWin')->andReturn(50.00);
        });

        $response = $this->actingAs($this->user)
            ->postJson("/api/sessions/{$session->id}/play", [
                'action' => 'bet',
                'amount' => 10.00
            ]);

        $response->assertStatus(200);

        $this->user->refresh();
        $this->assertEquals($initialBalance + 40.00, $this->user->balance);
    }
}