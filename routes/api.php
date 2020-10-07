<?php

use Illuminate\Support\Facades\Route;

Route::get('search', 'Api\SearchController')->middleware('throttle:60')->name('search');
Route::get('recent', 'Api\RecentController')->middleware('throttle:60');
Route::get('popular', 'Api\PopularController')->middleware('throttle:60');
Route::get('stats', 'Api\StatsController')->middleware('throttle:60');

Route::middleware('auth:api')->group(function () {
    Route::get('packages', 'Api\PackagesController');
});
