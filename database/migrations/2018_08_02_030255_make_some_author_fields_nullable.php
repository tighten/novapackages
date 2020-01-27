<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeSomeAuthorFieldsNullable extends Migration
{
    public function up()
    {
        Schema::table('authors', function (Blueprint $table) {
            $table->string('url')->nullable()->change();
            $table->text('description')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('authors', function (Blueprint $table) {
            $table->string('url')->change();
            $table->text('description')->change();
        });
    }
}
