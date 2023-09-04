<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CartFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => 1,
            'product_id' => $this->faker->numberBetween(10000, 20000),
            'width' => $this->faker->numberBetween(1, 30),
            'weight' => $this->faker->numberBetween(1, 100),
            'note' => $this->faker->optional()->sentence(2),
            // 'deleted_at' => $this->faker->dateTime(),
            'quantity' => 0,
            'weight_total' => 0
        ];
    }
}
  