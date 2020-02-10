<?php

namespace Tests;

use App\User;
use DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;
use Facades\App\Repo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Collection;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\GithubProvider;
use Laravel\Socialite\Two\User as SocialiteUser;
use PHPUnit\Framework\Assert;

abstract class TestCase extends BaseTestCase
{
    use ArraySubsetAsserts, CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();

        /* Assert the collection equals the given collection */
        Collection::macro('assertEquals', function ($items) {
            Assert::assertCount($items->count(), $this);
            $this->zip($items)->each(function ($itemPair) {
                is_array($itemPair[0])
                    ? Assert::assertTrue($itemPair[0] === $itemPair[1])
                    : Assert::assertTrue($itemPair[0]->is($itemPair[1]));
            });
        });
    }

    protected function mockSocialiteWithUserData($userData)
    {
        $socialiteUser = (new SocialiteUser)->map([
            'name' => $userData['name'],
            'email' => $userData['email'],
            'avatar' => $userData['avatar'],
            'nickname' => $userData['github_username'],
            'id' => $userData['github_user_id'],
        ]);

        $m = $this->createMock(GithubProvider::class);
        $m->method('user')->willReturn($socialiteUser);
        Socialite::shouldReceive('driver')->andReturn($m);

        return $socialiteUser;
    }

    /**
     * This method builds a mock repo object that will be used when the `fromRepoRequest` method is called.
     * It accepts an array of parameters for a single repo object, or an array of arrays of parameters to
     * create multiple objects. A fake `Repo` will be created for each array of parameters. These repo
     * objects will be returned to the test in the same order they are provided in the array.
     * Optional parameters are 'url', 'source', 'readme', 'latest_version'.
     */
    protected function fakesRepoFromRequest($repoObjects = [])
    {
        return call_user_func_array(
            [Repo::shouldReceive('fromRequest'), 'andReturn'],
            $this->buildMockRepoObjectsFromArray($repoObjects)
        );
    }

    /**
     * This method builds a mock repo object that will be used when the `fromPackageModel` method is called.
     * It accepts an array of parameters for a single repo object, or an array of arrays of parameters to
     * create multiple objects. A fake `Repo` will be created for each array of parameters. These repo
     * objects will be returned to the test in the same order they are provided in the array.
     * Optional parameters are 'url', 'source', 'readme', 'latest_version'.
     */
    protected function fakeRepoFromPackageModel($repoObjects = [])
    {
        return call_user_func_array(
            [Repo::shouldReceive('fromPackageModel')->with(Model::class), 'andReturn'],
            $this->buildMockRepoObjectsFromArray($repoObjects)
        );
    }

    public function buildMockRepoObjectsFromArray($repoObjects)
    {
        // Becuase we indentd on mapping over an array of arrays of repo paremeters, we normalize
        // the data passed to this method so that is is in the proper format.
        if (! is_array(reset($repoObjects))) {
            $repoObjects = [$repoObjects];
        }

        return array_map(function ($repo) {
            return $this->buildFakeRepo(
                $repo['url'] ?? null,
                $repo['source'] ?? null,
                $repo['readme'] ?? null,
                $repo['readme_format'] ?? null,
                $repo['latest_version'] ?? null
            );
        }, $repoObjects);
    }

    public function buildFakeRepo($url = null, $source = null, $readme = null, $readmeFormat = null, $latestVersion = null)
    {
        return new class($url, $source, $readme, $readmeFormat, $latestVersion) {
            protected $url;

            protected $source;

            protected $readme;

            protected $readmeFormat;

            protected $latestVersion;

            public function __construct($url, $source, $readme, $readmeFormat, $latestVersion)
            {
                $this->url = $url;
                $this->source = $source;
                $this->readme = $readme;
                $this->readmeFormat = $readmeFormat;
                $this->latestVersion = $latestVersion;
            }

            public function url()
            {
                return $this->url;
            }

            public function source()
            {
                return $this->source;
            }

            public function readme()
            {
                return $this->readme;
            }

            public function readmeFormat()
            {
                return $this->readmeFormat;
            }

            public function latestReleaseVersion()
            {
                return $this->latestVersion;
            }
        };
    }
}
