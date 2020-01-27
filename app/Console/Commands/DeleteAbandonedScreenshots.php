<?php

namespace App\Console\Commands;

use App\Jobs\DeleteAbandonedScreenshots as DeleteAbandonedScreenshotsJob;
use Illuminate\Console\Command;

class DeleteAbandonedScreenshots extends Command
{
    protected $signature = 'purge:abandonedscreenshots';

    protected $description = 'Delete all screenshots that are older than 24 hours and not associated with a package';

    public function handle()
    {
        dispatch(new DeleteAbandonedScreenshotsJob);
    }
}
