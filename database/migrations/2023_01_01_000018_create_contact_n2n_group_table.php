<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactN2nGroupTable extends Migration
{
    public function up()
    {
        Schema::create('contact_n2n_group', function (Blueprint $table) {
            $table->unsignedInteger('contact_id');
            $table->unsignedInteger('group_id');
            $table->primary(['contact_id', 'group_id']);
            $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('group_id')->references('id')->on('contact_groups')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('contact_n2n_group');
    }
}