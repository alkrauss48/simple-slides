<?php

namespace Database\Factories;

use App\Models\Presentation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AggregateView>
 */
class AggregateViewFactory extends Factory
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
            'presentation_id' => fake()->boolean() ? Presentation::factory() : null,
        ];
    }

    /**
     * Set data for an instructions record
     */
    public function instructions(): static
    {
        return $this->state(fn (array $attributes) => [
            'presentation_id' => null,
            'adhoc_slug' => null,
        ]);
    }

    /**
     * Set data for an adhoc record
     */
    public function adhoc(): static
    {
        return $this->state(fn (array $attributes) => [
            'presentation_id' => null,
            'adhoc_slug' => fake()->slug(),
        ]);
    }

    /**
     * Set data for a presentation record
     */
    public function presentation(): static
    {
        return $this->state(fn (array $attributes) => [
            'presentation_id' => Presentation::factory(),
        ]);
    }
}
