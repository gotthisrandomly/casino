<?php

namespace App\Providers;

use App\Contracts\GameInterface;
use App\Games\BasicSlot;
use App\Models\Game;
use Illuminate\Support\ServiceProvider;

class GameServiceProvider extends ServiceProvider
{
    protected $gameTypes = [
        'basic-slot' => BasicSlot::class,
    ];

    public function register(): void
    {
        $this->app->singleton('game.factory', function ($app) {
            return function (Game $gameModel) {
                $gameClass = $this->gameTypes[$gameModel->slug] ?? null;

                if (!$gameClass) {
                    throw new \InvalidArgumentException("No implementation found for game type: {$gameModel->slug}");
                }

                return new $gameClass($gameModel);
            };
        });

        // Bind the interface to a factory resolver
        $this->app->bind(GameInterface::class, function ($app) {
            return function (Game $gameModel) use ($app) {
                return $app->make('game.factory')($gameModel);
            };
        });
    }

    public function boot(): void
    {
        //
    }

    public function provides(): array
    {
        return ['game.factory'];
    }
}