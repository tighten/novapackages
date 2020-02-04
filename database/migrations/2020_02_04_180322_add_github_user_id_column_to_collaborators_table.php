<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGithubUserIdColumnToCollaboratorsTable extends Migration
{
    public function up()
    {
        Schema::table('collaborators', function (Blueprint $table) {
            $table->integer('github_user_id')->after('github_username')->nullable();
        });
    }

    public function down()
    {
        Schema::table('collaborators', function (Blueprint $table) {
            $table->dropColumn('github_user_id');
        });
    }
}
