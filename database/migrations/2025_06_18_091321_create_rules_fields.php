<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('rules', function (Blueprint $table) {
            $table->string('simplified_param_name', 255)->nullable()->after('param_name');
        });
    }

    public function down()
    {
        Schema::table('rules', function (Blueprint $table) {
            $table->dropColumn('simplified_param_name');
        });
    }
};