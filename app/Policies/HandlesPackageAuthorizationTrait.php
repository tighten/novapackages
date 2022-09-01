<?php

namespace App\Policies;

use Illuminate\Support\Facades\DB;

trait HandlesPackageAuthorizationTrait
{
    protected function userIsAdminOrAuthorOrCollaboratorOrUnclaimedSubmitter($user, $package)
    {
        return $user->isAdmin()
            || $this->userIsPackageAuthor($user, $package)
            || $this->userIsPackageCollaborator($user, $package)
            || $this->userIsUnclaimedPackageSubmitter($user, $package);
    }

    protected function userIsPackageAuthor($user, $package)
    {
        return $user->id === $package->author->user_id;
    }

    protected function userIsPackageCollaborator($user, $package)
    {
        // See if any of this user's collaborators are contributors on this package
        $collaboratorIds = $user->collaborators()->pluck('id');
        $packageContributorIds = DB::table('collaborator_package')
            ->where('package_id', $package->id)
            ->pluck('collaborator_id')
            ->toArray();

        return count(array_intersect($collaboratorIds->toArray(), $packageContributorIds)) > 0;
    }

    protected function userIsUnclaimedPackageSubmitter($user, $package)
    {
        return ((int) $user->id === (int) $package->submitter_id) && (! $package->authorIsUser());
    }
}
