<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\AchievementQuota;
use App\Models\Competition;
use App\Models\Prodi;
use App\Models\ScholarshipType;
use App\Models\Semester;

class MasterDataController extends Controller
{
    private array $sections = [
        'prodi' => ['label' => 'Prodi', 'icon' => 'prodi'],
        'semester' => ['label' => 'Semester', 'icon' => 'semester'],
        'competitions' => ['label' => 'Lomba', 'icon' => 'prestasi'],
        'scholarship-types' => ['label' => 'Jenis Beasiswa', 'icon' => 'beasiswa'],
        'quotas' => ['label' => 'Kuota Prestasi', 'icon' => 'grid'],
    ];

    public function index(string $section = 'prodi')
    {
        abort_unless(isset($this->sections[$section]), 404);

        return match ($section) {
            'prodi' => view('master.prodi.index', [
                'prodis' => Prodi::orderBy('nama')->paginate(request('limit', 10))->withQueryString(),
                'sectionShell' => $this->sectionShell($section, 'Master Prodi', 'Kelola data program studi.'),
            ]),
            'semester' => view('master.semester.index', [
                'semesters' => Semester::orderByDesc('id')->paginate(request('limit', 10))->withQueryString(),
                'sectionShell' => $this->sectionShell($section, 'Master Semester', 'Kelola periode akademik.'),
            ]),
            'competitions' => view('master.simple.index', [
                'master' => 'competitions',
                'config' => ['title' => 'Master Lomba', 'model' => Competition::class],
                'records' => Competition::orderBy('nama')->paginate(request('limit', 25))->withQueryString(),
                'sectionShell' => $this->sectionShell($section, 'Master Lomba', 'Kelola 23 nama lomba dan daftar lomba aktif.'),
            ]),
            'scholarship-types' => view('master.simple.index', [
                'master' => 'scholarship-types',
                'config' => ['title' => 'Jenis Beasiswa', 'model' => ScholarshipType::class],
                'records' => ScholarshipType::orderBy('nama')->paginate(request('limit', 25))->withQueryString(),
                'sectionShell' => $this->sectionShell($section, 'Jenis Beasiswa', 'Kelola pilihan beasiswa untuk form pengajuan.'),
            ]),
            'quotas' => view('master.quotas.index', [
                'quotas' => AchievementQuota::with(['semester', 'prodi'])->latest()->paginate(request('limit', 10))->withQueryString(),
                'semesters' => Semester::orderByDesc('id')->get(),
                'prodis' => Prodi::orderBy('nama')->get(),
                'sectionShell' => $this->sectionShell($section, 'Kuota Prestasi', 'Atur slot dukungan prestasi per prodi dan semester.'),
            ]),
        };
    }

    private function sectionShell(string $active, string $title, string $subtitle): array
    {
        return [
            'eyebrow' => 'Master Data',
            'title' => $title,
            'subtitle' => $subtitle,
            'items' => collect($this->sections)->map(fn ($item, $section) => [
                'label' => $item['label'],
                'icon' => $item['icon'],
                'href' => route('master-data.index', $section),
                'active' => $section === $active,
                'count' => $this->count($section),
            ])->values()->all(),
            'stats' => [
                ['label' => 'Prodi', 'value' => number_format(Prodi::count()), 'caption' => 'program studi', 'icon' => 'prodi', 'tone' => 'emerald'],
                ['label' => 'Lomba', 'value' => number_format(Competition::count()), 'caption' => 'master lomba', 'icon' => 'prestasi', 'tone' => 'blue'],
                ['label' => 'Kuota', 'value' => number_format(AchievementQuota::count()), 'caption' => 'slot prodi', 'icon' => 'semester', 'tone' => 'violet'],
            ],
        ];
    }

    private function count(string $section): int
    {
        return match ($section) {
            'prodi' => Prodi::count(),
            'semester' => Semester::count(),
            'competitions' => Competition::count(),
            'scholarship-types' => ScholarshipType::count(),
            'quotas' => AchievementQuota::count(),
        };
    }
}
