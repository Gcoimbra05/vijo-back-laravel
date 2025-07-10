<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('membership_plans', function (Blueprint $table) {
            $table->string('price_id')->nullable()->unique()->after('payment_link');
        });
    }

    public function down()
    {
        Schema::table('membership_plans', function (Blueprint $table) {
            $table->dropColumn('price_id');
        });
    }
};