<?php

namespace Tests\Unit\Remotes;

use App\Http\Remotes\GitHub;
use Tests\TestCase;

class GitHubTest extends TestCase
{
    /** @test */
    function validates_github_urls()
    {
        $this->assertTrue(GitHub::validateUrl('http://github.com/starwars/lightsabers'));
        $this->assertTrue(GitHub::validateUrl('https://github.com/starwars/lightsabers'));

        $this->assertFalse(GitHub::validateUrl('https://subdomain.github.com/starwars/lightsabers'));
        $this->assertFalse(GitHub::validateUrl('https://notgithub.com/starwars/lightsabers'));
    }
}
