<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('emlo_response_param_specs', function (Blueprint $table) {
            $table->text('simplified_param_name');
        });
    }

    public function down()
    {
        // Remove o campo is_private e volta o campo type para os valores anteriores
        Schema::table('emlo_response_param_specs', function (Blueprint $table) {
            $table->dropColumn('simplified_param_name');
        });
    }
};
