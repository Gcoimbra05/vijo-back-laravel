<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactsTable extends Migration
{
    public function up()
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->integer('id', true, true);
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('group_id')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->integer('country_code')->nullable();
            $table->string('mobile', 20)->nullable();
            $table->string('email', 200)->nullable();
            $table->tinyInteger('status')->default(1)->comment('0: Deactivated, 1: Active, 2: Deleted');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('contacts');
    }
}