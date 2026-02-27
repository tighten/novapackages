<?php

use App\Models\Package;
use App\ReadmeFormatter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(Tests\TestCase::class);
uses(RefreshDatabase::class);

it('formats github sources', function () {
    $package = Package::factory()->create([
        'readme_source' => 'github',
        'readme_format' => 'md',
        'repo_url' => 'https://github.com/tightenco/nova-stripe',
        'latest_version' => 'v1.0.0',
        'readme' => '[Dashboard index page](charges-index.jpeg) [contribution guidelines](CONTRIBUTING.md)',
    ]);

    $formattedReadme = (new ReadmeFormatter($package))->format($package->readme);

    $this->assertEquals(
        markdown('[Dashboard index page](https://github.com/tightenco/nova-stripe/raw/v1.0.0/charges-index.jpeg) [contribution guidelines](https://github.com/tightenco/nova-stripe/blob/v1.0.0/CONTRIBUTING.md)'),
        $formattedReadme
    );
});

it('formats gitlab sources', function () {
    $package = Package::factory()->create([
        'readme_source' => 'gitlab',
        'readme_format' => 'md',
        'repo_url' => 'https://gitlab.com/ctroms/test-project',
        'latest_version' => 'v1.0.0',
        'readme' => '[Dashboard index page](charges-index.jpeg) [contribution guidelines](CONTRIBUTING.md)',
    ]);

    $formattedReadme = (new ReadmeFormatter($package))->format($package->readme);

    $this->assertEquals(
        markdown('[Dashboard index page](https://gitlab.com/ctroms/test-project/raw/v1.0.0/charges-index.jpeg) [contribution guidelines](https://gitlab.com/ctroms/test-project/blob/v1.0.0/CONTRIBUTING.md)'),
        $formattedReadme
    );
});

it('formats bitbucket sources', function () {
    $package = Package::factory()->create([
        'readme_source' => 'bitbucket',
        'readme_format' => 'md',
        'repo_url' => 'https://bitbucket.org/tightenco/novapackages-test',
        'latest_version' => 'v1.0.0',
        'readme' => '[Dashboard index page](charges-index.jpeg) [contribution guidelines](CONTRIBUTING.md)',
    ]);

    $formattedReadme = (new ReadmeFormatter($package))->format($package->readme);
    $this->assertEquals(
        markdown('[Dashboard index page](https://bitbucket.org/tightenco/novapackages-test/raw/v1.0.0/charges-index.jpeg) [contribution guidelines](https://bitbucket.org/tightenco/novapackages-test/src/v1.0.0/CONTRIBUTING.md)'),
        $formattedReadme
    );
});

it('wraps html readme with markdown div', function () {
    $package = Package::factory()->create([
        'readme_source' => 'github',
        'readme_format' => 'html',
        'repo_url' => 'https://github.com/tightenco/nova-stripe',
        'latest_version' => 'v1.0.0',
        'readme' => '<div><h1>Foo</h1><pre><code class="language-md">[Dashboard index page](charges-index.jpeg) [contribution guidelines](CONTRIBUTING.md)</code></pre></div>',
    ]);

    $formattedReadme = (new ReadmeFormatter($package))->format($package->readme);
    $this->assertEquals(
        '<div class="markdown"><div><h1>Foo</h1><pre><code class="language-md">[Dashboard index page](charges-index.jpeg) [contribution guidelines](CONTRIBUTING.md)</code></pre></div></div>',
        $formattedReadme
    );
});

it('formats html image relative urls to full qualified raw urls', function () {
    $repoUrl = 'https://github.com/tightenco/nova-stripe';
    $latestVersion = 'v1.0.0';

    $package = Package::factory()->create([
        'readme_source' => 'github',
        'readme_format' => 'html',
        'repo_url' => $repoUrl,
        'latest_version' => $latestVersion,
    ]);

    $readmeFormatter = new ReadmeFormatter($package);

    $jpegUrl = '<img src="charges-index.jpeg" />';
    $jpgUrl = '<img src="charges-index.jpg" />';
    $pngUrl = '<img src="charges-index.png" />';
    $gifUrl = '<img src="charges-index.gif" />';
    $bmpUrl = '<img src="charges-index.bmp" />';
    $svgUrl = '<img src="charges-index.svg" />';

    $formattedJpeg = $readmeFormatter->format($jpegUrl);
    $formattedJpg = $readmeFormatter->format($jpgUrl);
    $formattedPng = $readmeFormatter->format($pngUrl);
    $formattedGif = $readmeFormatter->format($gifUrl);
    $formattedBmp = $readmeFormatter->format($bmpUrl);
    $formattedSvg = $readmeFormatter->format($svgUrl);

    expect($formattedJpeg)->toEqual("<div class=\"markdown\"><img src=\"{$repoUrl}/raw/{$latestVersion}/charges-index.jpeg\" /></div>");
    expect($formattedJpg)->toEqual("<div class=\"markdown\"><img src=\"{$repoUrl}/raw/{$latestVersion}/charges-index.jpg\" /></div>");
    expect($formattedPng)->toEqual("<div class=\"markdown\"><img src=\"{$repoUrl}/raw/{$latestVersion}/charges-index.png\" /></div>");
    expect($formattedGif)->toEqual("<div class=\"markdown\"><img src=\"{$repoUrl}/raw/{$latestVersion}/charges-index.gif\" /></div>");
    expect($formattedBmp)->toEqual("<div class=\"markdown\"><img src=\"{$repoUrl}/raw/{$latestVersion}/charges-index.bmp\" /></div>");
    expect($formattedSvg)->toEqual("<div class=\"markdown\"><img src=\"{$repoUrl}/raw/{$latestVersion}/charges-index.svg\" /></div>");
});

