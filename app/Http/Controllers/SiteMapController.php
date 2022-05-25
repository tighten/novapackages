<?php

namespace App\Http\Controllers;

use App\Package;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class SiteMapController extends Controller
{
    public function __invoke()
    {
        return cache()->remember('sitemap', now()->addHour(), function () {
            $sitemap = Sitemap::create();

            foreach (Package::orderBy('created_at', 'desc')->get() as $package) {
                $sitemap->add(
                    Url::create(route('packages.show', [$package->composer_vendor, $package->composer_package]))
                        ->setLastModificationDate($package->updated_at)
                        ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                        ->setPriority(1.0)
                );
            }

            return $sitemap;
        });
    }
}
