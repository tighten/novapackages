<?php

namespace Tests\Unit;

use App\Package;
use App\ReadmeFormatter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ReadmeFormatterTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_formats_github_sources()
    {
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
    }

    /** @test */
    public function it_formats_gitlab_sources()
    {
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
    }

    /** @test */
    public function it_formats_bitbucket_sources()
    {
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
    }

    /** @test */
    public function it_wraps_html_readme_with_markdown_div()
    {
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
    }

    /** @test */
    public function it_formats_html_image_relative_urls_to_full_qualified_raw_urls()
    {
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

        $this->assertEquals("<div class=\"markdown\"><img src=\"{$repoUrl}/raw/{$latestVersion}/charges-index.jpeg\" /></div>", $formattedJpeg);
        $this->assertEquals("<div class=\"markdown\"><img src=\"{$repoUrl}/raw/{$latestVersion}/charges-index.jpg\" /></div>", $formattedJpg);
        $this->assertEquals("<div class=\"markdown\"><img src=\"{$repoUrl}/raw/{$latestVersion}/charges-index.png\" /></div>", $formattedPng);
        $this->assertEquals("<div class=\"markdown\"><img src=\"{$repoUrl}/raw/{$latestVersion}/charges-index.gif\" /></div>", $formattedGif);
        $this->assertEquals("<div class=\"markdown\"><img src=\"{$repoUrl}/raw/{$latestVersion}/charges-index.bmp\" /></div>", $formattedBmp);
        $this->assertEquals("<div class=\"markdown\"><img src=\"{$repoUrl}/raw/{$latestVersion}/charges-index.svg\" /></div>", $formattedSvg);
    }

    /** @test */
    public function it_formats_html_non_image_relative_urls_to_fully_qualified_blob_urls()
    {
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

        $this->assertEquals("<div class=\"markdown\"><a href=\"{$repoUrl}/blob/{$latestVersion}/CONTRIBUTING.md\">contribution guidelines</a></div>", $formattedUrl);
    }

    /** @test */
    public function it_formats_markdown_image_relative_urls_to_full_qualified_raw_urls()
    {
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

        $this->assertEquals(markdown("[Dashboard index page]({$repoUrl}/raw/{$latestVersion}/charges-index.jpeg)"), $formattedJpeg);
        $this->assertEquals(markdown("[Dashboard index page]({$repoUrl}/raw/{$latestVersion}/charges-index.jpg)"), $formattedJpg);
        $this->assertEquals(markdown("[Dashboard index page]({$repoUrl}/raw/{$latestVersion}/charges-index.png)"), $formattedPng);
        $this->assertEquals(markdown("[Dashboard index page]({$repoUrl}/raw/{$latestVersion}/charges-index.gif)"), $formattedGif);
        $this->assertEquals(markdown("[Dashboard index page]({$repoUrl}/raw/{$latestVersion}/charges-index.bmp)"), $formattedBmp);
        $this->assertEquals(markdown("[Dashboard index page]({$repoUrl}/raw/{$latestVersion}/charges-index.svg)"), $formattedSvg);
    }

    /** @test */
    public function it_formats_markdown_non_image_relative_urls_to_fully_qualified_blob_urls()
    {
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

        $this->assertEquals(markdown("[contribution guidelines]({$repoUrl}/blob/{$latestVersion}/CONTRIBUTING.md)"), $formattedUrl);
    }

    /** @test */
    public function it_does_not_format_fully_qualified_urls()
    {
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

        $this->assertEquals(markdown($fullyQualifiedUrl), $unformattedUrl);
    }

    /** @test */
    public function it_does_not_format_mailto_urls()
    {
        $mailtoUrl = '[email](mailto:me@example.com)';

        $readmeFormatter = new ReadmeFormatter(Package::factory()->create());

        $unformattedUrl = $readmeFormatter->format($mailtoUrl);

        $this->assertEquals(markdown($mailtoUrl), $unformattedUrl);
    }
}
