<?php

use App\Collaborator;
use App\Package;
use App\Tag;
use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        factory(User::class, 500)->create();

        foreach (Tag::PROJECT_TYPES as $name) {
            Tag::create(['name' => $name, 'slug' => Str::slug($name)]);
        }

        // non-type tags
        foreach (collect(['chart', 'form', 'nova']) as $name) {
            Tag::create(['name' => $name, 'slug' => Str::slug($name)]);
        }

        $packages = [
            null,
            factory(Package::class)->make([
                'name' => 'Stripe Dashboard',
                'abstract' => 'A Nova tool that shows you high-level information about your Stripe charges and balance. https://www.thisisalongurl.com/with/sub/directories/that/should/break-width',
                'composer_name' => 'tightenco/nova-stripe',
                'url' => 'https://github.com/tighten/nova-stripe',
                'readme_source' => 'github',
                'readme_format' => 'md',
                'readme' => "# Laravel Nova Stripe Dashboard\n\nThis package makes it easy to see high-level information about your application's [Stripe](https://stripe.com/) balance and charges in a Nova dashboard.\n\nLooking to manage your users' Stripe subscriptions with [Laravel Cashier](https://github.com/laravel/cashier)? Check out [Nova Cashier Manager](https://novapackages.com/packages/themsaid/nova-cashier-manager).\n\n**This package is in alpha and under very active development, but check out the to-do section below for features we plan to add soon!**\n\n![Dashboard index page](charges-index.png)\n\n![Charge detail page](charges-detail.png)\n\n### Installation Instructions\n\nInstall via [Composer](https://getcomposer.org/):\n\n`$ composer require tightenco/nova-stripe`\n\nIf you have not already done so, in your `config/services.php` file, set up your Stripe key/secret:\n\n```php\n'stripe' => [\n    'key' => env('STRIPE_KEY'),\n    'secret' => env('STRIPE_SECRET'),\n],\n```\n\nand add these values to your `.env` file:\n\n```\nSTRIPE_KEY=\nSTRIPE_SECRET=\n```\n\nFrom there, you can register your tools in `app/Providers/NovaServiceProvider`:\n\n```php\npublic function tools()\n{\n    return [\n        new \\Tightenco\\NovaStripe\\NovaStripe,\n    ];\n}\n```\n\n### Alpha To-Dos\n\n#### Charges Index\n\n- [X] Improve balance card design\n- [ ] Add ability to filter by livemode\n- [ ] Add ability to filter by status\n- [ ] Add ability to sort fields\n- [ ] Add perPage dropdown\n- [ ] Add ability to search charges\n\n#### Charge Detail\n\n- [X] Calculate Stripe processing fee / net amount\n- [ ] Add \"Refund\" button\n- [X] Better handling of booleans (green dot like regular Nova Boolean)\n- [ ] Labels for statuses\n- [ ] Handle Metadata more like a Textarea field\n- [X] Refactor to use existing Nova fields instead of a bespoke one\n\n#### Customers\n\n- [ ] Add an index of customers\n- [ ] Add a customer detail page\n\n#### General Housekeeping\n\n- [ ] Add some PHPUnit tests\n- [ ] Add some Dusk tests\n- [X] Better handling of currency symbols\n- [ ] Break navbar item into any applicable sub-items (Charges, Customers, etc.)\n\n### Possible Beta To-Dos\n\n- [ ] Add some pretty graphs showing earnings\n- [ ] Investigate creating pseudo-resources (not relying on actual Laravel models) in order to use less custom/hard-coded code\n- [ ] Add ability to update charge\n- [ ] Add Payout information\n- [ ] Add Stripe Connect account management\n- [ ] Build cards / resource tools that can be used in Nova resources (User / Transaction / etc.?)\n- [ ] Better integration with Cashier\n",
            ]),
            factory(Package::class)->make([
                'name' => 'Nova Releases',
                'abstract' => null,
                'composer_name' => 'tightenco/nova-releases',
                'url' => 'https://github.com/tightenco/nova-releases',
                'readme_source' => 'github',
                'readme_format' => 'md',
                'readme' => "# Keep up on Nova releases\n\n[![Latest Version on Packagist](https://img.shields.io/packagist/v/tightenco/nova-releases.svg?style=flat-square)](https://packagist.org/packages/tightenco/nova-releases)\n[![Total Downloads](https://img.shields.io/packagist/dt/tightenco/nova-releases.svg?style=flat-square)](https://packagist.org/packages/tightenco/nova-releases)\n\nJUST GETTING STARTED.\n\nThe card:\n\n<img width=\"396\" alt=\"screen shot 2018-09-06 at 12 13 19 am\" src=\"https://user-images.githubusercontent.com/151829/45134868-a18fb680-b16a-11e8-98c4-f5583c6009da.png\">\n<img width=\"398\" alt=\"screen shot 2018-09-06 at 12 13 45 am\" src=\"https://user-images.githubusercontent.com/151829/45134870-a18fb680-b16a-11e8-8192-4e08ce1f0524.png\">\n\nThe tool:\n\n<img width=\"1256\" alt=\"screen shot 2018-09-10 at 12 07 39 am\" src=\"https://user-images.githubusercontent.com/151829/45276011-e62e9100-b48d-11e8-8217-481f999f9521.png\">\n\n\nPlans:\n\n- [x] Show the latest release on a card and compare it against yours\n- [x] Add to the card a big icon to show green or red and a link to upgrade if you're red\n- [x] Show all releases in a list in a tool\n- [ ] Add a danger state if there are security issues, maybe? or something clever like that?\n\n\n## Installation\n\nYou can install the package in to a Laravel app that uses [Nova](https://nova.laravel.com) via composer:\n\n```md\ncomposer require tightenco/nova-releases\n```\n\nNext up, you may register the card with Nova. This is typically done in the `cards` method of the `NovaServiceProvider`.\n\n```php\n// in app/Providers/NovaServiceProvider.php\n\n// ...\n\npublic function cards()\n{\n    return [\n        // ...\n        new \\Tightenco\\NovaReleases\\LatestRelease,\n    ];\n}\n```\n\nYou can also register the \"release history\" tool, in the `tools` method of the `NovaServiceProvider`.\n\n```php\n// in app/Providers/NovaServiceProvider.php\n\n// ...\n\npublic function tools()\n{\n    return [\n        // ...\n        new \\Tightenco\\NovaReleases\\AllReleases,\n    ];\n}\n```\n\n### Security\n\nIf you discover any security related issues, please email matt@tighten.co instead of using the issue tracker.\n\n## License\n\nThe MIT License (MIT). Please see [License File](LICENSE.md) for more information.\n\n",
            ]),
            factory(Package::class)->make([
                'name' => 'Nova Google Analytics',
                'abstract' => 'Add Google Analytics tools to your Nova app.',
                'composer_name' => 'tightenco/nova-google-analytics',
                'url' => 'https://github.com/tightenco/nova-google-analytics',
                'readme_source' => 'github',
                'readme_format' => 'md',
                'readme' => "# Google Analytics integration with Nova\n\n[![Latest Version on Packagist](https://img.shields.io/packagist/v/tightenco/nova-google-analytics.svg?style=flat-square)](https://packagist.org/packages/tightenco/nova-google-analytics)\n[![Total Downloads](https://img.shields.io/packagist/dt/tightenco/nova-google-analytics.svg?style=flat-square)](https://packagist.org/packages/tightenco/nova-google-analytics)\n\n![image](https://user-images.githubusercontent.com/151829/44671717-4a644600-a9f4-11e8-8505-b99e9b9ed65a.png)\n\n<img src=\"https://user-images.githubusercontent.com/151829/44892455-defbcc00-acb2-11e8-9236-cbc04f1a29eb.png\" width=\"465\">\n\nJUST GETTING STARTED.\n\nPlans:\n\n- Analytics tool\n- Individual cards for each of the useful analytics data points\n- Resource tools (e.g. analytics on each page)\n- Maybe actions for events?\n- Other great stuff I hope :)\n\n## Installation\n\nYou can install the package in to a Laravel app that uses [Nova](https://nova.laravel.com) via composer:\n\n```bash\ncomposer require tightenco/nova-google-analytics\n```\n\nNext up, you must register the card with Nova. This is typically done in the `cards` method of the `NovaServiceProvider`.\n\n```php\n// in app/Providers/NovaServiceProvider.php\n\n// ...\n\npublic function cards()\n{\n    return [\n        // ...\n        new \\Tightenco\\NovaGoogleAnalytics\\PageViewsMetric,\n        new \\Tightenco\\NovaGoogleAnalytics\\VisitorsMetric,\n        new \\Tightenco\\NovaGoogleAnalytics\\MostVisitedPagesCard,\n    ];\n}\n```\n\nFor now, follow the directions on [Spatie's Laravel Google Analytics package](https://github.com/spatie/laravel-analytics) for getting your credentials, then put them here:\n\n```\nyourapp/storage/app/analytics/service-account-credentials.json\n```\n\nAlso add this to the `.env` for your Nova app:\n\n```ini\nANALYTICS_VIEW_ID=\n```\n\n### Security\n\nIf you discover any security related issues, please email matt@tighten.co instead of using the issue tracker.\n\n## License\n\nThe MIT License (MIT). Please see [License File](LICENSE.md) for more information.\n",
            ]),
            factory(Package::class)->make([
                'name' => 'Nova Package Discovery',
                'abstract' => 'Discover new and popular Nova Packages',
                'composer_name' => 'tightenco/nova-package-discovery',
                'url' => 'https://github.com/tightenco/nova-package-discovery',
                'readme_source' => 'github',
                'readme_format' => 'md',
                'readme' => "# Nova Package Discovery package\n\nDiscover new packages! Check out the ten most recent and most popular packages on NovaPackages.com, and also check out some stats about the number of packages submitted and more.\n \n<img width=\"474\" alt=\"image\" src=\"https://user-images.githubusercontent.com/151829/44622253-14538480-a883-11e8-896c-55b08a5c1280.png\">\n\n \n ## Installation\n\nYou can install the package in to a Laravel app that uses [Nova](https://nova.laravel.com) via composer:\n\n```bash\ncomposer require tightenco/nova-package-discovery\n```\n\nNext, you must register the card with Nova. This is typically done in the `cards` method of the `NovaServiceProvider`.\n\n```php\n// in app/Providers/NovaServiceProvider.php\n\n// ...\npublic function cards()\n{\n    return [\n        // ...\n        new \\Tightenco\\NovaPackageDiscovery\\NovaPackageDiscovery,\n    ];\n}\n```\n",
            ]),
        ];

        factory(Collaborator::class, 2)->create()->each(function ($collaborator) use (&$packages) {
            $collaborator->authoredPackages()->save(next($packages));
            $collaborator->authoredPackages()->save(next($packages));
        });

        $users = User::all();

        // Give each of our main packages a jillion ratings
        Package::all()->each(function ($package) use ($users) {
            $users->shuffle();
            $users->take(50)->each(function ($user) use ($package) {
                $user->ratePackage($package->id, rand(1, 15) / 3);
            });
        });

        $tags = Tag::all();

        factory(Package::class, 400)->create();

        Package::all()->each(function ($package) use ($tags, $users) {
            $package->tags()->attach($tags->random()->take(3)->get());
            $users->random()->ratePackage($package->id, rand(1, 15) / 3);
            $users->random()->ratePackage($package->id, rand(1, 15) / 3);
            $users->random()->ratePackage($package->id, rand(1, 15) / 3);
        });

        // @todo make sure tags get synced up when pushing *anything* up to algolia
    }
}
