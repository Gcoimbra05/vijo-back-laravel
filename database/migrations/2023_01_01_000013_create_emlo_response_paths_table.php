<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmloResponsePathsTable extends Migration
{
    public function up()
    {
        Schema::create('emlo_response_paths', function (Blueprint $table) {
            $table->integer('id', true, true);
            $table->string('path_key')->notNullable();
            $table->text('json_path')->notNullable();
            $table->string('data_type')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('emlo_response_paths');
    }
}