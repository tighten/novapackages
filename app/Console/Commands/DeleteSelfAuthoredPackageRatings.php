<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use willvincent\Rateable\Rating;

class DeleteSelfAuthoredPackageRatings extends Command
{
    protected $signature = 'purge:self-authored-package-ratings';

    protected $description = 'Delete all package ratings where the rating was by the author of the package';

    public function handle()
    {
        $query = Rating::whereHasMorph('rateable', 'App\Package', function ($query) {
            return $query->whereRaw('packages.author_id = ratings.user_id');
        });

        $this->info("Deleting {$query->count()} self-authored package ratings");

        $query->delete();
    }
}
