<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type'); // deposit, withdrawal, bet, win, refund
            $table->decimal('amount', 10, 2);
            $table->string('currency');
            $table->string('status'); // pending, completed, failed, cancelled
            $table->string('payment_provider')->nullable();
            $table->string('payment_id')->nullable();
            $table->json('payment_data')->nullable();
            $table->string('reference')->unique();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('transactions');
    }
};