<?php

namespace Tests\Unit;

use App\BaseRepo;
use App\NpmRepo;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class NpmRepoTest extends TestCase
{
    /** @test */
    function it_returns_proper_readme_format()
    {
        Http::fake(['https://registry.npmjs.org/lodash/' => Http::response()]);

        $repo = NpmRepo::make('https://www.npmjs.com/package/lodash');

        $this->assertEquals(BaseRepo::README_FORMAT, $repo->readmeFormat());
    }
}
