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
        Schema::create('user_feedback_replies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_feedback_id')->nullable();;
            $table->unsignedInteger('user_id')->nullable();
            $table->string('type')->nullable();
            $table->string('subject')->nullable();
            $table->text('message');
            $table->timestamps();
            
            $table->foreign('user_feedback_id')
                  ->references('id')
                  ->on('user_feedbacks')
                  ->onDelete('cascade');

            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_feedback_replies');
    }
};
