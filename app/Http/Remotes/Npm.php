<?php

namespace App\Http\Remotes;

use App\Exceptions\NpmException;
use Illuminate\Support\Facades\Http;

class Npm
{
    public $registryUrl;

    private $data;

    public function __construct($url)
    {
        if (! $this->validateUrl($url)) {
            throw new NpmException('Invalid Url Provided');
        }

        $this->setRegistryUrl($url);
        $this->data = $this->fetchData($url);
    }

    public static function make($url)
    {
        return new static($url);
    }

    public static function validateUrl($url)
    {
        return (bool) preg_match('/npmjs\.(?:com|org)\/(?:package\/)?([\w-]+)(?:\/v\/((?:\d+\.)?(?:\d+\.)?(?:\*|\d+)))?/i', $url);
    }

    protected function setRegistryUrl($url)
    {
        if (preg_match('/npmjs\.(?:com|org)\/(?:package\/)?([\w-]+)(?:\/v\/((?:\d+\.)?(?:\d+\.)?(?:\*|\d+)))?/i', $url, $parts)) {
            $this->registryUrl = 'https://registry.npmjs.org/'.$parts[1].'/'.($parts[2] ?? '');
        }
    }

    protected function fetchData($url)
    {
        return Http::get($this->registryUrl)->json();
    }

    public function data()
    {
        return $this->data;
    }
}
