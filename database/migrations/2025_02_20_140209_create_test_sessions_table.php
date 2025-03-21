<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTestSessionsTable extends Migration
{
    public function up()
    {
        Schema::create('test_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('test_id')->constrained()->onDelete('cascade');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->integer('time_limit')->default(30)->comment('Daqiqalarda');
            $table->json('user_answers')->nullable()->comment('Foydalanuvchi javoblari');
            $table->integer('score')->nullable();
            $table->string('status')->default('pending')->comment('pending, in_progress, completed');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('test_sessions');
    }
}