<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jackpots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->decimal('current_amount', 14, 2)->default(0);
            $table->decimal('seed_amount', 14, 2);
            $table->decimal('contribution_rate', 6, 4);
            $table->decimal('trigger_probability', 8, 6);
            $table->foreignId('last_winner_id')->nullable()->constrained('users');
            $table->timestamp('last_won_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jackpots');
    }
};