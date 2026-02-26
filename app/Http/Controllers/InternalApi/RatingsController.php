<?php

namespace App\Http\Controllers\InternalApi;

use App\Events\PackageRated;
use App\Exceptions\SelfAuthoredRatingException;
use App\Http\Controllers\Controller;

class RatingsController extends Controller
{
    public function store()
    {
        request()->validate([
            'package_id' => [
                'required',
                'exists:App\Models\Package,id',
            ],
            'rating' => 'required',
        ]);

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
