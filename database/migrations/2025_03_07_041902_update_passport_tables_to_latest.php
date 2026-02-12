<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('oauth_auth_codes', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->change();
            $table->unsignedBigInteger('client_id')->change();
        });

        Schema::table('oauth_access_tokens', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->change();
            $table->unsignedBigInteger('client_id')->change();
        });

        Schema::table('oauth_clients', function (Blueprint $table) {
            $table->bigIncrements('id')->change();
            $table->unsignedBigInteger('user_id')->change()->nullable();
            $table->string('secret', 100)->nullable()->change();
        });

        Schema::table('oauth_personal_access_clients', function (Blueprint $table) {
            $table->bigIncrements('id')->change();
            $table->unsignedBigInteger('client_id')->change();
        });
    }
};
