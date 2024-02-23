<?php

namespace Database\Factories;

use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Service>
 */
class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = User::factory();

        return [
            'name' => $this->faker->name(),
            'price' => $this->faker->randomFloat(2),
            'team_id' => Team::factory()->create(['user_id' => $user]),
            'user_id' => $user,
            'active' => true
        ];
    }
}
