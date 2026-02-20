<?php

namespace Tests\Integration;

use App\Http\Remotes\GitHub;
use Illuminate\Support\Facades\Http;
use Illuminate\Testing\AssertableJsonString;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group("integration")]
class GitHubApiTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Http::allowStrayRequests();
    }

    #[Test]
    public function readme_response_in_expected_format(): void
    {
        $response = app(GitHub::class)->readme('tighten/nova-stripe');

        $this->assertTrue(str_contains($response, '<div id="readme"'));
    }

    #[Test]
    public function releases_response_in_expected_format(): void
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

    #[Test]
    public function searching_issues_response_in_expected_format(): void
    {
        $response = app(GitHub::class)->packageIdeaIssues();

        (new AssertableJsonString($response))->assertStructure([
            [
                'body',
                'html_url',
                'labels',
                'reactions' => [
                    'total_count',
                ],
                'title',
                'url',
                'user' => [
                    'login',
                ],
            ],
        ]);
    }

    #[Test]
    public function user_response_in_expected_format(): void
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
