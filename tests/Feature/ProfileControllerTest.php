<?php

use App\Models\Presentation;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('profile screen can be rendered', function () {
    $response = $this->get(route('profile.show', [
        'user' => $this->user->username,
    ]));

    $response->assertStatus(200);
});

test('profile screen returns the right view and user data', function () {
    $response = $this->get(route('profile.show', [
        'user' => $this->user->username,
    ]));

    $response->assertInertia(fn (Assert $page) => $page
        ->component('Profile')
        ->has('user', fn (Assert $page) => $page
            ->where('id', $this->user->id)
            ->where('name', $this->user->name)
            ->where('username', $this->user->username)
        )
        ->has('presentations')
        ->where('search', '')
    );
});

test('profile screen only shows published presentations', function () {
    $publishedPresentation = Presentation::factory()->create([
        'is_published' => true,
        'user_id' => $this->user->id,
        'title' => 'Published Presentation',
    ]);

    $draftPresentation = Presentation::factory()->create([
        'is_published' => false,
        'user_id' => $this->user->id,
        'title' => 'Draft Presentation',
    ]);

    $response = $this->get(route('profile.show', [
        'user' => $this->user->username,
    ]));

    $response->assertInertia(fn (Assert $page) => $page
        ->component('Profile')
        ->has('presentations.data', 1)
        ->has('presentations.data.0', fn (Assert $page) => $page
            ->where('id', $publishedPresentation->id)
            ->where('title', $publishedPresentation->title)
            ->where('slug', $publishedPresentation->slug)
            ->where('description', $publishedPresentation->description)
            ->has('updated_at')
        )
    );
});

test('profile screen orders presentations by updated_at desc', function () {
    $olderPresentation = Presentation::factory()->create([
        'is_published' => true,
        'user_id' => $this->user->id,
        'updated_at' => now()->subDays(2),
    ]);

    $newerPresentation = Presentation::factory()->create([
        'is_published' => true,
        'user_id' => $this->user->id,
        'updated_at' => now()->subDay(),
    ]);

    $response = $this->get(route('profile.show', [
        'user' => $this->user->username,
    ]));

    $response->assertInertia(fn (Assert $page) => $page
        ->component('Profile')
        ->has('presentations.data', 2)
        ->where('presentations.data.0.id', $newerPresentation->id)
        ->where('presentations.data.1.id', $olderPresentation->id)
    );
});

test('profile screen search filters presentations by title', function () {
    $matchingPresentation = Presentation::factory()->create([
        'is_published' => true,
        'user_id' => $this->user->id,
        'title' => 'Laravel Testing Guide',
    ]);

    $nonMatchingPresentation = Presentation::factory()->create([
        'is_published' => true,
        'user_id' => $this->user->id,
        'title' => 'Vue Components',
    ]);

    $response = $this->get(route('profile.show', [
        'user' => $this->user->username,
        'search' => 'Laravel',
    ]));

    $response->assertInertia(fn (Assert $page) => $page
        ->component('Profile')
        ->has('presentations.data', 1)
        ->where('presentations.data.0.id', $matchingPresentation->id)
        ->where('search', 'Laravel')
    );
});

test('profile screen search filters presentations by description', function () {
    $matchingPresentation = Presentation::factory()->create([
        'is_published' => true,
        'user_id' => $this->user->id,
        'title' => 'My Talk',
        'description' => 'This is about Laravel testing',
    ]);

    $nonMatchingPresentation = Presentation::factory()->create([
        'is_published' => true,
        'user_id' => $this->user->id,
        'title' => 'Another Talk',
        'description' => 'This is about Vue',
    ]);

    $response = $this->get(route('profile.show', [
        'user' => $this->user->username,
        'search' => 'testing',
    ]));

    $response->assertInertia(fn (Assert $page) => $page
        ->component('Profile')
        ->has('presentations.data', 1)
        ->where('presentations.data.0.id', $matchingPresentation->id)
    );
});

test('profile screen paginates results', function () {
    Presentation::factory(15)->create([
        'is_published' => true,
        'user_id' => $this->user->id,
    ]);

    $response = $this->get(route('profile.show', [
        'user' => $this->user->username,
    ]));

    $response->assertInertia(fn (Assert $page) => $page
        ->component('Profile')
        ->has('presentations.data', 12)
        ->where('presentations.total', 15)
        ->where('presentations.per_page', 12)
        ->where('presentations.current_page', 1)
    );
});

test('profile screen can navigate to second page', function () {
    Presentation::factory(15)->create([
        'is_published' => true,
        'user_id' => $this->user->id,
    ]);

    $response = $this->get(route('profile.show', [
        'user' => $this->user->username,
        'page' => 2,
    ]));

    $response->assertInertia(fn (Assert $page) => $page
        ->component('Profile')
        ->has('presentations.data', 3)
        ->where('presentations.current_page', 2)
    );
});

test('profile screen maintains search across pagination', function () {
    Presentation::factory(15)->create([
        'is_published' => true,
        'user_id' => $this->user->id,
        'title' => 'Laravel Tutorial',
    ]);

    Presentation::factory(5)->create([
        'is_published' => true,
        'user_id' => $this->user->id,
        'title' => 'Vue Guide',
    ]);

    $response = $this->get(route('profile.show', [
        'user' => $this->user->username,
        'search' => 'Laravel',
        'page' => 2,
    ]));

    $response->assertInertia(fn (Assert $page) => $page
        ->component('Profile')
        ->has('presentations.data', 3)
        ->where('presentations.total', 15)
        ->where('presentations.current_page', 2)
        ->where('search', 'Laravel')
    );
});

test('non-existing username shows 404', function () {
    $response = $this->get(route('profile.show', [
        'user' => 'nonexistentuser',
    ]));

    $response->assertStatus(404);
});
