<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateToolsTable extends Migration
{
    public function up()
    {
        Schema::create('tools', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('author_id');
            $table->string('name');
            $table->string('type');
            $table->string('promo_url');
            $table->string('docs_url');
            $table->string('git_url');
            $table->string('picture_url');
            $table->text('description');
            $table->text('instructions');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tools');
    }
}
