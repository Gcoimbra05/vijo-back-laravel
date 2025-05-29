<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('catalogs', function (Blueprint $table) {
            $table->unsignedInteger('parent_catalog_id')->nullable()->after('id');
            $table->unsignedInteger('category_id')->nullable()->after('parent_catalog_id');
            $table->boolean('is_promotional')->default(false)->after('category_id');
            $table->boolean('is_premium')->default(false)->after('is_promotional');

            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('catalogs', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn(['category_id', 'parent_catalog_id', 'is_promotional', 'is_premium']);
        });
    }
};