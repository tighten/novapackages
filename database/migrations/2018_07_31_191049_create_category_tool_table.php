<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('category_tool', function (Blueprint $table) {
            $table->unsignedInteger('category_id');
            $table->unsignedInteger('tool_id');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::drop('category_tool');
    }
};
