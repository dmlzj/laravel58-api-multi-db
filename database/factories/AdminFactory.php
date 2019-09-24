<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Admin;
use App\Model;
use Faker\Generator as Faker;

$factory->define(Admin::class, function (Faker $faker) {
    return [
        'username' => $faker->userName,
        'password' => bcrypt('123456'),
        'remember_token' => str_random(10),
    ];
});
