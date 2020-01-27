<?php

namespace App\Http\Resources;

use Exception;
use Illuminate\Support\Collection;

class ModelResource
{
    public $model;

    protected $payload;

    public function __construct($payload)
    {
        $this->payload = $payload;
    }

    public static function from($payload)
    {
        return (new static($payload))->response();
    }

    public function toArray($payload)
    {
        return $payload->toArray();
    }

    public function response()
    {
        if ($this->payload instanceof Collection) {
            return $this->payload->map(function ($item) {
                return static::from($item);
            });
        }

        if (get_class($this->payload) != $this->model) {
            throw new Exception("Payload is not an instance of {$this->model}");
        }

        return $this->toArray($this->payload);
    }
}
