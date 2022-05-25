<?php

use App\Http\Controllers\Api;
use Illuminate\Support\Facades\Route;

Route::get('search', Api\SearchController::class)->middleware('throttle:60')->name('search');
Route::get('recent', Api\RecentController::class)->middleware('throttle:60');
Route::get('popular', Api\PopularController::class)->middleware('throttle:60');
Route::get('stats', Api\StatsController::class)->middleware('throttle:60');

Route::middleware('auth:api')->group(function () {
    Route::get('packages', Api\PackagesController::class);
});
