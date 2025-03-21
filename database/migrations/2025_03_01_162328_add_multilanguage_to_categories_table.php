<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMultilanguageToCategoriesTable extends Migration
{
    public function up()
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->string('name_uz')->nullable()->after('name'); // O‘zbekcha nom
            $table->string('name_ru')->nullable()->after('name_uz'); // Ruscha nom
            $table->renameColumn('name', 'name_default'); // Eski nomni standart qilib qoldiramiz
        });
    }

    public function down()
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->renameColumn('name_default', 'name');
            $table->dropColumn(['name_uz', 'name_ru']);
        });
    }
}