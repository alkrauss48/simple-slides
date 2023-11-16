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

        return Inertia::render('Slides', [
            'content' => $presentation->content,
        ]);
    }
}
