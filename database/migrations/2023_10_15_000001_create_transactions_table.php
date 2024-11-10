<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('game_session_id')->nullable()->constrained()->onDelete('set null');
            $table->string('type'); // deposit, withdrawal, bet, win, bonus
            $table->decimal('amount', 10, 2);
            $table->string('currency');
            $table->string('status'); // pending, completed, failed, cancelled
            $table->string('reference')->unique(); // transaction reference number
            $table->string('description')->nullable();
            $table->json('metadata')->nullable(); // additional transaction data
            $table->timestamps();

            $table->index(['user_id', 'type', 'status']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};