<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProvidersColumnToOauthClientsTable extends Migration
{
    public function up()
    {
        if (! Schema::hasColumn('oauth_clients', 'provider')) {
            Schema::table('oauth_clients', function (Blueprint $table) {
                // https://github.com/laravel/passport/blob/master/UPGRADE.md#support-for-multiple-guards
                $table->string('provider')->after('secret')->nullable();
            });
        }
    }

    public function down()
    {
        Schema::table('oauth_clients', function (Blueprint $table) {
            //
        });
    }
}
