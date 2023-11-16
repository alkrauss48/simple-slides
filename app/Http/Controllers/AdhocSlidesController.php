<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class AdhocSlidesController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Slides');
    }

    public function show(string $slides): Response
    {
        return Inertia::render('Slides', [
            'slides' => $slides,
        ]);
    }
}
