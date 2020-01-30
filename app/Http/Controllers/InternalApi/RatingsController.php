<?php

namespace App\Http\Controllers\InternalApi;

use App\Events\PackageRated;
use App\Exceptions\SelfAuthoredRatingException;
use App\Http\Controllers\Controller;
use App\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use willvincent\Rateable\Rating;

class RatingsController extends Controller
{
    public function store()
    {
        try {
            auth()->user()->ratePackage(request('package_id'), request('rating'));
        } catch (SelfAuthoredRatingException $e) {
            return response([
                'status' => 'error',
                'message' => 'A package cannot be rated by its author',
            ], 422);
        }

        event(new PackageRated(request('package_id')));

        return ['status' => 'success', 'message' => 'Rating created successfully'];
    }
}
