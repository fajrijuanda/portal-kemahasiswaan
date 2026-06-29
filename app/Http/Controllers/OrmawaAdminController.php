<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Ormawa;
use App\Models\OrmawaProposal;
use App\Models\Prodi;
use App\Models\Semester;
use App\Models\UnitActivity;
use App\Models\User;

class OrmawaAdminController extends Controller
{
    private array $sections = [
        'data-ormawa' => ['label' => 'Data Ormawa', 'icon' => 'user'],
        'kegiatan' => ['label' => 'Kegiatan', 'icon' => 'event'],
        'proposal' => ['label' => 'Proposal', 'icon' => 'grid'],
        'reimbursement' => ['label' => 'Reimbursement', 'icon' => 'beasiswa'],
    ];

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
        return view('master.ormawa.index', [
            'ormawas' => Ormawa::with(['user', 'activities', 'proposals', 'reimbursements'])->orderBy('nama')->paginate(request('limit', 10))->withQueryString(),
            'users' => User::role('ormawa')->orderBy('name')->get(),
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
        $query = UnitActivity::with(['semester', 'prodi', 'ormawa', 'creator'])->where('unit', $unit)->latest();
        $baseQuery = UnitActivity::query()->where('unit', $unit);

        return view('unit-activities.index', [
            'unit' => $unit,
            'config' => $config,
            'records' => $query->paginate(request('limit', 10))->withQueryString(),
            'totalRecords' => (clone $baseQuery)->count(),
            'completedRecords' => (clone $baseQuery)->where('status', 'Selesai')->count(),
            'semesters' => Semester::orderByDesc('id')->get(),
            'prodis' => Prodi::orderBy('nama')->get(),
            'ormawas' => Ormawa::where('status', 'Aktif')->orderBy('nama')->get(),
            'sectionShell' => $this->sectionShell('kegiatan', 'Kegiatan Ormawa', 'Aktivitas Ormawa berada dalam satu ruang dengan data dan pengajuan Ormawa.'),
        ]);
    }

    private function proposal()
    {
        return view('ormawa.admin.index', [
            'section' => 'proposal',
            'title' => 'Proposal Ormawa',
            'subtitle' => 'Pantau proposal kegiatan yang diajukan akun Ormawa.',
            'records' => OrmawaProposal::with(['ormawa', 'semester', 'creator'])->latest()->paginate(request('limit', 10))->withQueryString(),
            'sectionShell' => $this->sectionShell('proposal', 'Proposal Ormawa', 'Pengajuan proposal kegiatan Ormawa untuk review admin/kabag.'),
        ]);
    }

    private function reimbursement()
    {
        return view('ormawa.admin.index', [
            'section' => 'reimbursement',
            'title' => 'Reimbursement Ormawa',
            'subtitle' => 'Pantau reimbursement acara yang memiliki relasi Ormawa.',
            'records' => Event::with(['ormawa', 'semester', 'prodi', 'creator'])->whereNotNull('ormawa_id')->latest()->paginate(request('limit', 10))->withQueryString(),
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
                ['label' => 'Ormawa', 'value' => number_format(Ormawa::count()), 'caption' => 'terdaftar', 'icon' => 'user', 'tone' => 'amber'],
                ['label' => 'Proposal', 'value' => number_format(OrmawaProposal::count()), 'caption' => 'pengajuan', 'icon' => 'grid', 'tone' => 'blue'],
                ['label' => 'Reimbursement', 'value' => number_format(Event::whereNotNull('ormawa_id')->count()), 'caption' => 'ormawa', 'icon' => 'beasiswa', 'tone' => 'emerald'],
            ],
        ];
    }

    private function count(string $section): int
    {
        return match ($section) {
            'data-ormawa' => Ormawa::count(),
            'kegiatan' => UnitActivity::where('unit', 'pengembangan-ormawa')->count(),
            'proposal' => OrmawaProposal::count(),
            'reimbursement' => Event::whereNotNull('ormawa_id')->count(),
        };
    }
}
