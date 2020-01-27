<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropToolTypeColumn extends Migration
{
    public function up()
    {
        Schema::table('tools', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }

    public function down()
    {
        Schema::table('tools', function (Blueprint $table) {
            $table->string('type');
        });
    }
}
