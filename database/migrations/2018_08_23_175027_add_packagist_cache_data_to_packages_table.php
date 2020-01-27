<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPackagistCacheDataToPackagesTable extends Migration
{
    public function up()
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->integer('packagist_downloads')->default(0);
            $table->integer('github_stars')->default(0);
        });
    }

    public function down()
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn('packagist_downloads');
            $table->dropColumn('github_stars');
        });
    }
}
