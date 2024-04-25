<?php

namespace Database\Factories;

use App\Models\AggregateView;
use App\Models\DailyView;
use App\Models\Presentation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Support\Facades\App;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Presentation>
 */
class PresentationFactory extends Factory
{
    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        if (App::environment('testing')) {
            return $this;
        }

        return $this->afterCreating(function (Presentation $presentation) {
            // Create some fake Daily Views
            DailyView::factory()
                ->count(rand(0, 9))
                ->state(new Sequence(
                    ['session_id' => uniqid()],
                    ['session_id' => uniqid()],
                    ['session_id' => uniqid()],
                ))
                ->create([
                    'presentation_id' => $presentation->id,
                    'adhoc_slug' => null,
                ]);

            $daysSincePresentationCreated = now()->diffInDays($presentation->created_at);

            if ($daysSincePresentationCreated === 0) {
                return;
            }

            // Create some fake Aggregate Views
            for ($i = 1; $i <= $daysSincePresentationCreated; $i++) {
                if (rand(0, 9) < 3) {
                    // 30% chance of no views on a given day
                    continue;
                }

                $totalCount = rand(1, 9);

                AggregateView::create([
                    'presentation_id' => $presentation->id,
                    'adhoc_slug' => null,
                    'total_count' => $totalCount,
                    'unique_count' => rand(1, $totalCount),
                    'created_at' => now()->subDays($i),
                ]);
            }
        });
    }

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
            'created_at' => now()->subDays(rand(0, 21)), // Sometime over the last 3 weeks
        ];
    }
}
