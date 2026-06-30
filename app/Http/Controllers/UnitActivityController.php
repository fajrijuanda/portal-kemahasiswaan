<?php

namespace App\Http\Controllers;

use App\Models\Ormawa;
use App\Models\Prodi;
use App\Models\Semester;
use App\Models\UnitActivity;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Schema;

class UnitActivityController extends Controller
{
    private array $units = [
        'humas-marketing' => [
            'title' => 'Humas Marketing',
            'subtitle' => 'Aktivitas promosi, publikasi, dan komunikasi kampus.',
            'icon' => 'grid',
            'tone' => 'rose',
        ],
        'science-center' => [
            'title' => 'Science Center',
            'subtitle' => 'Program, agenda, dan aktivitas science center.',
            'icon' => 'prodi',
            'tone' => 'cyan',
        ],
        'pengembangan-ormawa' => [
            'title' => 'Pengembangan Ormawa',
            'subtitle' => 'Pembinaan, kegiatan, dan pengembangan organisasi mahasiswa.',
            'icon' => 'user',
            'tone' => 'amber',
        ],
        'alumni-pusat-karir' => [
            'title' => 'Alumni dan Pusat Karir',
            'subtitle' => 'Aktivitas alumni, karir, dan relasi industri.',
            'icon' => 'access',
            'tone' => 'slate',
        ],
    ];

    public function overview()
    {
        $overview = [
            'eyebrow' => 'Unit Data',
            'title' => 'Layanan Unit Kegiatan',
            'subtitle' => 'Pilih unit kegiatan untuk mengelola data aktivitasnya.',
            'items' => collect($this->units)->except('pengembangan-ormawa')->map(fn ($config, $unit) => [
                'label' => $config['title'],
                'module' => $unit,
                'icon' => $config['icon'],
                'tone' => $config['tone'],
                'href' => route('unit-activities.index', $unit),
                'description' => $config['subtitle'],
            ])->values()->all(),
        ];

        return view('records.overview', [
            'overview' => $overview,
            'stats' => collect($overview['items'])->map(fn ($item) => [
                'label' => $item['label'],
                'value' => number_format($this->hasTable('unit_activities') ? UnitActivity::where('unit', $item['module'])->count() : 0),
                'caption' => 'aktivitas',
                'icon' => $item['icon'],
                'tone' => $item['tone'],
            ])->values()->all(),
        ]);
    }

    public function index(Request $request, string $unit)
    {
        $config = $this->config($unit);
        $relations = ['semester', 'prodi', 'creator'];
        if ($this->hasOrmawaColumn()) {
            $relations[] = 'ormawa';
        }

        $query = $this->hasTable('unit_activities')
            ? UnitActivity::with($relations)->where('unit', $unit)->latest()
            : null;

        if ($query) {
            $this->applyScopeAndFilters($query, $request);
        }

        $baseQuery = $this->hasTable('unit_activities') ? UnitActivity::query()->where('unit', $unit) : null;
        if ($request->user()->hasRole('kaprodi')) {
            $baseQuery?->where('prodi_id', $request->user()->prodi_id);
        }

        return view('unit-activities.index', [
            'unit' => $unit,
            'config' => $config,
            'records' => $query ? $query->paginate(request('limit', 10))->withQueryString() : $this->emptyPaginator(),
            'totalRecords' => $baseQuery ? (clone $baseQuery)->count() : 0,
            'completedRecords' => $baseQuery ? (clone $baseQuery)->where('status', 'Selesai')->count() : 0,
            'semesters' => $this->hasTable('semesters') ? Semester::orderByDesc('id')->get() : collect(),
            'prodis' => $this->hasTable('prodis') ? Prodi::orderBy('nama')->get() : collect(),
            'ormawas' => $this->hasOrmawaColumn() ? Ormawa::where('status', 'Aktif')->orderBy('nama')->get() : collect(),
            'canUseOrmawa' => $this->hasOrmawaColumn(),
            'sectionShell' => [
                'eyebrow' => 'Unit Data',
                'title' => $config['title'],
                'subtitle' => 'Kelola data unit layanan khusus dari satu halaman ringkas.',
                'items' => $this->unitSectionItems($unit),
                'stats' => [
                    ['label' => 'Total Aktivitas', 'value' => number_format($baseQuery ? (clone $baseQuery)->count() : 0), 'caption' => $config['title'], 'icon' => $config['icon'], 'tone' => $config['tone']],
                    ['label' => 'Berjalan', 'value' => number_format($baseQuery ? (clone $baseQuery)->where('status', 'Berjalan')->count() : 0), 'caption' => 'aktif', 'icon' => 'event', 'tone' => 'blue'],
                    ['label' => 'Selesai', 'value' => number_format($baseQuery ? (clone $baseQuery)->where('status', 'Selesai')->count() : 0), 'caption' => 'aktivitas tuntas', 'icon' => 'prestasi', 'tone' => 'emerald'],
                    ['label' => 'Draft/Tertunda', 'value' => number_format($baseQuery ? (clone $baseQuery)->whereIn('status', ['Draft', 'Tertunda'])->count() : 0), 'caption' => 'perlu tindak lanjut', 'icon' => 'grid', 'tone' => 'amber'],
                ],
            ],
        ]);
    }

