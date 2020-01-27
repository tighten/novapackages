<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScreenshotsTable extends Migration
{
    public function up()
    {
        Schema::create('screenshots', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('uploader_id');
            $table->unsignedInteger('package_id')->nullable();
            $table->string('path');
            $table->timestamps();

            $table->foreign('uploader_id')->references('id')->on('users');
            $table->foreign('package_id')->references('id')->on('packages');
        });
    }

    public function down()
    {
        Schema::dropIfExists('screenshots');
    }
}
