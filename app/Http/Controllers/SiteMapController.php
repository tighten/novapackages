<?php

namespace App\Http\Controllers;

use App\Package;
use Carbon\Carbon;
use Laravelium\Sitemap\Sitemap;

class SiteMapController extends Controller
{
    public function __invoke(Sitemap $sitemap)
    {
        // set cache key (string), duration in minutes (Carbon|Datetime|int), turn on/off (boolean)
        $sitemap->setCache('laravel.sitemap', 60);

        // check if there is cached sitemap and build new only if is not
        if (! $sitemap->isCached()) {
            $now = Carbon::now()->format('c');
            $sitemap->add(url('/'), $now, '1.0', 'daily');

            $packages = Package::orderBy('created_at', 'desc')->get();

            foreach ($packages as $package) {
                $sitemap->add(
                    route('packages.show', [$package->composer_vendor, $package->composer_package]),
                    $package->updated_at,
                    '1.0',
                    'daily'
                );
            }
        }

        // show your sitemap (options: 'xml' (default), 'html', 'txt', 'ror-rss', 'ror-rdf')
        return $sitemap->render('xml');
    }
}
