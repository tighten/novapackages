<?php

namespace App\Http\Controllers\InternalApi;

use App\Http\Controllers\Controller;
use App\Package;
use App\Review;
use willvincent\Rateable\Rating;

class ReviewsController extends Controller
{
    public function store()
    {
        request()->validate([
            'package_id' => [
                'required',
                'exists:App\Package,id',
            ],
            'review' => 'required',
        ]);

        $ratingId = Rating::query()
            ->where('rateable_id', request('package_id'))
            ->where('user_id', auth()->id())
            ->value('id');

        Package::findOrFail(request('package_id'))->addReview($ratingId, request('review'));

        return ['status' => 'success', 'message' => 'Review created successfully'];
    }

    public function update()
    {
        request()->validate([
            'package_id' => [
                'required',
                'exists:App\Package,id',
            ],
            'review' => 'required',
        ]);

        Package::findOrFail(request('package_id'))->updateReview(request('review'));

        return ['status' => 'success', 'message' => 'Review edited successfully'];
    }

    public function destroy(Review $review)
    {
        $review->delete();

        return response('Success', 200);
    }
}
