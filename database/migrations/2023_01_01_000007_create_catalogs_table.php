<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCatalogsTable extends Migration
{
    public function up()
    {
        Schema::create('catalogs', function (Blueprint $table) {
            $table->integer('id', true, true);
            $table->string('title', 100)->nullable();
            $table->text('description')->nullable();
            $table->string('tags', 255)->nullable();
            $table->integer('min_record_time')->default(1);
            $table->integer('max_record_time')->default(30);
            $table->string('emoji', 100)->nullable();
            $table->tinyInteger('is_deleted')->default(0)->comment('0: Active, 1: Deleted')->nullable(false);
            $table->tinyInteger('status')->default(1)->comment('0: Deactivated, 1: Active, 2: Deleted, 3: Archived')->nullable(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('catalogs');
    }
}
