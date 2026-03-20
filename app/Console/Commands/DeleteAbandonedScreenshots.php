<?php

namespace App\Console\Commands;

use App\Jobs\DeleteAbandonedScreenshots as DeleteAbandonedScreenshotsJob;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('purge:abandonedscreenshots')]
#[Description('Delete all screenshots that are older than 24 hours and not associated with a package')]
class DeleteAbandonedScreenshots extends Command
{
    public function handle(): void
    {
        dispatch(new DeleteAbandonedScreenshotsJob);
    }
}
