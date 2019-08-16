<?php

use Illuminate\Support\Str;
use Faker\Generator as Faker;
use Laravel\Cashier\Subscription;
use App\{Model\Inventory\Article, Model\Invoice\Bill, Model\Person\Client, User, Organization, Plan, Restriction};

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
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'email_verified_at' => now(),
        'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        'remember_token' => Str::random(10),
    ];
});

$factory->define(Subscription::class, function (Faker $faker) {
    return [
        'user_id' => factory(User::class),
        'name' => 'main',
        'braintree_id' => Str::random(5),
        'braintree_plan' => $faker->randomElement(['Advanced', 'Basic', 'Premium']),
        'quantity' => 1,
    ];
});

$factory->state(Subscription::class, 'active', function () {
    return [
        'created_at' => now(),
        'updated_at' => now(),
    ];
});

$factory->state(Subscription::class, 'trials', function () {
    return [
        'trial_ends_at' => now()->addDay(5),
        'created_at' => now(),
        'updated_at' => now(),
    ];
});

$factory->state(Subscription::class, 'ends', function () {
    return [
        'ends_at' => now()->addDay(5),
        'created_at' => now(),
        'updated_at' => now(),
    ];
});

$factory->define(Organization::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'user_id' => factory(User::class),
    ];
});

$factory->define(Plan::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'braintree_plan' => $faker->randomElement(['Advanced', 'Basic', 'Premium']),
        'price' => $faker->numberBetween(10, 1000),
        'trialDuration' => 0
    ];
});

$factory->define(Restriction::class, function (Faker $faker) {
    return [
        'plan_id' => factory(Plan::class),
        'restriction_key' => Str::random(5),
        'restriction_limit' => $faker->numberBetween(10, 1000),
    ];
});

$factory->define(Article::class, function (Faker $faker) {
    return [
        'organization_id' => factory(Organization::class),
        'name' => $faker->name,
        'cost' => $faker->numberBetween(100, 1000),
    ];
});

$factory->define(Client::class, function (Faker $faker) {
    return [
        'organization_id' => factory(Organization::class),
        'name' => $faker->name,
        'last_name' => $faker->lastName,
        'id_card' => $faker->randomDigit,
    ];
});

$factory->define(Bill::class, function (Faker $faker) {
    return [
        'organization_id' => factory(Organization::class),
        'client_id' => factory(Client::class),
        'bill_type' => $faker->randomElement(Bill::ALL_BILL_TYPE),
        'status' => $faker->randomElement(Bill::ALL_STATUS)
    ];
});
