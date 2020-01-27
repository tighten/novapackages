<?php

namespace App\Http\Controllers\InternalApi;

use App\Events\PackageRated;
use App\Http\Controllers\Controller;
use App\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use willvincent\Rateable\Rating;

class RatingsController extends Controller
{
    public function store()
    {
        auth()->user()->ratePackage(request('package_id'), request('rating'));

        event(new PackageRated(request('package_id')));

        return ['status' => 'success', 'message' => 'Rating created successfully'];
    }
}
