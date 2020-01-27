<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AllowNullPackageInstructions extends Migration
{
    public function up()
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->text('instructions')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->text('instructions')->change();
        });
    }
}
