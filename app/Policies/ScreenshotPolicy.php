<?php

namespace App\Policies;

use App\Policies\HandlesPackageAuthorizationTrait;
use App\Screenshot;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ScreenshotPolicy
{
    use HandlesAuthorization, HandlesPackageAuthorizationTrait;

    public function delete(User $user, Screenshot $screenshot)
    {
        if ($screenshot->uploader->is($user)
            || ($screenshot->hasPackage() && $this->userIsAdminOrAuthorOrCollaboratorOrUnclaimedSubmitter($user, $screenshot->package))
        ) {
            return true;
        }

        return false;
    }
}
