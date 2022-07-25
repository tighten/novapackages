<?php

use App\Http\Controllers\DisablePackageController;
use App\Http\Controllers\EnablePackageController;
use App\Http\Controllers\PackageIdeaController;
use App\Http\Controllers\SiteMapController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\App;
use App\Http\Controllers\Auth;
use App\Http\Controllers\CollaboratorController;
use App\Http\Controllers\InternalApi;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\PackageReviewController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PackageController::class, 'index'])->name('home');
Route::get('packages/{namespace}/{name}', [PackageController::class, 'show'])->name('packages.show');
Route::get('packages/{package}', [PackageController::class, 'showId'])->name('packages.show-id');

Route::middleware('auth')->group(function () {
    Route::get('packages/{namespace}/{name}/reviews/create', [PackageReviewController::class, 'create'])->name('reviews.create');
});

Route::get('collaborators/{collaborator}', [CollaboratorController::class, 'show'])->name('collaborators.show');

Route::get('stats', StatsController::class)->name('stats');
Route::get('package-ideas', PackageIdeaController::class)->name('package-ideas');

Route::get('login/github', [Auth\LoginController::class, 'redirectToProvider'])->name('login');
Route::get('login/github/callback', [Auth\LoginController::class, 'handleProviderCallback']);

Route::get('sitemap.xml', SiteMapController::class);
Route::feeds();

Route::post('logout', [Auth\LoginController::class, 'logout'])->name('logout');

Route::prefix('app/email')->middleware('auth')->name('app.email.')->group(function () {
    Route::get('', [App\EmailController::class, 'create'])->name('create');
    Route::post('', [App\EmailController::class, 'store'])->name('store');
});

Route::prefix('app')->middleware('auth', 'email')->name('app.')->group(function () {
    Route::post('screenshot-uploads', [App\ScreenshotUploadController::class, 'store'])->name('screenshot-uploads.store');
    Route::middleware(['can:delete,screenshot'])->group(function () {
        Route::delete('screenshot-uploads/{screenshot}', [App\ScreenshotUploadController::class, 'destroy'])->name('screenshot-uploads.destroy');
    });

    Route::prefix('packages')->name('packages.')->group(function () {
        Route::get('/', [App\PackageController::class, 'index'])->name('index');
        Route::get('create', [App\PackageController::class, 'create'])->name('create');
        Route::post('/', [App\PackageController::class, 'store'])->name('store');

        Route::middleware(['can:update,any_package'])->group(function () {
            Route::put('{any_package}', [App\PackageController::class, 'update'])->name('update');
            Route::get('{any_package}/edit', [App\PackageController::class, 'edit'])->name('edit');
            Route::delete('{any_package}/packagist-cache', [App\PackagePackagistCacheController::class, 'destroy'])->name('packagistcache.destroy');
            Route::post('{any_package}/repository-refresh', App\RefreshPackageRepositoryController::class)->name('repository.refresh');
        });
    });

    Route::prefix('collaborators')->name('collaborators.')->group(function () {
        Route::get('/', [App\CollaboratorController::class, 'index'])->name('index');
        Route::get('create', [App\CollaboratorController::class, 'create'])->name('create');
        Route::post('/', [App\CollaboratorController::class, 'store'])->name('store');
        Route::get('{collaborator}/edit', [App\CollaboratorController::class, 'edit'])->name('edit')->middleware('claimed');
        Route::patch('{collaborator}', [App\CollaboratorController::class, 'update'])->name('update')->middleware('claimed');

        Route::get('{collaborator}/claim', [App\CollaboratorClaimController::class, 'create'])->name('claims.create');
        Route::post('{collaborator}/claim', [App\CollaboratorClaimController::class, 'store'])->name('claims.store');
    });

    Route::prefix('admin')->middleware('role:admin')->name('admin.')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('index');
        Route::get('disable-package/{any_package}', DisablePackageController::class)->name('disable-package');
        Route::get('enable-package/{any_package}', EnablePackageController::class)->name('enable-package');
    });

    Route::get('api', App\ApiDetailsController::class)->name('api_details');
});

/* Internal API */
Route::prefix('internalapi')->name('internalapi.')->middleware('auth')->group(function () {
    Route::prefix('ratings')->name('ratings.')->group(function () {
        Route::post('', [InternalApi\RatingsController::class, 'store'])->name('store');
    });

    Route::prefix('reviews')->name('reviews.')->group(function () {
        Route::post('/', [InternalApi\ReviewsController::class, 'store'])->name('store');
        Route::patch('/', [InternalApi\ReviewsController::class, 'update'])->name('update');
        Route::middleware(['can:delete,review'])->group(function () {
            Route::delete('{review}', [InternalApi\ReviewsController::class, 'destroy'])->name('delete');
        });
    });

    Route::prefix('packages/{package}/favorites')->name('package.favorites.')->group(function () {
        Route::post('/', [InternalApi\PackageFavoritesController::class, 'store'])->name('store');
        Route::delete('/', [InternalApi\PackageFavoritesController::class, 'destroy'])->name('destroy');
    });
});
