<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'ctg_id' => $this->faker->optional()->numberBetween(1, 200),
            'width' => $this->faker->numberBetween(1, 30),
            'weight' => $this->faker->numberBetween(1, 100),
            'empty' => $this->faker->numberBetween(0, 1),
        ];
    }
}
