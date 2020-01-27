<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class RenameAuthorsTableToCollaborators extends Migration
{
    public function up()
    {
        Schema::rename('authors', 'collaborators');
    }

    public function down()
    {
        Schema::rename('collaborators', 'authors');
    }
}
