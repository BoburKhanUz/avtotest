<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionsTable extends Migration
{
    public function up()
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_id')->constrained()->onDelete('cascade');
            $table->text('content_uz')->comment('Savol matni (uz)');
            $table->text('content_ru')->comment('Savol matni (ru)');
            $table->json('options_uz')->comment('Javob variantlari (uz) JSON');
            $table->json('options_ru')->comment('Javob variantlari (ru) JSON');
            $table->string('correct_option')->comment('To‘g‘ri javob');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('questions');
    }
}