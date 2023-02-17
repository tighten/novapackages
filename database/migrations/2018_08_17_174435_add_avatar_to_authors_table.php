<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('authors', function ($table) {
            $table->string('avatar')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('authors', function ($table) {
            $table->dropColumn('avatar');
        });
    }
};
