<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddComposerStringToToolsTable extends Migration
{
    public function up()
    {
        Schema::table('tools', function (Blueprint $table) {
            $table->string('composer_name')->nullable();
        });
    }

    public function down()
    {
        Schema::table('tools', function (Blueprint $table) {
            $table->dropColumn('composer_name');
        });
    }
}
