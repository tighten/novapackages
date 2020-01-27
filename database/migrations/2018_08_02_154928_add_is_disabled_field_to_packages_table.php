<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsDisabledFieldToPackagesTable extends Migration
{
    public function up()
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->boolean('is_disabled')->after('instructions')->default(false);
        });
    }

    public function down()
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn('is_disabled');
        });
    }
}
