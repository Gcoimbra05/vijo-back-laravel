<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAffiliatesTable extends Migration
{
    public function up()
    {
        Schema::create('affiliates', function (Blueprint $table) {
            $table->integer('id', true, true);
            $table->unsignedInteger('user_id');
            $table->string('status', 50)->nullable();
            $table->timestamps();

            // Define foreign key
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('affiliates');
    }
}