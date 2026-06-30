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
            'services' => collect($this->publicServices())->take(4)->values(),
        ]);
    }

    public function profile()
    {
        return view('public.profile');
    }

    public function services()
    {
        return view('public.services', [
            'services' => collect($this->publicServices()),
        ]);
    }

    public function serviceDetail(string $service)
    {
        $service = collect($this->publicServices())->firstWhere('slug', $service);

        abort_unless($service, 404);

        return view('public.service-detail', compact('service'));
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

    private function publicServices(): array
    {
        return [
            ['slug' => 'prestasi-mahasiswa', 'title' => 'Prestasi Mahasiswa', 'desc' => 'Pendataan lomba, kategori event, scope, juara, dan kuota prestasi prodi.', 'icon' => 'prestasi', 'tone' => 'gold'],
            ['slug' => 'event-mahasiswa', 'title' => 'Event Mahasiswa', 'desc' => 'Pengajuan kegiatan, dokumentasi, dan status review kegiatan mahasiswa.', 'icon' => 'event', 'tone' => 'teal'],
            ['slug' => 'reimbursement', 'title' => 'Reimbursement', 'desc' => 'Pengajuan klaim dengan foto, surat tugas, sertifikat, dan link penyelenggara.', 'icon' => 'tracer', 'tone' => 'violet'],
            ['slug' => 'beasiswa', 'title' => 'Beasiswa', 'desc' => 'Pengajuan KIP, Kacer, Tahfidz, dan jenis beasiswa lainnya.', 'icon' => 'beasiswa', 'tone' => 'pink'],
            ['slug' => 'ormawa', 'title' => 'Ormawa', 'desc' => 'Profil organisasi, proposal kegiatan, dan reimbursement acara Ormawa.', 'icon' => 'user', 'tone' => 'orange'],
            ['slug' => 'tracer-study', 'title' => 'Tracer Study', 'desc' => 'Kanal pengumpulan data alumni untuk kebutuhan evaluasi dan akreditasi.', 'icon' => 'access', 'tone' => 'green'],
            ['slug' => 'berita', 'title' => 'Berita', 'desc' => 'Berita resmi yang disusun dan dipublikasikan bagian terkait.', 'icon' => 'grid', 'tone' => 'blue'],
            ['slug' => 'karir', 'title' => 'Karir', 'desc' => 'Kurasi lowongan kerja dan job fair untuk mahasiswa serta alumni.', 'icon' => 'prodi', 'tone' => 'slate'],
        ];
    }
}
