<?php

/** @var Factory $factory */

use App\User;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(User::class, function (Faker $faker) {
    return [
//        'role_id' => $this->faker->randomElement(['1', '2', '3']),
        'first_name' => $this->faker->firstName(),
        'last_name' => $this->faker->lastName(),
        'bio' => $this->faker->text(300),
        'email' => $this->faker->unique()->safeEmail(),
        'email_verified_at' => now(),
        'profile_picture' => $this->faker->imageUrl(150, 150),
        'phone' => $faker->phoneNumber,
        'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        'remember_token' => Str::random(10),
    ];
});
