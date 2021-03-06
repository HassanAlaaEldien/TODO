<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = 'secret',
        'remember_token' => str_random(10)
    ];
});

$factory->define(App\Task::class, function (Faker\Generator $faker) {

    return [
        'task' => $faker->sentence,
        'user_id' => function () {
            return factory(\App\User::class)->create(['password' => bcrypt('secret')])->id;
        },
        'status' => 'private'
    ];
});

$factory->define(App\TaskDeadline::class, function (Faker\Generator $faker) {

    return [
        'deadline' => $faker->dateTime(),
        'task_id' => function () {
            return factory(\App\Task::class)->create()->id;
        }
    ];
});
