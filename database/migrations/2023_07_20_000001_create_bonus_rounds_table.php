<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bonus_rounds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_session_id')->constrained()->onDelete('cascade');
            $table->string('type');
            $table->decimal('multiplier', 8, 2)->default(1.00);
            $table->integer('free_spins')->default(0);
            $table->integer('spins_remaining')->default(0);
            $table->decimal('total_win', 14, 2)->default(0);
            $table->string('status')->default('active');
            $table->json('config')->nullable();
            $table->timestamp('started_at')->useCurrent();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bonus_rounds');
    }
};