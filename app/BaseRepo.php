<?php

namespace App;

class BaseRepo
{
    const README_FORMAT = 'md';

    protected $url;

    private function __construct($url)
    {
        $this->url = $url;
    }

    public static function make($url)
    {
        return new self($url);
    }

    public function url()
    {
        return $this->url;
    }

    public function source()
    {
        if ($repoSource = $this->repoSource($this)) {
            return $repoSource;
        }

        // In some cases, user may submit a url to a website that links to their package rather than a url to
        // a VCS or package registry specifically. This regex parses the url submitted for domain name and
        // tld to use as the source.
        // e.g. https://tighten.co/cool-package has a source of tighten.co
        preg_match('/\/\/(.*?)\//i', $this->url, $source) || preg_match('/\/\/(.*)/i', $this->url, $source);

        return $source[1] ?? $this->url;
    }

    public function readme()
    {
    }

    public function readmeFormat()
    {
        if ($this->repoHasConst('README_FORMAT')) {
            return $this->repo()::README_FORMAT;
        }

        return static::README_FORMAT;
    }

    public function latestReleaseVersion()
    {
    }

    private function repoSource($registryRepo)
    {
        if ($this->repoHasConst('SOURCE')) {
            return $this->repo()::SOURCE;
        }

        if (defined('static::SOURCE')) {
            return static::SOURCE;
        }
    }

    protected function repoHasConst($const)
    {
        return method_exists($this, 'repo') && $this->repo() !== null && defined(get_class($this->repo()).'::'.$const);
    }
}
