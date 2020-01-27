<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSubmitterIdToPackagesTable extends Migration
{
    public function up()
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->unsignedInteger('submitter_id')->default(null)->nullable();
            $table->foreign('submitter_id')->references('id')->on('users');
        });
    }

    public function down()
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropForeign('packages_submitter_id_foreign');
            $table->dropIndex('packages_submitter_id_index');
            $table->dropColumn('submitter_id');
        });
    }
}
