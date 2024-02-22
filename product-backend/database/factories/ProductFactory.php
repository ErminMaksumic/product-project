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
            'name' => fake()->name(),
            'description' => fake()->sentence(5),
            'product_type_id' => 1,
            'validFrom' => '2020-01-28 19:27:42.000',
            'validTo' => "2021-01-28 19:27:42.000",
            'status' => 'DRAFT'
        ];
    }
}
