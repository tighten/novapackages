<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Collaborator extends JsonResource
{
    public function toArray($request)
    {
        return [
            'name' => $this->name,
            'url' => $this->url,
            'description' => $this->description,
            'description_html' => markdown($this->description),
            'github_username' => $this->github_username,
        ];
    }
}
