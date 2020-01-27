<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AddCollaboratorPackagePivotTable extends Migration
{
    public function up()
    {
        Schema::create('collaborator_package', function ($table) {
            $table->increments('id');
            $table->integer('collaborator_id')->unsigned();
            $table->integer('package_id')->unsigned();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('collaborator_package');
    }
}
