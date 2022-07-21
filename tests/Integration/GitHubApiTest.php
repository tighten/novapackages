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
                'author',
                'body',
                'created_at',
                'draft',
                'id',
                'name',
                'prerelease',
                'published_at',
                'tag_name',
                'url',
            ],
        ]);
    }

    /** @test */
    function user_response_in_expected_format()
    {
        $response = app(GitHub::class)->user('marcusmoore');

        (new AssertableJsonString($response))->assertStructure([
            'avatar_url',
            'html_url',
            'id',
            'name',
            'type',
            'url',
            'login',
        ]);
    }
}