it('formats html non image relative urls to fully qualified blob urls', function () {
    $relativeUrl = '<a href="CONTRIBUTING.md">contribution guidelines</a>';
    $repoUrl = 'https://github.com/tightenco/nova-stripe';
    $latestVersion = 'v1.0.0';

    $package = Package::factory()->create([
        'readme_source' => 'github',
        'readme_format' => 'html',
        'repo_url' => $repoUrl,
        'latest_version' => $latestVersion,
    ]);

    $readmeFormatter = new ReadmeFormatter($package);

    $formattedUrl = $readmeFormatter->format($relativeUrl);

    expect($formattedUrl)->toEqual("<div class=\"markdown\"><a href=\"{$repoUrl}/blob/{$latestVersion}/CONTRIBUTING.md\">contribution guidelines</a></div>");
});

it('formats markdown image relative urls to full qualified raw urls', function () {
    $repoUrl = 'https://github.com/tightenco/nova-stripe';
    $latestVersion = 'v1.0.0';

    $package = Package::factory()->create([
        'readme_source' => 'github',
        'repo_url' => $repoUrl,
        'latest_version' => $latestVersion,
    ]);

    $readmeFormatter = new ReadmeFormatter($package);

    $jpegUrl = '[Dashboard index page](charges-index.jpeg)';
    $jpgUrl = '[Dashboard index page](charges-index.jpg)';
    $pngUrl = '[Dashboard index page](charges-index.png)';
    $gifUrl = '[Dashboard index page](charges-index.gif)';
    $bmpUrl = '[Dashboard index page](charges-index.bmp)';
    $svgUrl = '[Dashboard index page](charges-index.svg)';

    $formattedJpeg = $readmeFormatter->format($jpegUrl);
    $formattedJpg = $readmeFormatter->format($jpgUrl);
    $formattedPng = $readmeFormatter->format($pngUrl);
    $formattedGif = $readmeFormatter->format($gifUrl);
    $formattedBmp = $readmeFormatter->format($bmpUrl);
    $formattedSvg = $readmeFormatter->format($svgUrl);

    expect($formattedJpeg)->toEqual(markdown("[Dashboard index page]({$repoUrl}/raw/{$latestVersion}/charges-index.jpeg)"));
    expect($formattedJpg)->toEqual(markdown("[Dashboard index page]({$repoUrl}/raw/{$latestVersion}/charges-index.jpg)"));
    expect($formattedPng)->toEqual(markdown("[Dashboard index page]({$repoUrl}/raw/{$latestVersion}/charges-index.png)"));
    expect($formattedGif)->toEqual(markdown("[Dashboard index page]({$repoUrl}/raw/{$latestVersion}/charges-index.gif)"));
    expect($formattedBmp)->toEqual(markdown("[Dashboard index page]({$repoUrl}/raw/{$latestVersion}/charges-index.bmp)"));
    expect($formattedSvg)->toEqual(markdown("[Dashboard index page]({$repoUrl}/raw/{$latestVersion}/charges-index.svg)"));
});

it('formats markdown non image relative urls to fully qualified blob urls', function () {
    $relativeUrl = '[contribution guidelines](CONTRIBUTING.md)';
    $repoUrl = 'https://github.com/tightenco/nova-stripe';
    $latestVersion = 'v1.0.0';

    $package = Package::factory()->create([
        'readme_source' => 'github',
        'repo_url' => $repoUrl,
        'latest_version' => $latestVersion,
    ]);

    $readmeFormatter = new ReadmeFormatter($package);

    $formattedUrl = $readmeFormatter->format($relativeUrl);

    expect($formattedUrl)->toEqual(markdown("[contribution guidelines]({$repoUrl}/blob/{$latestVersion}/CONTRIBUTING.md)"));
});

it('does not format fully qualified urls', function () {
    $fullyQualifiedUrl = '[Dashboard index page](http://example.com)';
    $repoUrl = 'https://github.com/tightenco/nova-stripe';
    $latestVersion = 'v1.0.0';

    $package = Package::factory()->create([
        'readme_source' => 'github',
        'repo_url' => $repoUrl,
        'latest_version' => $latestVersion,
    ]);

    $readmeFormatter = new ReadmeFormatter($package);

    $unformattedUrl = $readmeFormatter->format($fullyQualifiedUrl);

    expect($unformattedUrl)->toEqual(markdown($fullyQualifiedUrl));
});

it('does not format mailto urls', function () {
    $mailtoUrl = '[email](mailto:me@example.com)';

    $readmeFormatter = new ReadmeFormatter(Package::factory()->create());

    $unformattedUrl = $readmeFormatter->format($mailtoUrl);

    expect($unformattedUrl)->toEqual(markdown($mailtoUrl));
});
