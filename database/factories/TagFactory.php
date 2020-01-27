<?php

use App\Tag;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(Tag::class, function (Faker $faker) {
    $name = $faker->sentence;

    return [
        'name' => Str::lower($name),
        'slug' => Str::slug($name),
    ];
});
