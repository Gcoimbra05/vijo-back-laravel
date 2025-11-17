<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_feedbacks', function (Blueprint $table) {
            $table->boolean('status_manual')->default(false)->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('user_feedbacks', function (Blueprint $table) {
            $table->dropColumn('status_manual');
        });
    }
};
