<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('rules', function (Blueprint $table) {
            $table->text('simplified_param_name')->nullable()->change();
            $table->text('param_name')->nullable()->change();

            $table->dropColumn('simplified_param_name');
            $table->dropColumn('param_name');

            $table->unsignedBigInteger('param_spec_id')->nullable();
            $table->foreign('param_spec_id')->references('id')->on('emlo_response_param_specs');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rules', function (Blueprint $table) {
            $table->dropForeign(['param_spec_id']);
            $table->dropColumn('param_spec_id');
            $table->text('param_name');
            $table->text('simplified_param_name');
        });
    }
};
