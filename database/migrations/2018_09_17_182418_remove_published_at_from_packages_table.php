<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemovePublishedAtFromPackagesTable extends Migration
{
    public function up()
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn('published_at');
        });
    }

    public function down()
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->timestamp('published_at')->nullable()->after('updated_at');
        });
    }
}
