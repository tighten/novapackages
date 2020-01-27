<?php

use App\Favorite;
use App\Package;
use App\User;
use Faker\Generator as Faker;

$factory->define(Favorite::class, function (Faker $faker) {
    return [
        'user_id' => factory(User::class)->create(),
        'package_id' => factory(Package::class)->create(),
    ];
});
