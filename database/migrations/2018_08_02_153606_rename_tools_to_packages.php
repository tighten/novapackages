<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('tools', 'packages');
        Schema::rename('category_tool', 'category_package');

        Schema::table('category_package', function (Blueprint $table) {
            $table->renameColumn('tool_id', 'package_id');
        });
    }

    public function down(): void
    {
        Schema::rename('packages', 'tools');
        Schema::rename('category_package', 'category_tool');

        Schema::table('category_tool', function (Blueprint $table) {
            $table->renameColumn('package_id', 'tool_id');
        });
    }
};
