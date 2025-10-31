<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display the user's public profile with their presentations.
     */
    public function show(Request $request, User $user): Response
    {
        $search = $request->input('search', '');

        $presentations = $user->presentations()
            ->where('is_published', true)
            ->when($search, function ($query, $search) {
                $searchLower = strtolower($search);
                $query->where(function ($q) use ($searchLower) {
                    $q->whereRaw('LOWER(title) LIKE ?', ["%{$searchLower}%"])
                        ->orWhereRaw('LOWER(description) LIKE ?', ["%{$searchLower}%"]);
                });
            })
            ->orderBy('updated_at', 'desc')
            ->paginate(12)
            ->withQueryString()
            ->through(fn ($presentation) => [
                'id' => $presentation->id,
                'title' => $presentation->title,
                'slug' => $presentation->slug,
                'description' => $presentation->description,
                'updated_at' => $presentation->updated_at->format('M j, Y'),
            ]);

        return Inertia::render('Profile', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
            ],
            'presentations' => $presentations,
            'search' => $search,
            'meta' => [
                'title' => $user->name.'\'s Presentations',
                'description' => 'View '.$user->name.'\'s presentations on Simple Slides',
            ],
        ]);
    }
}
