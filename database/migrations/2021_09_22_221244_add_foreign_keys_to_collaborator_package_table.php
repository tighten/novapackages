<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('collaborator_package', function (Blueprint $table) {
            $table->foreign('package_id')->references('id')->on('packages');
            $table->foreign('collaborator_id')->references('id')->on('collaborators');
        });
    }

    public function down(): void
    {
        Schema::table('collaborator_package', function (Blueprint $table) {
            $table->dropForeign([
                'package_id',
                'collaborator_id',
            ]);
        });
    }
};
