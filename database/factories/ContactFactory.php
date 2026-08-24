<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContactFactory extends Factory
{
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'gender' => fake()->numberBetween(1, 3),
            'email' => fake()->unique()->safeEmail(),
            'tel' => fake()->numerify('###########'),
            'address' => fake()->address(),
            'building' => fake()->optional()->secondaryAddress(),
            'detail' => fake()->text(120),
        ];
    }
}
