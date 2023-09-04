<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class BraceletsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'cart_id' => 1,
            'height_58' => $this->faker->numberBetween(1, 30),
            'height_60' => $this->faker->numberBetween(1, 30),
            'height_62' => $this->faker->numberBetween(1, 30),
            'height_64' => $this->faker->numberBetween(1, 30),
            'height_56' => $this->faker->numberBetween(1, 30),
            'height_66' => $this->faker->numberBetween(1, 30),
            'height_68' => $this->faker->numberBetween(1, 30),
            'height_70' => $this->faker->numberBetween(1, 30),
            'height_72' => $this->faker->numberBetween(1, 30),
            'height_74' => $this->faker->numberBetween(1, 30),
            // 'deleted_at' => $this->faker->dateTime()
        ];
    }
}
