<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmloResponseSegmentsTable extends Migration
{
    public function up()
    {
        Schema::create('emlo_response_segments', function (Blueprint $table) {
            $table->integer('id', true, true);
            $table->integer('number')->nullable();
            $table->string('name')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('emlo_response_segments');
    }
}