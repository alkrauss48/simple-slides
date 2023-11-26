<?php

use App\Models\Presentation;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->guest = User::factory()->create();

    $this->publishedPresentation = Presentation::factory()->create([
        'is_published' => true,
        'user_id' => $this->user->id,
    ]);

    $this->draftPresentation = Presentation::factory()->create([
        'is_published' => false,
        'user_id' => $this->user->id,
    ]);
});

test('published presentation show screen can be rendered for unauthenticated user', function () {
    $response = $this->get(route('presentations.show', [
        'user' => $this->user->username,
        'slug' => $this->publishedPresentation->slug,
    ]));

    $response->assertStatus(200);
});

test('published presentation show screen returns the right view and data', function () {
    $response = $this->get(route('presentations.show', [
        'user' => $this->user->username,
        'slug' => $this->publishedPresentation->slug,
    ]));

    $response->assertInertia(fn (Assert $page) => $page
        ->component('Slides')
        ->where('content', $this->publishedPresentation->content)
        ->has('meta', fn (Assert $page) => $page
            ->where('title', $this->publishedPresentation->title)
            ->where('description', $this->publishedPresentation->description)
            ->where('imageUrl', $this->publishedPresentation->getFirstMediaUrl('thumbnail'))
        )
    );
});

test('draft presentation show screen is not rendered for unauthenticated user', function () {
    $response = $this->get(route('presentations.show', [
        'user' => $this->user->username,
        'slug' => $this->draftPresentation->slug,
    ]));

    $response->assertStatus(403);
});

test('draft presentation show screen can be rendered for author', function () {
    $response = $this
        ->actingAs($this->user)
        ->get(route('presentations.show', [
            'user' => $this->user->username,
            'slug' => $this->draftPresentation->slug,
        ]));

    $response->assertStatus(200);
});

test('draft presentation show screen is not rendered for non-author', function () {
    $response = $this
        ->actingAs($this->guest)
        ->get(route('presentations.show', [
            'user' => $this->user->username,
            'slug' => $this->draftPresentation->slug,
        ]));

    $response->assertStatus(403);
});

test('non-existing slug for presentation shows 404', function () {
    $response = $this->get(route('presentations.show', [
        'user' => $this->user->username,
        'slug' => 'foo',
    ]));

    $response->assertStatus(404);
});

test('non-existing username for user shows 404', function () {
    $response = $this->get(route('presentations.show', [
        'user' => 'foo',
        'slug' => $this->publishedPresentation->slug,
    ]));

    $response->assertStatus(404);
});
