<?php

namespace App\Http\Remotes;

use App\CacheKeys;
use App\Exceptions\PackagistException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class Packagist
{
    protected $url;

    private $data;

    public function __construct($name)
    {
        $this->url = "https://packagist.org/packages/{$name}";
        $this->data = $this->fetchData($name);
    }

    public static function make($name)
    {
        return new static($name);
    }

    public static function validateUrl($url)
    {
        return (bool) preg_match('/packagist\.org\/packages\/([\w-]+)\/([\w-]+)/i', $url);
    }

    public function fetchData($name)
    {
        $this->data = Cache::remember(CacheKeys::packagistData($name), 5, function () {
            $response = Http::packagist()
                ->retry(2, 500, function ($exception, $request) {
                    return $exception instanceof ConnectionException;
                }, false)
                ->get("{$this->url}.json");

            if ($response->status() === 404) {
                return null;
            }

            return $response->json();
        });

        if (Arr::get($this->data, 'status') === 'error') {
            throw new PackagistException("Packagist error looking up [{$name}]: ".$this->data['message']);
        }

        return $this->data;
    }

    public function data()
    {
        return $this->data;
    }
}
