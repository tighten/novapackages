<?php

use App\Console\Commands\DeleteAbandonedScreenshots;
use App\Console\Commands\SyncPackagistData;
use App\Console\Commands\SyncRepositoryData;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command(SyncPackagistData::class)->everyTwoHours();
// Every two hours at minute 30.
Schedule::command(SyncRepositoryData::class)->cron('30 */2 * * *');
Schedule::command(DeleteAbandonedScreenshots::class)->dailyAt('1:00');
// Schedule::command('novapackages:check-package-urls')->weeklyOn(7, '20:00');
// Schedule::command('novapackages:send-unavailable-package-followup')->dailyAt('21:00');
// Schedule::command('novapackages:disable-unavailable-packages')->dailyAt('21:30');
