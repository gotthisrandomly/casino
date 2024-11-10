<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('type'); // slot, table, card, etc.
            $table->string('provider');
            $table->decimal('min_bet', 10, 2);
            $table->decimal('max_bet', 10, 2);
            $table->decimal('rtp', 5, 2); // Return to Player percentage
            $table->string('volatility'); // low, medium, high
            $table->json('features')->nullable(); // game-specific features
            $table->string('status')->default('active'); // active, maintenance, disabled
            $table->string('thumbnail')->nullable();
            $table->json('config')->nullable(); // game configuration
            $table->timestamps();

            $table->index('slug');
            $table->index(['type', 'status']);
            $table->index('provider');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};