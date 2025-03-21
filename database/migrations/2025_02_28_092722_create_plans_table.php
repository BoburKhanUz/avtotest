<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlansTable extends Migration
{
    public function up()
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Reja nomi');
            $table->string('slug')->unique()->comment('Unikal identifikator');
            $table->decimal('price', 8, 2)->comment('Narx');
            $table->integer('duration_days')->comment('Davomiyligi (kunlarda)');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('plans');
    }
}