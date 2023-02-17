<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('authors', 'collaborators');
    }

    public function down(): void
    {
        Schema::rename('collaborators', 'authors');
    }
};
