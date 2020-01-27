<?php

namespace App\Policies;

use App\Review;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReviewPolicy
{
    use HandlesAuthorization;

    public function delete(User $user, Review $review)
    {
        return $this->userIsAdminOrReviewAuthor($user, $review);
    }

    protected function userIsAdminOrReviewAuthor($user, $review)
    {
        return $user->isAdmin() || $this->userIsReviewAuthor($user, $review);
    }

    protected function userIsReviewAuthor($user, $review)
    {
        return $user->id == $review->user->id;
    }
}
