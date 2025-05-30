<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('membership_plans', function (Blueprint $table) {
            $table->string('slug', 255)->unique()->after('name');
        });
    }

    public function down()
    {
        Schema::table('membership_plans', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};