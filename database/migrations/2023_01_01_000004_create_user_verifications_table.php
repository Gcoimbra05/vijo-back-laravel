<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserVerificationsTable extends Migration
{
    public function up()
    {
        Schema::create('user_verifications', function (Blueprint $table) {
            $table->integer('id', true, true);
            $table->unsignedInteger('user_id');
            $table->string('code', 10)->nullable(false);
            $table->dateTime('expires_at')->nullable();
            $table->boolean('is_used')->default(false);
            $table->timestamps();

            // Define foreign key
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_verifications');
    }
}