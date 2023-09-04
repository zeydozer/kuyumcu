<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'role' => $this->faker->numberBetween(0, 1),
            'name' => $this->faker->name(),
            'mail' => $this->faker->unique()->email(),
            'pass' => md5('asdasd'),
            'phone' => $this->faker->optional()->numerify('53########'),
            'address' => $this->faker->optional()->sentence(4)
        ];
    }
}
