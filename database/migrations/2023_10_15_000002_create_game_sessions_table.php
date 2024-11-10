<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('game_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('game_id')->constrained()->onDelete('cascade');
            $table->string('status'); // active, completed, terminated
            $table->decimal('initial_balance', 10, 2);
            $table->decimal('final_balance', 10, 2)->nullable();
            $table->decimal('total_bet', 10, 2)->default(0);
            $table->decimal('total_win', 10, 2)->default(0);
            $table->string('currency');
            $table->json('game_data')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('game_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('game_sessions');
    }
};