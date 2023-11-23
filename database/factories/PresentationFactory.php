<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Presentation>
 */
class PresentationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->realText(50),
            'description' => fake()->optional()->realText(100),
            'is_published' => fake()->boolean(),
            'content' => "# My Presentation\n\n**Slide 1**\n\n*Slide 2*\n\nSlide 3",
            'user_id' => User::factory(),
        ];
    }
}
