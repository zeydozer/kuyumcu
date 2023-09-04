<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => $this->faker->numberBetween(1, 100),
            'auth_id' => $this->faker->numberBetween(1, 100),
            'note' => $this->faker->optional()->sentence(2),
            'quantity' => $this->faker->numberBetween(1, 1000),
            'weight' => $this->faker->numberBetween(1, 10000),
            'status' => $this->faker->numberBetween(-1, 2),
            'finished_at' => $this->faker->date(),
            'deleted_at' => $this->faker->optional()->dateTime()
        ];
    }
}
