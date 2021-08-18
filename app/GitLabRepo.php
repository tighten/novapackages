<?php

namespace App;

use App\BaseRepo;
use App\Http\Remotes\GitLab;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class GitLabRepo extends BaseRepo
{
    const SOURCE = 'gitlab';

    protected $url;

    protected $gitLab;

    private function __construct($url, GitLab $gitLab)
    {
        if (! $gitLab->validateUrl($url)) {
            throw new Exception('Invalid Url Provided');
        }

        $this->url = $url;
        $this->gitLab = $gitLab;

        preg_match('/gitlab.com\/([\w-]+)\/([\w-]+)/i', $url, $parts);
        $this->username = $parts[1];
        $this->repo = $parts[2];
    }

    public static function make($url)
    {
        // If a user changes their name on GitLab, requests
        // sent in a browser will redirect to the new
        // repository while the API will just return a 404.
        // To prevent this, we'll send an HTTP request first
        // and see if the response is a redirect. If it is
        // then we will use the url that the request was
        // going to be redirected to for future requests.
        $response = Http::withoutRedirecting()->get($url);

        if ($response->status() === 301) {
            $url = $response->headers()['Location'][0];
        }

        return new static($url, app(GitLab::class, ['url' => $url]));
    }

    public function readme()
    {
        return $this->getContentsOfFile('README%2Emd')
            ?: $this->getContentsOfFile('readme%2Emd')
            ?: $this->getContentsOfFile('README')
            ?: $this->getContentsOfFile('readme');
    }

    public function releases()
    {
        return collect(
            $this->gitLab->fetchData("projects/{$this->username}%2F{$this->repo}/repository/tags")
        );
    }

    public function latestRelease()
    {
        return $this->releases()->first();
    }

    public function latestReleaseVersion()
    {
        return Arr::get($this->latestRelease(), 'name', 'master');
    }

    public function getContentsOfFile($path)
    {
        $ref = $this->latestReleaseVersion();

        $file = $this->gitLab->fetchData("projects/{$this->username}%2F{$this->repo}/repository/files/{$path}?ref={$ref}");

        if ($this->gitLab->hasFileNotFoundError()) {
            return;
        }

        return ($file['encoding'] == 'base64')
            ? base64_decode($file['content'])
            : $file;
    }
}
