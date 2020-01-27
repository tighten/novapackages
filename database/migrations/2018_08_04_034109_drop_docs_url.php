<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropDocsUrl extends Migration
{
    public function up()
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn('docs_url');
        });
    }

    public function down()
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->string('docs_url')->nullable();
        });
    }
}
