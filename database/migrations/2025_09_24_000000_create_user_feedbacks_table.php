<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('user_feedbacks', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id')->nullable();
            $table->string('type')->default('general'); // general, bug, help, etc
            $table->text('message');
            $table->string('email')->nullable();
            $table->string('subject')->nullable();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }
    public function down() {
        Schema::dropIfExists('user_feedbacks');
    }
};
