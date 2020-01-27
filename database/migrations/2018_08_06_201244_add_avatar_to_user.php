<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AddAvatarToUser extends Migration
{
    public function up()
    {
        Schema::table('users', function ($table) {
            $table->string('avatar')->nullable();
        });
    }

    public function down()
    {
        Schema::table('users', function ($table) {
            $table->dropColumn('avatar');
        });
    }
}
