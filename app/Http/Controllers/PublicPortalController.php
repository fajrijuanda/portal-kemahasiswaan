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
            'pressReleases' => $this->publishedPressReleases(6),
            'careerPosts' => $this->publishedCareerPosts(8),
        ]);
    }

    public function profile()
    {
        return view('public.profile');
    }

    public function services()
    {
        return view('public.services');
    }

    public function news()
    {
        return view('public.news', [
            'pressReleases' => $this->publishedPressReleases(12),
        ]);
    }

    public function links()
    {
        return view('public.links', [
            'careerPosts' => $this->publishedCareerPosts(12),
        ]);
    }

    public function pressRelease(PressRelease $pressRelease)
    {
        abort_unless($pressRelease->status === 'Published', 404);

        return view('public.press-release', compact('pressRelease'));
    }

    private function publishedPressReleases(int $limit)
    {
        return Schema::hasTable('press_releases')
            ? PressRelease::where('status', 'Published')->latest('published_at')->take($limit)->get()
            : collect();
    }

    private function publishedCareerPosts(int $limit)
    {
        return Schema::hasTable('career_posts')
            ? CareerPost::where('status', 'Published')->latest('published_at')->take($limit)->get()
            : collect();
    }
}
