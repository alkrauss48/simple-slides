<?php

namespace Database\Factories;

use App\Models\Presentation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DailyView>
 */
class DailyViewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'adhoc_slug' => fake()->optional()->slug(),
            'session_id' => fake()->uuid(),
            'presentation_id' => fake()->boolean() ? Presentation::factory() : null,
        ];
    }
}