    public function store(Request $request, string $unit)
    {
        $config = $this->config($unit);
        $data = $this->validated($request);
        $data['unit'] = $unit;
        $data['created_by'] = $request->user()->id;

        if ($request->user()->hasRole('kaprodi')) {
            $data['prodi_id'] = $request->user()->prodi_id;
        }

        UnitActivity::create($data);

        return redirect($this->indexUrl($unit))->with('status', $config['title'].' berhasil ditambahkan.');
    }

    public function update(Request $request, string $unit, UnitActivity $activity)
    {
        $config = $this->config($unit);
        $this->authorizeUnitRecord($activity, $unit);
        $data = $this->validated($request);

        if ($request->user()->hasRole('kaprodi')) {
            $data['prodi_id'] = $request->user()->prodi_id;
        }

        $activity->update($data);

        return redirect($this->indexUrl($unit))->with('status', $config['title'].' berhasil diperbarui.');
    }

    public function destroy(string $unit, UnitActivity $activity)
    {
        $config = $this->config($unit);
        $this->authorizeUnitRecord($activity, $unit);
        $activity->delete();

        return back()->with('status', $config['title'].' berhasil dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'semester_id' => ['required', 'exists:semesters,id'],
            'prodi_id' => [$request->user()->hasRole('kaprodi') ? 'nullable' : 'required', 'nullable', 'exists:prodis,id'],
            'ormawa_id' => $this->hasOrmawaColumn() ? ['nullable', 'exists:ormawas,id'] : ['exclude'],
            'judul' => ['required', 'string', 'max:255'],
            'penanggung_jawab' => ['nullable', 'string', 'max:255'],
            'tanggal' => ['nullable', 'date'],
            'status' => ['required', 'string', 'in:Draft,Berjalan,Selesai,Tertunda'],
            'catatan' => ['nullable', 'string'],
        ]);
    }

    private function applyScopeAndFilters(\Illuminate\Database\Eloquent\Builder $query, Request $request): void
    {
        if ($request->user()->hasRole('kaprodi')) {
            $query->where('prodi_id', $request->user()->prodi_id);
        } elseif ($request->filled('prodi_id')) {
            $query->where('prodi_id', $request->integer('prodi_id'));
        }

        if ($request->filled('semester_id')) {
            $query->where('semester_id', $request->integer('semester_id'));
        }

        $keyword = trim((string) $request->query('q', ''));
        if ($keyword !== '') {
            $query->where(function ($inner) use ($keyword) {
                $inner->where('judul', 'like', "%{$keyword}%")
                    ->orWhere('penanggung_jawab', 'like', "%{$keyword}%")
                    ->orWhere('status', 'like', "%{$keyword}%")
                    ->orWhere('catatan', 'like', "%{$keyword}%")
                    ->orWhereHas('prodi', fn ($prodi) => $prodi->where('nama', 'like', "%{$keyword}%"))
                    ->orWhereHas('semester', fn ($semester) => $semester->where('nama', 'like', "%{$keyword}%"));
            });
        }
    }

    private function authorizeUnitRecord(UnitActivity $activity, string $unit): void
    {
        abort_unless($activity->unit === $unit, 404);

        if (request()->user()->hasRole('kaprodi')) {
            abort_unless($activity->prodi_id === request()->user()->prodi_id, 403);
        }
    }

    private function config(string $unit): array
    {
        abort_unless(isset($this->units[$unit]), 404);

        return $this->units[$unit];
    }

    private function unitSectionItems(string $activeUnit): array
    {
        return collect($this->units)
            ->except('pengembangan-ormawa')
            ->map(fn ($config, $unit) => [
                'label' => $config['title'],
                'icon' => $config['icon'],
                'href' => route('unit-activities.index', $unit),
                'active' => $unit === $activeUnit,
                'count' => $this->hasTable('unit_activities') ? UnitActivity::where('unit', $unit)->count() : 0,
            ])
            ->values()
            ->all();
    }

    private function indexUrl(string $unit): string
    {
        if ($unit === 'pengembangan-ormawa') {
            return route('ormawa.index', 'kegiatan');
        }

        return route('unit-activities.index', $unit);
    }

    private function hasTable(string $table): bool
    {
        return Schema::hasTable($table);
    }

    private function hasOrmawaColumn(): bool
    {
        return $this->hasTable('ormawas')
            && $this->hasTable('unit_activities')
            && Schema::hasColumn('unit_activities', 'ormawa_id');
    }

    private function emptyPaginator(): LengthAwarePaginator
    {
        return new LengthAwarePaginator(collect(), 0, request('limit', 10), 1, ['path' => request()->url()]);
    }
}
