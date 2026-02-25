<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('password_resets', 'password_reset_tokens');
    }

    public function down(): void
    {
        Schema::rename('password_reset_tokens', 'password_resets');
    }
};
