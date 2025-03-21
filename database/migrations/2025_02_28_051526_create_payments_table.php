<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('subscription_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('promocode_id')->nullable()->constrained('promocodes')->onDelete('set null');
            $table->decimal('amount', 8, 2)->comment('To‘lov summasi');
            $table->string('status')->default('pending')->comment('pending, completed, failed');
            $table->string('stripe_id')->nullable()->comment('Stripe tranzaksiya ID');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payments');
    }
}