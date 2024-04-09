<?php

namespace App\Http\Controllers;

use App\Models\Presentation;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class PresentationController extends Controller
{
    public function show(User $user, string $slug): Response
    {
        $presentation = $user
            ->presentations()
            ->where('slug', $slug)
            ->firstOrFail();

        if (! $this->canViewPresentation($presentation)) {
            abort(403);
        }

        dispatch(function () use ($presentation) {
            $presentation->addDailyView();
        })->afterResponse();

        return Inertia::render('Slides', [
            'content' => $presentation->content,
            'meta' => [
                'title' => $presentation->title,
                'description' => $presentation->description,
                'imageUrl' => $presentation->getFirstMediaUrl('thumbnail'),
            ],
        ]);
    }

    private function canViewPresentation(Presentation $presentation): bool
    {
        // If the presentation is published, then anyone can see it.
        if ($presentation->is_published) {
            return true;
        }

        // If the user is not logged in, then they can't see any draft
        // presentations.
        if (! auth()->check()) {
            return false;
        }

        // Default to the normal view policy function
        return auth()->user()->can('view', $presentation);
    }
}
