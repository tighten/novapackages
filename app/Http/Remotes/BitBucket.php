<?php

namespace App\Http\Remotes;

use App\Exceptions\BitBucketException;
use Illuminate\Support\Arr;
use Zttp\Zttp;

class BitBucket
{
    const API_VERSION = '2.0';

    protected $url;

    public function __construct($url)
    {
        $this->url = $url;
    }

    public static function validateUrl($url)
    {
        return (bool) preg_match('/bitbucket.org\/([\w-]+)\/([\w-]+)/i', $url);
    }

    public function fetchData($endpoint, $asJson = false)
    {
        $response = Zttp::get('https://api.bitbucket.org/'.self::API_VERSION.'/'.$endpoint);
        $responseContents = $response->getBody()->getContents();
        $responseJson = json_decode($responseContents, true);

        if ($this->responseHasErrors($responseJson) && $this->isFileNotFoundError($responseJson) === false) {
            throw new BitBucketException("BitBucket error fetching data for [{$this->url}]: ".Arr::get($responseJson, 'error.message'));
        }

        return $asJson ? $responseJson : $responseContents;
    }

    protected function responseHasErrors($responseJson)
    {
        return Arr::get($responseJson, 'type') === 'error';
    }

    public function isFileNotFoundError($responseJson)
    {
        return strpos(Arr::get($responseJson, 'error.message', ''), 'No such file or directory');
    }
}
