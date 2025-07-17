<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('emlo_response_param_specs', function (Blueprint $table) {
            $table->string('emoji', 32)->nullable()->after('description');
        });
    }

    public function down()
    {
        Schema::table('emlo_response_param_specs', function (Blueprint $table) {
            $table->dropColumn('emoji');
        });
    }
};