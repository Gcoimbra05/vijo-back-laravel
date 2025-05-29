<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmloResponseValuesTable extends Migration
{
    public function up()
    {
        Schema::create('emlo_response_values', function (Blueprint $table) {
            $table->integer('id', true, true);
            $table->unsignedInteger('response_id');
            $table->unsignedInteger('path_id');
            $table->text('value')->nullable();
            $table->timestamps();

            $table->foreign('response_id')->references('id')->on('emlo_responses')->onDelete('cascade');
            $table->foreign('path_id')->references('id')->on('emlo_response_paths');
        });
    }

    public function down()
    {
        Schema::dropIfExists('emlo_response_values');
    }
}