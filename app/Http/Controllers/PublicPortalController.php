<?php

namespace App\Http\Controllers;

use App\Models\CareerPost;
use App\Models\PressRelease;
use Illuminate\Support\Facades\Schema;

class PublicPortalController extends Controller
{
    public function index()
    {
        return view('public.index', [
            'pressReleases' => Schema::hasTable('press_releases')
                ? PressRelease::where('status', 'Published')->latest('published_at')->take(6)->get()
                : collect(),
            'careerPosts' => Schema::hasTable('career_posts')
                ? CareerPost::where('status', 'Published')->latest('published_at')->take(8)->get()
                : collect(),
        ]);
    }

    public function pressRelease(PressRelease $pressRelease)
    {
        abort_unless($pressRelease->status === 'Published', 404);

        return view('public.press-release', compact('pressRelease'));
    }
}
