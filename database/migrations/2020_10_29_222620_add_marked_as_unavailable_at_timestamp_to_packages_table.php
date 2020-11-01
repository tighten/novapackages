<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMarkedAsUnavailableAtTimestampToPackagesTable extends Migration
{

    public function up()
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->datetime('marked_as_unavailable_at')->after('instructions')->nullable();
        });
    }

    public function down()
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn('marked_as_unavailable_at');
        });
    }
}
