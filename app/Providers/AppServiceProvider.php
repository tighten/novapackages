<?php

namespace App\Providers;

use App\Models\Package;
use App\Tighten;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/';

    public function boot(): void
    {
        Relation::morphMap([
            'App\Package' => \App\Models\Package::class,
        ]);

        Passport::$clientUuids = false;
        Passport::$registersJsonApiRoutes = true;

        Event::listen(
            \App\Events\CollaboratorClaimed::class,
            [\App\Notifications\CollaboratorClaimed::class, 'handle']
        );

        Blade::directive('og', function ($expression) {
            [$property, $content] = explode(',', $expression, 2);

            return "<?php echo '<meta property=\"og:' . {$property} . '\" content=\"' . {$content} . '\">' . \"\n\"; ?>";
        });

        $this->bootRoute();
    }

    public function register(): void
    {
        $this->app->singleton(Tighten::class, function () {
            return new Tighten;
        });
    }

    public function bootRoute(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60);
        });

        Route::bind('any_package', function ($id) {

            if (auth()->user()?->isAdmin()) {
                return Package::withoutGlobalScope('notDisabled')->findOrFail($id);
            }

            if (auth()->user()?->isPackageAuthor($id)) {
                return Package::withoutGlobalScope('notDisabled')->findOrFail($id);
            }

            return Package::findOrFail($id);
        });
    }
}
