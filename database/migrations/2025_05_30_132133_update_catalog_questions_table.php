<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('catalog_questions', function (Blueprint $table) {
            $table->foreign('catalog_id')->references('id')->on('catalogs')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('catalog_questions', function (Blueprint $table) {
            $table->dropForeign(['catalog_id']);
        });
    }
};