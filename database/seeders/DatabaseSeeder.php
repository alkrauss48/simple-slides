<?php

namespace Database\Seeders;

use App\Models\Presentation;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Generate the main user and their published presentations.
        User::factory()
            ->hasPresentations(3, [
                'is_published' => true,
            ])
            ->hasImageUploads(2)
            ->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'username' => 'test-user',
                'is_admin' => true,
            ]);

        // Generate some extra users with presentations.
        User::factory()
            ->count(4)
            ->has(Presentation::factory()->count(3))
            ->create();
    }
}
