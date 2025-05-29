<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->integer('id', true, true);
            $table->unsignedInteger('plan_id')->nullable();
            $table->string('first_name', 100)->nullable();
            $table->string('last_name', 100)->nullable();
            $table->string('email', 255)->unique()->nullable(false);
            $table->string('password', 255)->nullable(false);
            $table->string('country_code', 10)->nullable();
            $table->string('mobile', 20)->nullable();
            $table->tinyInteger('guided_tours')->default(0)->comment('0: pending, 1: completed');
            $table->dateTime('last_login_date')->nullable();
            $table->tinyInteger('status')->default(1)->comment('0: Deactivated, 1: Active, 2: Deleted, 3: Archived')->nullable(false);
            $table->tinyInteger('is_verified')->default(0)->comment('0: not verified, 1: verified');
            $table->dateTime('plan_start_date')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Define a foreign key
            $table->foreign('plan_id')->references('id')->on('membership_plans')->onDelete('set null')->onUpdate('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
}