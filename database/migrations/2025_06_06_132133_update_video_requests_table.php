<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Altera o campo 'type' para aceitar também o valor 'share'
        Schema::table('video_requests', function (Blueprint $table) {
            $table->enum('type', ['daily', 'request', 'share'])->default('request')->change();
        });
    }

    public function down()
    {
        // Volta para os valores anteriores
        Schema::table('video_requests', function (Blueprint $table) {
            $table->enum('type', ['daily', 'request'])->default('request')->change();
        });
    }
};