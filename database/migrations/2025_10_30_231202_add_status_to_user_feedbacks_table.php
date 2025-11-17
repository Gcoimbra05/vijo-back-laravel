<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('user_feedbacks', function (Blueprint $table) {
            $table->tinyInteger('status')->default(0)->after('subject'); 
            // after('subject') é opcional, só define a posição
        });
    }

    public function down(): void
    {
        Schema::table('user_feedbacks', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};

