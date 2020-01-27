<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameCategoriesTablesToTags extends Migration
{
    public function up()
    {
        Schema::rename('categories', 'tags');
        Schema::rename('category_package', 'package_tag');

        Schema::table('package_tag', function (Blueprint $table) {
            $table->renameColumn('category_id', 'tag_id');
        });
    }

    public function down()
    {
        Schema::rename('tags', 'categories');
        Schema::rename('package_tag', 'category_package');

        Schema::table('category_package', function (Blueprint $table) {
            $table->renameColumn('tag_id', 'category_id');
        });
    }
}
