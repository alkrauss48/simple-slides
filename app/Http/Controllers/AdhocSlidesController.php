<?php

namespace App\Http\Controllers;

use App\Models\DailyView;
use Inertia\Inertia;
use Inertia\Response;

class AdhocSlidesController extends Controller
{
    public function index(): Response
    {
        dispatch(function () {
            DailyView::createForAdhocPresentation();
        })->afterResponse();

        return Inertia::render('Slides');
    }

    public function show(string $slides): Response
    {
        dispatch(function () use ($slides) {
            DailyView::createForAdhocPresentation(slug: $slides);
        })->afterResponse();

        return Inertia::render('Slides', [
            'slides' => $slides,
            'meta' => [
                'title' => 'My Presentation',
            ],
        ]);
    }
}
