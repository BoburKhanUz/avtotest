<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payme_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('payme_transaction_id')->unique();
            $table->bigInteger('payme_time');
            $table->bigInteger('amount');
            $table->json('account');
            $table->integer('state')->default(1); // 1: created, 2: completed, -1: cancelled before perform, -2: cancelled after perform
            $table->timestamp('create_time')->nullable();
            $table->timestamp('perform_time')->nullable();
            $table->timestamp('cancel_time')->nullable();
            $table->integer('reason')->nullable();
            $table->timestamps();

            $table->index('payme_transaction_id');
            $table->index('state');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payme_transactions');
    }
};
