<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLatestVersionColumnToPackagesTable extends Migration
{
    public function up()
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->string('latest_version')->nullable();
        });
    }

    public function down()
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn('latest_version');
        });
    }
}
