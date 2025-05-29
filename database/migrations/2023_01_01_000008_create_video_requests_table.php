<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVideoRequestsTable extends Migration
{
    public function up()
    {
        Schema::create('video_requests', function (Blueprint $table) {
            $table->integer('id', true, true);
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('catalog_id');
            $table->unsignedInteger('ref_user_id')->nullable();
            $table->string('ref_first_name', 100)->nullable();
            $table->string('ref_last_name', 100)->nullable();
            $table->integer('ref_country_code')->nullable();
            $table->string('ref_mobile', 20)->nullable();
            $table->string('ref_email', 200)->nullable();
            $table->tinyInteger('status')->default(1)->comment('0: Canceled, 1: Active, 2: Denied, 3: Completed')->nullable(false);
            $table->timestamps();

            // Define foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('catalog_id')->references('id')->on('catalogs')->onDelete('cascade');
            $table->foreign('ref_user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('video_requests');
    }
}
