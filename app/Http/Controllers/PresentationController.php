<?php

namespace App\Http\Controllers;

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

        if (! $presentation->canBeViewed) {
            abort(403);
        }

        if ($presentation->shouldTrackView) {
            dispatch(function () use ($presentation) {
                $presentation->addDailyView();
            })->afterResponse();
        }

        return Inertia::render('Slides', [
            'content' => $presentation->content,
            'delimiter' => $presentation->slide_delimiter,
            'meta' => [
                'title' => $presentation->title,
                'description' => $presentation->description,
                'imageUrl' => $presentation->getFirstMediaUrl('thumbnail'),
            ],
        ]);
    }
}
