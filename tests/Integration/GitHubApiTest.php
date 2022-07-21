<?php

namespace Tests\Integration;

use App\Http\Remotes\GitHub;
use Illuminate\Testing\AssertableJsonString;
use Tests\TestCase;

/** @group integration */
class GitHubApiTest extends TestCase
{
    /** @test */
    function readme_response_in_expected_format()
    {
        $response = app(GitHub::class)->readme('tighten/nova-stripe');

        $this->assertTrue(str_contains($response, '<div id="readme"'));
    }

    /** @test */
    function releases_response_in_expected_format()
    {
        $response = app(GitHub::class)->releases('tighten/nova-stripe');

        (new AssertableJsonString($response))->assertStructure([
            [
                'url',
                'id',
                'author',
                'tag_name',
                'name',
                'draft',
                'prerelease',
                'created_at',
                'published_at',
                'body',
            ],
        ]);
    }
}
