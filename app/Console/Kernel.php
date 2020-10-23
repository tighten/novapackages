<?php

namespace App\Console;

use App\Console\Commands\CheckPackageUrls;
use App\Console\Commands\SyncPackagistData;
use App\Console\Commands\SyncRepositoryData;
use App\Console\Commands\DeleteAbandonedScreenshots;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        SyncPackagistData::class,
        DeleteAbandonedScreenshots::class,
        SyncRepositoryData::class,
        CheckPackageUrls::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('sync:packagist')->hourly();
        $schedule->command('sync:repo')->hourlyAt(30);
        $schedule->command('purge:abandonedscreenshots')->dailyAt('1:00');
        $schedule->command('telescope:prune')->daily();
        $schedule->command('check:package-urls')->weeklyOn(7, '20:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
