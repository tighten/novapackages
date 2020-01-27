<?php

namespace App\Http\Remotes;

use App\Exceptions\GitLabException;
use Illuminate\Support\Arr;
use Zttp\Zttp;

class GitLab
{
    const API_VERSION = 'v4';

    protected $url;

    protected $response;

    public function __construct($url)
    {
        $this->url = $url;
    }

    public static function validateUrl($url)
    {
        return (bool) preg_match('/gitlab.com\/([\w-]+)\/([\w-]+)/i', $url);
    }

    public function fetchData($endpoint)
    {
        $this->response = Zttp::get('https://gitlab.com/api/'.self::API_VERSION.'/'.$endpoint)->json();

        if ($this->responseHasErrors() && $this->isNotFileNotFoundError()) {
            throw new GitLabException("GitLab error fetching data for [{$this->url}]: ".Arr::get($this->response, 'message'));
        }

        return $this->response;
    }

    protected function responseHasErrors()
    {
        return Arr::get($this->response, 'message');
    }

    public function isNotFileNotFoundError()
    {
        return Arr::get($this->response, 'message') !== '404 File Not Found';
    }

    public function hasFileNotFoundError()
    {
        return Arr::get($this->response, 'message') === '404 File Not Found';
    }
}
