<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('click_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('click_trans_id')->unique();
            $table->string('merchant_trans_id');
            $table->decimal('amount', 10, 2);
            $table->integer('action');
            $table->string('sign_time');
            $table->integer('status')->default(0); // 0 = prepared, 1 = completed, -1 = failed
            $table->integer('error')->nullable();
            $table->timestamps();

            $table->index('click_trans_id');
            $table->index('merchant_trans_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('click_transactions');
    }
};
