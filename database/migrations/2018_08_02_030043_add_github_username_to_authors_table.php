<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGithubUsernameToAuthorsTable extends Migration
{
    public function up()
    {
        Schema::table('authors', function (Blueprint $table) {
            $table->string('github_username')->nullable();
        });
    }

    public function down()
    {
        Schema::table('authors', function (Blueprint $table) {
            $table->dropColumn('github_username');
        });
    }
}
