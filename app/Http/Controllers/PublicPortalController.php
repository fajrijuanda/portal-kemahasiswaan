<?php

namespace App\Http\Controllers;

use App\Models\CareerPost;
use App\Models\PressRelease;

class PublicPortalController extends Controller
{
    public function index()
    {
        return view('public.index', [
            'pressReleases' => PressRelease::where('status', 'Published')->latest('published_at')->take(6)->get(),
            'careerPosts' => CareerPost::where('status', 'Published')->latest('published_at')->take(8)->get(),
        ]);
    }

    public function pressRelease(PressRelease $pressRelease)
    {
        abort_unless($pressRelease->status === 'Published', 404);

        return view('public.press-release', compact('pressRelease'));
    }
}
