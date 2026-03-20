<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use willvincent\Rateable\Rating;

#[Signature('purge:self-authored-package-ratings')]
#[Description('Delete all package ratings where the rating was by the author or a contributor of the package')]
class DeleteSelfAuthoredPackageRatings extends Command
{
    public function handle(): void
    {
        $this->deleteSelfAuthoredPackageRatings();
        $this->deleteSelfContributedPackageRatings();
    }

    private function deleteSelfAuthoredPackageRatings()
    {
        $query = Rating::whereHasMorph('rateable', \App\Models\Package::class, function ($query) {
            return $query
                ->join('collaborators', 'collaborators.id', '=', 'packages.author_id')
                ->whereRaw('collaborators.user_id = ratings.user_id');
        });

        $this->info("Deleting {$query->count()} self-authored package ratings");

        $query->delete();
    }

    private function deleteSelfContributedPackageRatings()
    {
        $query = Rating::whereHasMorph('rateable', \App\Models\Package::class, function ($query) {
            return $query
                ->join('collaborator_package', 'collaborator_package.package_id', '=', 'packages.id')
                ->join('collaborators', 'collaborators.id', '=', 'collaborator_package.collaborator_id')
                ->whereRaw('collaborators.user_id = ratings.user_id');
        });

        $this->info("Deleting {$query->count()} self-contributed package ratings");

        $query->delete();
    }
}
