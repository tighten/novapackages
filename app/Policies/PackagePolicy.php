<?php

namespace App\Policies;

use App\Models\Package;
use App\Policies\HandlesPackageAuthorizationTrait;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PackagePolicy
{
    use HandlesAuthorization, HandlesPackageAuthorizationTrait;

    /**
     * Can't rely on this via middleware until 5.7 because it forces a login
     * calling directly from the controller for now.
     */
    public function show(User $user, Package $package)
    {
        return $this->userIsAdminOrAuthorOrCollaboratorOrUnclaimedSubmitter($user, $package);
    }

    public function update(User $user, Package $package): bool
    {
        return $this->userIsAdminOrAuthorOrCollaboratorOrUnclaimedSubmitter($user, $package);
    }

    public function delete(User $user, Package $package): bool
    {
        return $this->userIsAdminOrAuthorOrCollaboratorOrUnclaimedSubmitter($user, $package);
    }
}
