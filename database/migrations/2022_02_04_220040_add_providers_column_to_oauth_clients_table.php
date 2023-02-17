<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('oauth_clients', 'provider')) {
            Schema::table('oauth_clients', function (Blueprint $table) {
                // https://github.com/laravel/passport/blob/master/UPGRADE.md#support-for-multiple-guards
                $table->string('provider')->after('secret')->nullable();
            });
        }
    }

    public function down(): void
    {
        Schema::table('oauth_clients', function (Blueprint $table) {
            //
        });
    }
};
