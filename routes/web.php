<?php

use Illuminate\Support\Facades\Route;

Route::get('/', 'PackageController@index')->name('home');
Route::get('packages/{namespace}/{name}', 'PackageController@show')->name('packages.show');
Route::get('packages/{package}', 'PackageController@showId')->name('packages.show-id');

Route::middleware('auth')->group(function () {
    Route::get('packages/{namespace}/{name}/reviews/create', 'PackageReviewController@create')->name('reviews.create');
});

Route::get('collaborators/{collaborator}', 'CollaboratorController@show')->name('collaborators.show');

Route::get('stats', 'StatsController')->name('stats');
Route::get('package-ideas', 'PackageIdeaController')->name('package-ideas');

Route::get('login/github', 'Auth\LoginController@redirectToProvider')->name('login');
Route::get('login/github/callback', 'Auth\LoginController@handleProviderCallback');

Route::get('sitemap.xml', 'SiteMapController');
Route::feeds();

Route::post('logout', 'Auth\LoginController@logout')->name('logout');

Route::prefix('app/email')->middleware('auth')->name('app.email.')->group(function () {
    Route::get('', 'App\EmailController@create')->name('create');
    Route::post('', 'App\EmailController@store')->name('store');
});

Route::prefix('app')->middleware('auth', 'email')->name('app.')->group(function () {
    Route::post('screenshot-uploads', 'App\ScreenshotUploadController@store')->name('screenshot-uploads.store');
    Route::middleware(['can:delete,screenshot'])->group(function () {
        Route::delete('screenshot-uploads/{screenshot}', 'App\ScreenshotUploadController@destroy')->name('screenshot-uploads.destroy');
    });

    Route::prefix('packages')->name('packages.')->group(function () {
        Route::get('/', 'App\PackageController@index')->name('index');
        Route::get('create', 'App\PackageController@create')->name('create');
        Route::post('/', 'App\PackageController@store')->name('store');

        Route::middleware(['can:update,any_package'])->group(function () {
            Route::put('{any_package}', 'App\PackageController@update')->name('update');
            Route::get('{any_package}/edit', 'App\PackageController@edit')->name('edit');
            Route::delete('{any_package}/packagist-cache', 'App\PackagePackagistCacheController@destroy')->name('packagistcache.destroy');
            Route::post('{any_package}/repository-refresh', 'App\RefreshPackageRepositoryController')->name('repository.refresh');
        });
    });

    Route::prefix('collaborators')->name('collaborators.')->group(function () {
        Route::get('/', 'App\CollaboratorController@index')->name('index');
        Route::get('create', 'App\CollaboratorController@create')->name('create');
        Route::post('/', 'App\CollaboratorController@store')->name('store');
        Route::get('{collaborator}/edit', 'App\CollaboratorController@edit')->name('edit')->middleware('claimed');
        Route::patch('{collaborator}', 'App\CollaboratorController@update')->name('update')->middleware('claimed');

        Route::get('{collaborator}/claim', 'App\CollaboratorClaimController@create')->name('claims.create');
        Route::post('{collaborator}/claim', 'App\CollaboratorClaimController@store')->name('claims.store');
    });

    Route::prefix('admin')->middleware('role:admin')->name('admin.')->group(function () {
        Route::get('/', 'AdminController@index')->name('index');
        Route::get('disable-package/{any_package}', 'DisablePackageController')->name('disable-package');
        Route::get('enable-package/{any_package}', 'EnablePackageController')->name('enable-package');
    });

    Route::get('api', 'App\ApiDetailsController')->name('api_details');
});

/* Internal API */
Route::prefix('internalapi')->name('internalapi.')->middleware('auth')->group(function () {
    Route::prefix('ratings')->name('ratings.')->group(function () {
        Route::post('', 'InternalApi\RatingsController@store')->name('store');
    });

    Route::prefix('reviews')->name('reviews.')->group(function () {
        Route::post('/', 'InternalApi\ReviewsController@store')->name('store');
        Route::patch('/', 'InternalApi\ReviewsController@update')->name('update');
        Route::middleware(['can:delete,review'])->group(function () {
            Route::delete('{review}', 'InternalApi\ReviewsController@destroy')->name('delete');
        });
    });

    Route::prefix('packages/{package}/favorites')->name('package.favorites.')->group(function () {
        Route::post('/', 'InternalApi\PackageFavoritesController@store')->name('store');
        Route::delete('/', 'InternalApi\PackageFavoritesController@destroy')->name('destroy');
    });
});
