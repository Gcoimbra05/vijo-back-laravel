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
        Schema::create('realtime_sessions', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->foreign("user_id")->references("id")->on('users');
            $table->unsignedInteger("user_id");

            $table->text('openai_session_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('realtime_sessions');
    }
};
