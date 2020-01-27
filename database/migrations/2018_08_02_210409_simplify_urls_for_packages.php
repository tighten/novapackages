<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SimplifyUrlsForPackages extends Migration
{
    public function up()
    {
        // Friggin' SQLite.
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn('git_url');
        });

        Schema::table('packages', function (Blueprint $table) {
            $table->renameColumn('promo_url', 'url');
        });

        Schema::table('packages', function (Blueprint $table) {
            $table->string('docs_url')->nullable()->change();
        });

        Schema::table('packages', function (Blueprint $table) {
            $table->string('picture_url')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->string('git_url');
            $table->renameColumn('url', 'promo_url');
            $table->string('docs_url')->change();
            $table->string('picture_url')->change();
        });
    }
}
