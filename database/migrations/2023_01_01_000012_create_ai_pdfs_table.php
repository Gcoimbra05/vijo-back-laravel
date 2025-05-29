<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAiPdfsTable extends Migration
{
    public function up()
    {
        Schema::create('ai_pdfs', function (Blueprint $table) {
            $table->integer('id', true, true);
            $table->unsignedInteger('response_id');
            $table->text('s3_url');
            $table->timestamps();

            $table->foreign('response_id')->references('id')->on('emlo_responses')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('ai_pdfs');
    }
}