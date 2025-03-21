<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromocodesTable extends Migration
{
    public function up()
    {
        Schema::create('promocodes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique()->comment('Promokod');
            $table->integer('discount_percentage')->comment('Chegirma foizi');
            $table->timestamp('expires_at')->nullable()->comment('Amal qilish muddati');
            $table->boolean('is_active')->default(true);
            $table->integer('usage_limit')->nullable()->comment('Foydalanish chegarasi');
            $table->integer('used_count')->default(0)->comment('Ishlatilgan soni');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('promocodes');
    }
}