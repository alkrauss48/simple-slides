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
        if (! $this->isValidBase64String($slides)) {
            abort(404);
        }

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

    private function isValidBase64String(string $value): bool
    {
        // Decode and encode the string via base64.
        // If the string is the same, then it is valid.
        if (base64_encode((string) base64_decode($value, true)) == $value) {
            return true;
        }

        return false;
    }
}
