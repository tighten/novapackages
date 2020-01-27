<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AddAvatarToAuthorsTable extends Migration
{
    public function up()
    {
        Schema::table('authors', function ($table) {
            $table->string('avatar')->nullable();
        });
    }

    public function down()
    {
        Schema::table('authors', function ($table) {
            $table->dropColumn('avatar');
        });
    }
}
