<?php

namespace Database\Factories;

use Faker\Provider\pt_BR\Person;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory {
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array {
        $genderArr = ['M', 'F'];

        return [
            'name' => $this->faker->name(),
            'cpf' => $this->faker->cpf(false),
            'gender' => $genderArr[array_rand($genderArr)],
            'birthdate' => $this->faker->date()
        ];
    }
}
