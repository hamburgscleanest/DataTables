<?php

use hamburgscleanest\DataTables\Tests\TestModel;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(TestModel::class, function(Faker\Generator $faker) {
    return [
        'name'       => $faker->name,
        'created_at' => $faker->dateTimeThisYear
    ];
});