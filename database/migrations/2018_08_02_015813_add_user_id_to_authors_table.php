<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserIdToAuthorsTable extends Migration
{
    public function up()
    {
        Schema::table('authors', function (Blueprint $table) {
            $table->unsignedInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    public function down()
    {
        Schema::table('authors', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
    }
}
