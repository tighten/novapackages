<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRepoUrlFieldToPackagesTable extends Migration
{
    public function up()
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->string('repo_url')
                ->nullable()
                ->default(null)
                ->after('composer_name');
            $table->string('readme_source')
                ->nullable()
                ->default(null)
                ->after('repo_url');
            $table->mediumText('readme')
                ->nullable()
                ->default(null)
                ->after('readme_source');
            $table->text('description')
                ->nullable()
                ->default(null)
                ->change();
        });
    }

    public function down()
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn('repo_url');
            $table->dropColumn('readme_source');
            $table->dropColumn('readme');
        });
    }
}
