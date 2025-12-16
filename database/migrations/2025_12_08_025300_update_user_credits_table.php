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
        Schema::table('user_credits', function (Blueprint $table) {
            $table->dropColumn('balance');
            $table->integer('general_credit_balance')->default(0);
            $table->integer("ai_credit_balance")->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_credits', function (Blueprint $table) {
            $table->integer('balance');
            $table->dropColumn('general_credit_balance');
            $table->intedropColumnger("ai_credit_balance");
        });
    }
};
