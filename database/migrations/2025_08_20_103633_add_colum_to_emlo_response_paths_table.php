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
        Schema::table('emlo_response_paths', function (Blueprint $table) {
            $table->unsignedBigInteger('emlo_param_spec_id')->nullable();
            $table->foreign('emlo_param_spec_id')->references('id')->on('emlo_response_param_specs');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('emlo_response_paths', function (Blueprint $table) {
            $table->dropColumn('emlo_param_spec_id');
        });
    }
};
