<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoryToolTable extends Migration
{
    public function up()
    {
        Schema::create('category_tool', function (Blueprint $table) {
            $table->unsignedInteger('category_id');
            $table->unsignedInteger('tool_id');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('category_tool');
    }
}
