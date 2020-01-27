<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemovePasswordFromUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('password');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('password');
        });
    }
}
