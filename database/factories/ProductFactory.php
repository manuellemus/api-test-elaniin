<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'code'      => Str::random(10),
            'name'      => $this->faker->name(),
            'amount'    => $this->faker->randomNumber(2),
            'price'     => $this->faker->randomFloat(2,1,1000),
            'image'     => $this->faker->imageUrl(),
            'description' => $this->faker->realText(rand(10,20)),
        ];
    }
}
