<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\AchievementQuota;
use App\Models\Competition;
use App\Models\Prodi;
use App\Models\ScholarshipType;
use App\Models\Semester;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Schema;

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
                'prodis' => $this->hasTable('prodis') ? Prodi::orderBy('nama')->paginate(request('limit', 10))->withQueryString() : $this->emptyPaginator(),
                'sectionShell' => $this->sectionShell($section, 'Master Prodi', 'Kelola data program studi.'),
            ]),
            'semester' => view('master.semester.index', [
                'semesters' => $this->hasTable('semesters') ? Semester::orderByDesc('id')->paginate(request('limit', 10))->withQueryString() : $this->emptyPaginator(),
                'sectionShell' => $this->sectionShell($section, 'Master Semester', 'Kelola periode akademik.'),
            ]),
            'competitions' => view('master.simple.index', [
                'master' => 'competitions',
                'config' => ['title' => 'Master Lomba', 'model' => Competition::class],
                'records' => $this->hasTable('competitions') ? Competition::orderBy('nama')->paginate(request('limit', 25))->withQueryString() : $this->emptyPaginator(25),
                'sectionShell' => $this->sectionShell($section, 'Master Lomba', 'Kelola 23 nama lomba dan daftar lomba aktif.'),
            ]),
            'scholarship-types' => view('master.simple.index', [
                'master' => 'scholarship-types',
                'config' => ['title' => 'Jenis Beasiswa', 'model' => ScholarshipType::class],
                'records' => $this->hasTable('scholarship_types') ? ScholarshipType::orderBy('nama')->paginate(request('limit', 25))->withQueryString() : $this->emptyPaginator(25),
                'sectionShell' => $this->sectionShell($section, 'Jenis Beasiswa', 'Kelola pilihan beasiswa untuk form pengajuan.'),
            ]),
            'quotas' => view('master.quotas.index', [
                'quotas' => $this->hasTable('achievement_quotas') ? AchievementQuota::with(['semester', 'prodi'])->latest()->paginate(request('limit', 10))->withQueryString() : $this->emptyPaginator(),
                'semesters' => $this->hasTable('semesters') ? Semester::orderByDesc('id')->get() : collect(),
                'prodis' => $this->hasTable('prodis') ? Prodi::orderBy('nama')->get() : collect(),
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
                ['label' => 'Prodi', 'value' => number_format($this->countModel('prodis', Prodi::class)), 'caption' => 'program studi', 'icon' => 'prodi', 'tone' => 'emerald'],
                ['label' => 'Semester', 'value' => number_format($this->countModel('semesters', Semester::class)), 'caption' => 'periode akademik', 'icon' => 'semester', 'tone' => 'slate'],
                ['label' => 'Lomba', 'value' => number_format($this->countModel('competitions', Competition::class)), 'caption' => 'master lomba', 'icon' => 'prestasi', 'tone' => 'blue'],
                ['label' => 'Kuota', 'value' => number_format($this->countModel('achievement_quotas', AchievementQuota::class)), 'caption' => 'slot prodi', 'icon' => 'semester', 'tone' => 'violet'],
            ],
        ];
    }

    private function count(string $section): int
    {
        return match ($section) {
            'prodi' => $this->countModel('prodis', Prodi::class),
            'semester' => $this->countModel('semesters', Semester::class),
            'competitions' => $this->countModel('competitions', Competition::class),
            'scholarship-types' => $this->countModel('scholarship_types', ScholarshipType::class),
            'quotas' => $this->countModel('achievement_quotas', AchievementQuota::class),
        };
    }

    private function hasTable(string $table): bool
    {
        return Schema::hasTable($table);
    }

    private function countModel(string $table, string $model): int
    {
        return $this->hasTable($table) ? $model::count() : 0;
    }

    private function emptyPaginator(?int $perPage = null): LengthAwarePaginator
    {
        return new LengthAwarePaginator(collect(), 0, $perPage ?? request('limit', 10), 1, ['path' => request()->url()]);
    }
}
