<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMultilanguageToTestsTable extends Migration
{
    public function up()
    {
        Schema::table('tests', function (Blueprint $table) {
            // Agar `name_default` mavjud bo‘lmasa, `name` dan keyin qo‘shish
            if (!Schema::hasColumn('tests', 'name_default')) {
                if (Schema::hasColumn('tests', 'name')) {
                    $table->renameColumn('name', 'name_default');
                } else {
                    $table->string('name_default')->nullable(); // Agar `name` ham yo‘q bo‘lsa, yangi qo‘shish
                }
            }
            // Yangi ustunlarni `name_default` dan keyin qo‘shish
            $table->string('name_uz')->nullable()->after('name_default');
            $table->string('name_ru')->nullable()->after('name_uz');
        });
    }

    public function down()
    {
        Schema::table('tests', function (Blueprint $table) {
            $table->dropColumn(['name_uz', 'name_ru']);
            if (Schema::hasColumn('tests', 'name_default')) {
                $table->renameColumn('name_default', 'name');
            }
        });
    }
}