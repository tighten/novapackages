<?php

namespace App;

use App\BaseRepo;
use App\Exceptions\BitBucketException;
use App\Http\Remotes\BitBucket;
use Illuminate\Support\Arr;

class BitBucketRepo extends BaseRepo
{
    const SOURCE = 'bitbucket';

    protected $url;

    protected $bitBucket;

    private function __construct($url, BitBucket $bitBucket)
    {
        if (! $bitBucket->validateUrl($url)) {
            throw new BitBucketException('Invalid Url Provided');
        }

        $this->url = $url;
        $this->bitBucket = $bitBucket;

        preg_match('/bitbucket.org\/([\w-]+)\/([\w-]+)/i', $url, $parts);
        $this->username = $parts[1];
        $this->repo = $parts[2];
    }

    public static function make($url)
    {
        return new static($url, app(BitBucket::class, ['url' => $url]));
    }

    public function readme()
    {
        return $this->getContentsOfFile('README.md') ?: $this->getContentsOfFile('readme.md');
    }

    public function releases()
    {
        return collect(
            Arr::get($this->bitBucket->fetchData("repositories/{$this->username}/{$this->repo}/refs", true), 'values')
        );
    }

    public function latestRelease()
    {
        return $this->releases()->last();
    }

    public function latestReleaseVersion()
    {
        return Arr::get($this->latestRelease(), 'name', 'master');
    }

    public function getContentsOfFile($path)
    {
        return $this->bitBucket->fetchData("repositories/{$this->username}/{$this->repo}/src/{$this->latestReleaseVersion()}/{$path}");
    }
}
