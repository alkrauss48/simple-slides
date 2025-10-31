<?php

namespace App\Http\Controllers;

use App\Http\Resources\PresentationResource;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class PresentationController extends Controller
{
    public function show(User $user, string $slug): Response
    {
        $presentation = $user
            ->presentations()
            ->with('user')
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

        return Inertia::render('Presentation', [
            'presentation' => new PresentationResource($presentation),
            'meta' => [
                'title' => $presentation->title,
                'description' => $presentation->description,
                'imageUrl' => $presentation->getFirstMediaUrl('thumbnail'),
            ],
        ]);
    }
}
