<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAbstractToPackagesTable extends Migration
{
    public function up()
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->string('abstract')->nullable();
        });
    }

    public function down()
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn('abstract');
        });
    }
}
