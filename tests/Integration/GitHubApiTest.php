<?php

use App\Http\Remotes\GitHub;
use Illuminate\Support\Facades\Http;
use Illuminate\Testing\AssertableJsonString;

uses()->group('integration');

beforeEach(function () {
    Http::allowStrayRequests();
});

test('readme response in expected format', function () {
    $response = app(GitHub::class)->readme('tighten/nova-stripe');

    $this->assertTrue(str_contains($response, '<div id="readme"'));
});

test('releases response in expected format', function () {
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
});

test('searching issues response in expected format', function () {
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
});

test('user response in expected format', function () {
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
});
