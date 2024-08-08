<?php

namespace App\Http\Resources;

use App\Models\Tag;
use Illuminate\Support\Str;

class TagResource extends ModelResource
{
    public $model = Tag::class;

    public function toArray($tag): array
    {
        return [
            'id' => $tag->id,
            'name' => Str::title($tag->name),
            'slug' => $tag->slug,
            'url' => $tag->url(),
        ];
    }
}
