<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Ormawa;
use App\Models\OrmawaProposal;
use App\Models\Prodi;
use App\Models\Semester;
use App\Models\UnitActivity;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Schema;

class OrmawaAdminController extends Controller
{
    private array $sections = [
        'data-ormawa' => ['label' => 'Data Ormawa', 'icon' => 'user'],
        'kegiatan' => ['label' => 'Kegiatan', 'icon' => 'event'],
        'proposal' => ['label' => 'Proposal', 'icon' => 'grid'],
        'reimbursement' => ['label' => 'Reimbursement', 'icon' => 'beasiswa'],
    ];

    public function overview()
    {
        $overview = [
            'eyebrow' => 'Ormawa Admin',
            'title' => 'Administrasi Ormawa',
            'subtitle' => 'Pilih tabel administrasi organisasi mahasiswa.',
            'items' => collect($this->sections)->map(fn ($item, $section) => [
                'label' => $item['label'],
                'module' => $section,
                'icon' => $item['icon'],
                'tone' => match($section) { 'data-ormawa' => 'amber', 'kegiatan' => 'teal', 'proposal' => 'blue', 'reimbursement' => 'emerald' },
                'href' => route('ormawa-admin.index', $section),
                'description' => match($section) { 'data-ormawa' => 'Kelola profil, akun, dan overview ormawa.', 'kegiatan' => 'Pembinaan dan pengembangan ormawa.', 'proposal' => 'Pantau pengajuan proposal.', 'reimbursement' => 'Pantau pengajuan reimbursement.' },
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

    public function index(string $section = 'data-ormawa')
    {
        abort_unless(isset($this->sections[$section]), 404);

        return match ($section) {
            'data-ormawa' => $this->dataOrmawa(),
            'kegiatan' => $this->kegiatan(),
            'proposal' => $this->proposal(),
            'reimbursement' => $this->reimbursement(),
        };
    }

    private function dataOrmawa()
    {
        $query = $this->hasTable('ormawas') ? Ormawa::query()->orderBy('nama') : null;

        if ($query && $this->hasOrmawaUserColumn()) {
            $query->with('user');
        }

        if ($query && $this->hasUnitOrmawaColumn()) {
            $query->withCount('activities');
        }
        if ($query && $this->hasTable('ormawa_proposals')) {
            $query->withCount('proposals');
        }
        if ($query && $this->hasEventOrmawaColumn()) {
            $query->withCount('reimbursements');
        }

        return view('master.ormawa.index', [
            'ormawas' => $query ? $query->paginate(request('limit', 10))->withQueryString() : $this->emptyPaginator(),
            'users' => $this->hasOrmawaUserColumn() && $this->hasPermissionTables() ? User::role('ormawa')->orderBy('name')->get() : collect(),
            'canLinkUser' => $this->hasOrmawaUserColumn(),
            'sectionShell' => $this->sectionShell('data-ormawa', 'Data Ormawa', 'Kelola profil, akun, dan overview organisasi mahasiswa.'),
        ]);
    }

    private function kegiatan()
    {
        $unit = 'pengembangan-ormawa';
        $config = [
            'title' => 'Kegiatan Ormawa',
            'subtitle' => 'Pembinaan, kegiatan, dan pengembangan organisasi mahasiswa.',
            'icon' => 'event',
            'tone' => 'amber',
        ];
        $relations = ['semester', 'prodi', 'creator'];
        if ($this->hasUnitOrmawaColumn()) {
            $relations[] = 'ormawa';
        }

        $query = $this->hasTable('unit_activities') ? UnitActivity::with($relations)->where('unit', $unit)->latest() : null;
        $baseQuery = $this->hasTable('unit_activities') ? UnitActivity::query()->where('unit', $unit) : null;

        return view('unit-activities.index', [
            'unit' => $unit,
            'config' => $config,
            'records' => $query ? $query->paginate(request('limit', 10))->withQueryString() : $this->emptyPaginator(),
            'totalRecords' => $baseQuery ? (clone $baseQuery)->count() : 0,
            'completedRecords' => $baseQuery ? (clone $baseQuery)->where('status', 'Selesai')->count() : 0,
            'semesters' => $this->hasTable('semesters') ? Semester::orderByDesc('id')->get() : collect(),
            'prodis' => $this->hasTable('prodis') ? Prodi::orderBy('nama')->get() : collect(),
            'ormawas' => $this->hasUnitOrmawaColumn() ? Ormawa::where('status', 'Aktif')->orderBy('nama')->get() : collect(),
            'canUseOrmawa' => $this->hasUnitOrmawaColumn(),
            'sectionShell' => $this->sectionShell('kegiatan', 'Kegiatan Ormawa', 'Aktivitas Ormawa berada dalam satu ruang dengan data dan pengajuan Ormawa.'),
        ]);
    }

    private function proposal()
    {
        return view('ormawa.admin.index', [
            'section' => 'proposal',
            'title' => 'Proposal Ormawa',
            'subtitle' => 'Pantau proposal kegiatan yang diajukan akun Ormawa.',
            'records' => $this->hasTable('ormawa_proposals') ? OrmawaProposal::with(['ormawa', 'semester', 'creator'])->latest()->paginate(request('limit', 10))->withQueryString() : $this->emptyPaginator(),
            'sectionShell' => $this->sectionShell('proposal', 'Proposal Ormawa', 'Pengajuan proposal kegiatan Ormawa untuk review admin/kabag.'),
        ]);
    }

    private function reimbursement()
    {
        return view('ormawa.admin.index', [
            'section' => 'reimbursement',
            'title' => 'Reimbursement Ormawa',
            'subtitle' => 'Pantau reimbursement acara yang memiliki relasi Ormawa.',
            'records' => $this->hasEventOrmawaColumn() ? Event::with(['ormawa', 'semester', 'prodi', 'creator'])->whereNotNull('ormawa_id')->latest()->paginate(request('limit', 10))->withQueryString() : $this->emptyPaginator(),
            'sectionShell' => $this->sectionShell('reimbursement', 'Reimbursement Ormawa', 'Reimbursement acara Ormawa beserta file syarat pendukung.'),
        ]);
    }

    private function sectionShell(string $active, string $title, string $subtitle): array
    {
        return [
            'eyebrow' => 'Ormawa Admin',
            'title' => $title,
            'subtitle' => $subtitle,
            'items' => collect($this->sections)->map(fn ($item, $section) => [
                'label' => $item['label'],
                'icon' => $item['icon'],
                'href' => route('ormawa-admin.index', $section),
                'active' => $section === $active,
                'count' => $this->count($section),
            ])->values()->all(),
            'stats' => [
                ['label' => 'Ormawa', 'value' => number_format($this->countModel('ormawas', Ormawa::class)), 'caption' => 'terdaftar', 'icon' => 'user', 'tone' => 'amber'],
                ['label' => 'Kegiatan', 'value' => number_format($this->hasTable('unit_activities') ? UnitActivity::where('unit', 'pengembangan-ormawa')->count() : 0), 'caption' => 'aktivitas', 'icon' => 'event', 'tone' => 'teal'],
                ['label' => 'Proposal', 'value' => number_format($this->countModel('ormawa_proposals', OrmawaProposal::class)), 'caption' => 'pengajuan', 'icon' => 'grid', 'tone' => 'blue'],
                ['label' => 'Reimbursement', 'value' => number_format($this->hasEventOrmawaColumn() ? Event::whereNotNull('ormawa_id')->count() : 0), 'caption' => 'ormawa', 'icon' => 'beasiswa', 'tone' => 'emerald'],
            ],
        ];
    }

    private function count(string $section): int
    {
        return match ($section) {
            'data-ormawa' => $this->countModel('ormawas', Ormawa::class),
            'kegiatan' => $this->hasTable('unit_activities') ? UnitActivity::where('unit', 'pengembangan-ormawa')->count() : 0,
            'proposal' => $this->countModel('ormawa_proposals', OrmawaProposal::class),
            'reimbursement' => $this->hasEventOrmawaColumn() ? Event::whereNotNull('ormawa_id')->count() : 0,
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

    private function hasOrmawaUserColumn(): bool
    {
        return $this->hasTable('ormawas')
            && $this->hasTable('users')
            && Schema::hasColumn('ormawas', 'user_id');
    }

    private function hasPermissionTables(): bool
    {
        return $this->hasTable('roles')
            && $this->hasTable('model_has_roles');
    }

    private function hasUnitOrmawaColumn(): bool
    {
        return $this->hasTable('ormawas')
            && $this->hasTable('unit_activities')
            && Schema::hasColumn('unit_activities', 'ormawa_id');
    }

    private function hasEventOrmawaColumn(): bool
    {
        return $this->hasTable('ormawas')
            && $this->hasTable('events')
            && Schema::hasColumn('events', 'ormawa_id');
    }

    private function emptyPaginator(): LengthAwarePaginator
    {
        return new LengthAwarePaginator(collect(), 0, request('limit', 10), 1, ['path' => request()->url()]);
    }
}
