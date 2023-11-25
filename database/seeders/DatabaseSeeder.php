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
        // Generate the main admin user.
        User::factory()
            ->create([
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'username' => 'admin-user',
                'is_admin' => true,
            ]);

        // Generate a non-admin user, with some content.
        User::factory()
            ->hasPresentations(3, [
                'is_published' => true,
            ])
            ->hasImageUploads(2)
            ->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'username' => 'test-user',
            ]);

        // Generate some extra users with content.
        User::factory()
            ->count(4)
            ->has(Presentation::factory()->count(3))
            ->hasImageUploads(1)
            ->create();

        foreach (User::all() as $user) {
            $user->regenerateImageUploadedSize();
        }
    }
}
