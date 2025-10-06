<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('trusted_devices', function (Blueprint $table) {
            $table->integer('id', true, true);
            $table->unsignedInteger('user_id');
            $table->string('device_token'); // hash/fingerprint of the device
            $table->string('device_name')->nullable(); // optional: device name
            $table->string('user_agent')->nullable(); // optional: user agent
            $table->timestamp('trusted_until')->nullable(); // validity of the trusted device
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['user_id', 'device_token']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('trusted_devices');
    }
};