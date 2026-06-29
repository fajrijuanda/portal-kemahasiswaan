<?php

namespace App\Http\Controllers;

use App\Models\CareerPost;
use App\Models\PressRelease;

class PublicationAdminController extends Controller
{
    private array $sections = [
        'press-releases' => ['label' => 'Press Release', 'icon' => 'grid'],
        'careers' => ['label' => 'Karir', 'icon' => 'access'],
    ];

    public function index(string $section = 'press-releases')
    {
        abort_unless(isset($this->sections[$section]), 404);
        abort_if($section === 'careers' && ! request()->user()->hasAnyRole(['super user', 'admin']), 403);

        return match ($section) {
            'press-releases' => view('content.press-releases.index', [
                'records' => PressRelease::with('creator')->latest()->paginate(request('limit', 10))->withQueryString(),
                'sectionShell' => $this->sectionShell($section, 'Press Release', 'Publikasi berita dari bagian kemahasiswaan.'),
            ]),
            'careers' => view('content.careers.index', [
                'records' => CareerPost::with('creator')->latest()->paginate(request('limit', 10))->withQueryString(),
                'sectionShell' => $this->sectionShell($section, 'Karir', 'Kelola lowongan kerja dan job fair untuk halaman publik.'),
            ]),
        };
    }

    private function sectionShell(string $active, string $title, string $subtitle): array
    {
        return [
            'eyebrow' => 'Publikasi',
            'title' => $title,
            'subtitle' => $subtitle,
            'items' => collect($this->sections)->map(fn ($item, $section) => [
                'label' => $item['label'],
                'icon' => $item['icon'],
                'href' => route('publications.index', $section),
                'active' => $section === $active,
                'count' => $section === 'press-releases' ? PressRelease::count() : CareerPost::count(),
            ])->values()->all(),
            'stats' => [
                ['label' => 'Press Release', 'value' => number_format(PressRelease::count()), 'caption' => 'konten', 'icon' => 'grid', 'tone' => 'blue'],
                ['label' => 'Press Published', 'value' => number_format(PressRelease::where('status', 'Published')->count()), 'caption' => 'tampil publik', 'icon' => 'event', 'tone' => 'emerald'],
                ['label' => 'Karir', 'value' => number_format(CareerPost::count()), 'caption' => 'loker/job fair', 'icon' => 'access', 'tone' => 'emerald'],
                ['label' => 'Karir Published', 'value' => number_format(CareerPost::where('status', 'Published')->count()), 'caption' => 'tampil publik', 'icon' => 'prestasi', 'tone' => 'teal'],
            ],
        ];
    }
}
