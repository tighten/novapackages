<?php

namespace App\Http\Resources;

use App\Exceptions\PackagistException;
use App\Http\Remotes\Packagist;
use App\Http\Resources\Collaborator;
use App\Http\Resources\Tag;
use Illuminate\Http\Resources\Json\JsonResource;

class Package extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'name' => $this->name,
            'author' => new Collaborator($this->author),
            'composer_name' => $this->composer_name,
            'url' => $this->url,
            'novapackages_url' => route('packages.show', [
                'namespace' => $this->composer_vendor,
                'name' => $this->composer_package,
            ]),
            'description' => $this->description,
            'description_html' => markdown($this->description),
            'abstract' => $this->abstract,
            'instructions' => $this->instructions,
            'instructions_html' => markdown($this->instructions),
            'created_at' => (string) $this->created_at,
            'updated_at' => (string) $this->updated_at,
            'packagist_downloads' => $this->packagist_downloads,
            'github_stars' => $this->github_stars,
            'tags' => Tag::collection($this->tags),
        ];
    }
}
