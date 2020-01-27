<?php

namespace App\Providers;

use App\Tighten;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Blade::directive('og', function ($expression) {
            list($property, $content) = explode(',', $expression, 2);

            return "<?php echo '<meta property=\"og:' . $property . '\" content=\"' . $content . '\">' . \"\n\"; ?>";
        });
    }

    public function register()
    {
        $this->app->singleton(Tighten::class, function () {
            return new Tighten;
        });
    }
}
