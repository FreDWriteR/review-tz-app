<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid'        => $this->faker->uuid(),
            'is_active'   => true,
            'category'    => $this->faker->word(),
            'name'        => $this->faker->word(),
            'description' => $this->faker->sentence(),
            'thumbnail'   => null,
            'price'       => $this->faker->randomFloat(2, 1, 100),
        ];
    }
}
