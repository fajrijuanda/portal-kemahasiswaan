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
        'quotas' => ['label' => 'Kuota Prestasi', 'icon' => 'semester'],
    ];

    public function overview()
    {
        $overview = [
            'eyebrow' => 'Master Data',
            'title' => 'Kelola Master Data',
            'subtitle' => 'Pilih tabel data master yang ingin dikelola.',
            'items' => collect($this->sections)->map(fn ($item, $section) => [
                'label' => $item['label'],
                'module' => $section,
                'icon' => $item['icon'],
                'tone' => match($section) { 'prodi' => 'emerald', 'semester' => 'slate', 'competitions' => 'blue', 'scholarship-types' => 'amber', 'quotas' => 'violet' },
                'href' => route('master-data.index', $section),
                'description' => match($section) { 'prodi' => 'Kelola data program studi.', 'semester' => 'Kelola periode akademik.', 'competitions' => 'Master data nama lomba.', 'scholarship-types' => 'Jenis beasiswa.', 'quotas' => 'Slot dukungan prestasi per prodi.' },
            ])->values()->all(),
        ];
        
        return view('records.overview', [
            'overview' => $overview,
            'stats' => collect($overview['items'])->map(fn ($item) => [
                'label' => $item['label'],
                'value' => number_format($this->count($item['module'])),
                'caption' => 'data',
                'icon' => $item['icon'],
                'tone' => $item['tone'],
            ])->values()->all(),
        ]);
    }

    public function index(string $section = 'prodi')
    {
        abort_unless(isset($this->sections[$section]), 404);

        return match ($section) {
            'prodi' => view('master.prodi.index', [
                'prodis' => $this->hasTable('prodis') ? $this->prodiQuery()->paginate($this->limit())->withQueryString() : $this->emptyPaginator(),
                'faculties' => $this->hasTable('prodis') ? Prodi::query()->whereNotNull('fakultas')->where('fakultas', '!=', '')->distinct()->orderBy('fakultas')->pluck('fakultas') : collect(),
                'sectionShell' => $this->sectionShell($section, 'Master Prodi', 'Kelola data program studi.'),
            ]),
            'semester' => view('master.semester.index', [
                'semesters' => $this->hasTable('semesters') ? $this->semesterQuery()->paginate($this->limit())->withQueryString() : $this->emptyPaginator(),
                'sectionShell' => $this->sectionShell($section, 'Master Semester', 'Kelola periode akademik.'),
            ]),
            'competitions' => view('master.simple.index', [
                'master' => 'competitions',
                'config' => ['title' => 'Master Lomba', 'model' => Competition::class],
                'records' => $this->hasTable('competitions') ? $this->simpleMasterQuery(Competition::class)->paginate($this->limit(25))->withQueryString() : $this->emptyPaginator(25),
                'sectionShell' => $this->sectionShell($section, 'Master Lomba', 'Kelola 23 nama lomba dan daftar lomba aktif.'),
            ]),
            'scholarship-types' => view('master.simple.index', [
                'master' => 'scholarship-types',
                'config' => ['title' => 'Jenis Beasiswa', 'model' => ScholarshipType::class],
                'records' => $this->hasTable('scholarship_types') ? $this->simpleMasterQuery(ScholarshipType::class)->paginate($this->limit(25))->withQueryString() : $this->emptyPaginator(25),
                'sectionShell' => $this->sectionShell($section, 'Jenis Beasiswa', 'Kelola pilihan beasiswa untuk form pengajuan.'),
            ]),
            'quotas' => view('master.quotas.index', [
                'quotas' => $this->hasTable('achievement_quotas') ? $this->quotaQuery()->paginate($this->limit())->withQueryString() : $this->emptyPaginator(),
                'prodis' => $this->hasTable('prodis') ? Prodi::query()->orderBy('nama')->get() : collect(),
                'semesters' => $this->hasTable('semesters') ? Semester::query()->orderByDesc('id')->get() : collect(),
                'sectionShell' => $this->sectionShell($section, 'Kuota Prestasi', 'Kelola slot dukungan prestasi per prodi dan semester.'),
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

    private function prodiQuery()
    {
        return Prodi::query()
            ->when(request('q'), fn ($query, $search) => $query->where(function ($query) use ($search) {
                $query->where('nama', 'like', "%{$search}%")
                    ->orWhere('kode', 'like', "%{$search}%")
                    ->orWhere('fakultas', 'like', "%{$search}%");
            }))
            ->when(request('fakultas'), fn ($query, $faculty) => $query->where('fakultas', $faculty))
            ->orderBy('nama');
    }

    private function semesterQuery()
    {
        return Semester::query()
            ->when(request('q'), fn ($query, $search) => $query->where(function ($query) use ($search) {
                $query->where('nama', 'like', "%{$search}%")
                    ->orWhere('tahun_akademik', 'like', "%{$search}%")
                    ->orWhere('periode', 'like', "%{$search}%");
            }))
            ->when(request('periode'), fn ($query, $periode) => $query->where('periode', $periode))
            ->when(request('status') !== null && request('status') !== '', fn ($query) => $query->where('is_active', request('status') === 'active'))
            ->orderByDesc('id');
    }

    private function simpleMasterQuery(string $model)
    {
        return $model::query()
            ->when(request('q'), fn ($query, $search) => $query->where('nama', 'like', "%{$search}%"))
            ->when(request('status') !== null && request('status') !== '', fn ($query) => $query->where('is_active', request('status') === 'active'))
            ->orderBy('nama');
    }

    private function quotaQuery()
    {
        return AchievementQuota::query()
            ->with(['semester', 'prodi'])
            ->when(request('q'), fn ($query, $search) => $query->where(function ($query) use ($search) {
                $query->whereHas('prodi', fn ($query) => $query->where('nama', 'like', "%{$search}%")->orWhere('kode', 'like', "%{$search}%"))
                    ->orWhereHas('semester', fn ($query) => $query->where('nama', 'like', "%{$search}%")->orWhere('tahun_akademik', 'like', "%{$search}%"));
            }))
            ->when(request('prodi_id'), fn ($query, $prodiId) => $query->where('prodi_id', $prodiId))
            ->when(request('semester_id'), fn ($query, $semesterId) => $query->where('semester_id', $semesterId))
            ->latest('id');
    }



    private function countModel(string $table, string $model): int
    {
        return $this->hasTable($table) ? $model::count() : 0;
    }

    private function limit(int $default = 10): int
    {
        return in_array((int) request('limit', $default), [10, 25, 50, 100], true) ? (int) request('limit', $default) : $default;
    }

    private function emptyPaginator(?int $perPage = null): LengthAwarePaginator
    {
        return new LengthAwarePaginator(collect(), 0, $perPage ?? $this->limit(), 1, ['path' => request()->url()]);
    }
}
