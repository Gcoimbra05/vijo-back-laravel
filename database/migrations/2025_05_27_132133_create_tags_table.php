<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('category_id');
            $table->string('name', 100)->nullable();
            $table->text('description')->nullable();
            $table->enum('type', ['catalog', 'journalTag', 'custom'])->default('catalog');
            $table->unsignedInteger('created_by_user')->nullable()->comment('User who created the tag');
            $table->tinyInteger('status')->default(1)->comment('0: Deactived, 1: Active, 2: Deleted, 3: Archieved');
            $table->timestamps();

            // Foreign key
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('created_by_user')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tags');
    }
};