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
        Schema::table('catalogs', function (Blueprint $table) {
            $table->unsignedBigInteger('vijo_plan_id')->nullable();
            $table->foreign('vijo_plan_id')->references('id')->on('vijo_plans');
            $table->integer('vijo_plan_order')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('catalogs', function (Blueprint $table) {
            $table->dropConstrainedForeignIdFor('vijo_plan_id');
            $table->dropColumn('vijo_plan_id');
            $table->dropColumn('vijo_plan_order');

        });
    }
};
