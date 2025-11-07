<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('video_request_share', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('request_id');
            $table->unsignedInteger('sender_user_id');
            $table->unsignedInteger('recipient_user_id')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('country_code')->nullable();
            $table->string('mobile')->nullable();
            $table->string('email')->nullable();
            $table->text('note')->nullable();
            $table->unsignedInteger('contact_id')->nullable();
            $table->unsignedInteger('group_id')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->timestamps();

            $table->foreign('request_id')->references('id')->on('video_requests')->onDelete('cascade');
            $table->foreign('sender_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('recipient_user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('set null');
            $table->foreign('group_id')->references('id')->on('contact_groups')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('video_request_share');
    }
};
