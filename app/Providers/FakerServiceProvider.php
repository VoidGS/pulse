<?php

namespace App\Providers;

use Faker\Factory;
use Faker\Generator;
use Faker\Provider\pt_BR\Person;
use Illuminate\Support\ServiceProvider;

class FakerServiceProvider extends ServiceProvider {
    /**
     * Register services.
     */
    public function register(): void {
        $locale = 'pt_BR';

        $abstract = Generator::class;

        $this->app->singleton($abstract, function () use ($locale) {
            $faker = Factory::create($locale);

            $faker->addProvider(new Person($faker));

            return $faker;
        });
    }
}
