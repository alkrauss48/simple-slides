<?php

namespace Database\Factories;

use App\Models\ImageUpload;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\App;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ImageUpload>
 */
class ImageUploadFactory extends Factory
{
    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        if (App::environment('testing')) {
            return $this;
        }

        return $this->afterCreating(function (ImageUpload $record) {
            $record
                ->addMediaFromUrl('https://loremflickr.com/640/480')
                ->toMediaCollection('image');
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
            'alt_text' => fake()->realText(100),
            'user_id' => User::factory(),
        ];
    }
}
