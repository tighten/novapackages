<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('collaborator_package', function ($table) {
            $table->increments('id');
            $table->integer('collaborator_id')->unsigned();
            $table->integer('package_id')->unsigned();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('collaborator_package');
    }
};
