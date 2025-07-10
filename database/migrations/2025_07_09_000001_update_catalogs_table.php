<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('catalogs', function (Blueprint $table) {
            $table->string('message_title')->nullable()->after('description');
            $table->text('message')->nullable()->after('message_title');
        });
    }

    public function down()
    {
        Schema::table('catalogs', function (Blueprint $table) {
            $table->dropColumn('message_title');
            $table->dropColumn('message');
        });
    }
};