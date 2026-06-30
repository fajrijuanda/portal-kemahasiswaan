<?php

namespace App\Http\Controllers;

use App\Models\CareerPost;
use App\Models\PressRelease;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Schema;

class PublicationAdminController extends Controller
{
    private array $sections = [
        'berita' => ['label' => 'Berita', 'icon' => 'grid'],
        'careers' => ['label' => 'Karir', 'icon' => 'access'],
    ];

    public function index(string $section = 'berita')
    {
        if ($section === 'press-releases') {
            return redirect()->route('publications.index', 'berita');
        }

        abort_unless(isset($this->sections[$section]), 404);
        abort_if($section === 'careers' && ! request()->user()->hasAnyRole(['super user', 'admin']), 403);

        return match ($section) {
            'berita' => view('content.press-releases.index', [
                'records' => $this->hasTable('press_releases') ? PressRelease::with('creator')->latest()->paginate(request('limit', 10))->withQueryString() : $this->emptyPaginator(),
                'sectionShell' => $this->sectionShell($section, 'Berita', 'Publikasi berita dari bagian kemahasiswaan.'),
            ]),
            'careers' => view('content.careers.index', [
                'records' => $this->hasTable('career_posts') ? CareerPost::with('creator')->latest()->paginate(request('limit', 10))->withQueryString() : $this->emptyPaginator(),
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
                'count' => $section === 'berita' ? $this->countModel('press_releases', PressRelease::class) : $this->countModel('career_posts', CareerPost::class),
            ])->values()->all(),
            'stats' => [
                ['label' => 'Berita', 'value' => number_format($this->countModel('press_releases', PressRelease::class)), 'caption' => 'konten', 'icon' => 'grid', 'tone' => 'blue'],
                ['label' => 'Berita Published', 'value' => number_format($this->hasTable('press_releases') ? PressRelease::where('status', 'Published')->count() : 0), 'caption' => 'tampil publik', 'icon' => 'event', 'tone' => 'emerald'],
                ['label' => 'Karir', 'value' => number_format($this->countModel('career_posts', CareerPost::class)), 'caption' => 'loker/job fair', 'icon' => 'access', 'tone' => 'emerald'],
                ['label' => 'Karir Published', 'value' => number_format($this->hasTable('career_posts') ? CareerPost::where('status', 'Published')->count() : 0), 'caption' => 'tampil publik', 'icon' => 'prestasi', 'tone' => 'teal'],
            ],
        ];
    }

    private function hasTable(string $table): bool
    {
        return Schema::hasTable($table);
    }

    private function countModel(string $table, string $model): int
    {
        return $this->hasTable($table) ? $model::count() : 0;
    }

    private function emptyPaginator(): LengthAwarePaginator
    {
        return new LengthAwarePaginator(collect(), 0, request('limit', 10), 1, ['path' => request()->url()]);
    }
}
