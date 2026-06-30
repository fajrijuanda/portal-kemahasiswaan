<?php

namespace App\Http\Controllers;

use App\Models\AchievementQuota;
use App\Models\Beasiswa;
use App\Models\ClaimFasilitas;
use App\Models\ClaimTransport;
use App\Models\Event;
use App\Models\Prestasi;
use App\Models\Prodi;
use App\Models\Semester;
use App\Models\TracerStudy;
use App\Models\UnitActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    private array $unitLabels = [
        'humas-marketing' => 'Humas Marketing',
        'science-center' => 'Science Center',
        'pengembangan-ormawa' => 'Pengembangan Ormawa',
        'alumni-pusat-karir' => 'Alumni dan Pusat Karir',
    ];

    public function index(Request $request)
    {
        return view('dashboard.index', [
            'cards' => $this->summaryCards($request),
        ]);
    }

    public function rekap(Request $request)
    {
        $semesterId = $request->integer('semester_id') ?: null;
        $prodiId = $this->visibleProdiId($request);

        return view('dashboard.rekap', [
            'semesters' => Semester::orderByDesc('id')->get(),
            'prodis' => Prodi::orderBy('nama')->get(),
            'selectedSemester' => $semesterId,
            'selectedProdi' => $prodiId,
            'cards' => $this->summaryCards($request),
            'dashboardMenu' => $this->dashboardMenu($request),
            'achievementQuotas' => $this->achievementQuotas(),
        ]);
    }

    public function summaryCardCharts(Request $request)
    {
        $semesterId = $request->integer('semester_id') ?: null;
        $prodiId = $this->visibleProdiId($request);

        $prestasi = $this->countFor(Prestasi::query(), $semesterId, $prodiId);
        $event = $this->countFor(Event::query(), $semesterId, $prodiId);
        $tracer = $this->sumFor(TracerStudy::query(), 'jumlah_input', $semesterId, $prodiId);
        $beasiswa = $this->countFor(Beasiswa::query(), $semesterId, $prodiId);

        return response()->json([
            'Prestasi' => $this->miniDataset($prestasi, 'Prestasi', 'Belum tercatat'),
            'Event/Reimbursement' => $this->miniDataset($event, 'Event', 'Belum tercatat'),
            'Tracer Study Input' => $this->miniDataset($tracer, 'Sudah input', 'Belum input'),
            'Beasiswa' => $this->miniDataset($beasiswa, 'Beasiswa', 'Belum tercatat'),
            'Humas Marketing' => $this->miniDataset($this->unitCount('humas-marketing', $semesterId, $prodiId), 'Humas Marketing', 'Belum ada data'),
            'Science Center' => $this->miniDataset($this->unitCount('science-center', $semesterId, $prodiId), 'Science Center', 'Belum ada data'),
            'Pengembangan Ormawa' => $this->miniDataset($this->unitCount('pengembangan-ormawa', $semesterId, $prodiId), 'Ormawa', 'Belum ada data'),
            'Alumni dan Pusat Karir' => $this->miniDataset($this->unitCount('alumni-pusat-karir', $semesterId, $prodiId), 'Alumni dan Karir', 'Belum ada data'),
        ]);
    }

    public function unitActivityChart(Request $request, string $unit)
    {
        abort_unless(array_key_exists($unit, $this->unitLabels), 404);

        $semesterId = $request->integer('semester_id') ?: null;
        $prodiId = $this->visibleProdiId($request);
        $query = UnitActivity::query()->where('unit', $unit);
        $this->applyFilters($query, $semesterId, $prodiId);

        $rows = $query
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->orderBy('status')
            ->get();

        if ($rows->isEmpty()) {
            return response()->json([
                'labels' => ['Belum ada data'],
                'data' => [1],
                'links' => [$this->unitLink($unit, $semesterId, $prodiId)],
                'empty' => true,
            ]);
        }

        return response()->json([
            'labels' => $rows->pluck('status'),
            'data' => $rows->pluck('total'),
            'links' => $rows->map(fn () => $this->unitLink($unit, $semesterId, $prodiId)),
            'empty' => false,
        ]);
    }

    public function prestasiBySemester(Request $request)
    {
        return $this->chartByRelation(Prestasi::query(), 'semesters', 'semester_id', 'nama', '/prestasi', $request);
    }

    public function prestasiByProdi(Request $request)
    {
        return $this->chartByRelation(Prestasi::query(), 'prodis', 'prodi_id', 'nama', '/prestasi', $request);
    }

    public function claims(Request $request)
    {
        $semesterId = $request->integer('semester_id') ?: null;
        $prodiId = $this->visibleProdiId($request);

        return response()->json([
            'labels' => ['Claim Transport', 'Claim Fasilitas'],
            'data' => [
                $this->countFor(Event::query()->where('jenis_reimbursement', 'Transport'), $semesterId, $prodiId),
                $this->countFor(Event::query()->where('jenis_reimbursement', 'Fasilitas'), $semesterId, $prodiId),
            ],
            'links' => [
                route('reimburse.table', ['semester_id' => $semesterId, 'prodi_id' => $prodiId]),
                route('reimburse.table', ['semester_id' => $semesterId, 'prodi_id' => $prodiId]),
            ],
        ]);
    }

    public function beasiswa(Request $request)
    {
        return $this->chartByRelation(Beasiswa::query(), 'prodis', 'prodi_id', 'nama', '/beasiswa', $request);
    }

    public function tracerStudy(Request $request)
    {
        $query = TracerStudy::query();
        $semesterId = $request->integer('semester_id') ?: null;
        $prodiId = $this->visibleProdiId($request);
        $this->applyFilters($query, $semesterId, $prodiId);

        $rows = $query
            ->join('prodis', 'tracer_studies.prodi_id', '=', 'prodis.id')
            ->select('prodis.id', 'prodis.nama', DB::raw('SUM(jumlah_input) as total'))
            ->groupBy('prodis.id', 'prodis.nama')
            ->orderBy('prodis.nama')
            ->get();

        return response()->json([
            'labels' => $rows->pluck('nama'),
            'data' => $rows->pluck('total'),
            'links' => $rows->map(fn ($row) => route('tracer.table', ['prodi_id' => $row->id, 'semester_id' => $semesterId])),
        ]);
    }

    private function chartByRelation(Builder $query, string $table, string $column, string $label, string $path, Request $request)
    {
        $semesterId = $request->integer('semester_id') ?: null;
        $prodiId = $this->visibleProdiId($request);
        $this->applyFilters($query, $semesterId, $prodiId);

        $baseTable = $query->getModel()->getTable();
        $rows = $query
            ->join($table, "{$baseTable}.{$column}", '=', "{$table}.id")
            ->select("{$table}.id", "{$table}.{$label}", DB::raw('COUNT(*) as total'))
            ->groupBy("{$table}.id", "{$table}.{$label}")
            ->orderBy("{$table}.{$label}")
            ->get();

        return response()->json([
            'labels' => $rows->pluck($label),
            'data' => $rows->pluck('total'),
            'links' => $rows->map(fn ($row) => $this->recordLink($path, $row->id, $column, $semesterId)),
        ]);
    }

    private function recordLink(string $path, int $id, string $column, ?int $semesterId): string
    {
        $module = trim($path, '/');

        return route(match ($module) {
            'prestasi' => 'prestasi.table',
            'beasiswa' => 'beasiswa.table',
            'tracer-study' => 'tracer.table',
            default => 'prestasi.table',
        }, array_filter([
            $column => $id,
            'semester_id' => $semesterId,
        ]));
    }

    private function unitLink(string $unit, ?int $semesterId, ?int $prodiId): string
    {
        $params = array_filter([
            'semester_id' => $semesterId,
            'prodi_id' => $prodiId,
        ]);

        if ($unit === 'pengembangan-ormawa') {
            return route('ormawa.index', array_merge(['section' => 'kegiatan'], $params));
        }

        return route('unit-activities.index', array_merge(['unit' => $unit], $params));
    }

    private function miniDataset(int $value, string $label, string $emptyLabel): array
    {
        return [
            'labels' => [$label, $emptyLabel],
            'data' => [$value, max(1, $value === 0 ? 1 : (int) ceil($value * 0.18))],
        ];
    }

    private function unitCount(string $unit, ?int $semesterId, ?int $prodiId): int
    {
        return $this->countFor(UnitActivity::query()->where('unit', $unit), $semesterId, $prodiId);
    }

    private function countFor(Builder $query, ?int $semesterId, ?int $prodiId): int
    {
        return $this->applyFilters($query, $semesterId, $prodiId)->count();
    }

    private function sumFor(Builder $query, string $column, ?int $semesterId, ?int $prodiId): int
    {
        return (int) $this->applyFilters($query, $semesterId, $prodiId)->sum($column);
    }

    private function summaryCards(Request $request): array
    {
        $semesterId = $request->integer('semester_id') ?: null;
        $prodiId = $this->visibleProdiId($request);

        return [
            'Prestasi' => $this->countFor(Prestasi::query(), $semesterId, $prodiId),
            'Event/Reimbursement' => $this->countFor(Event::query(), $semesterId, $prodiId),
            'Tracer Study Input' => $this->sumFor(TracerStudy::query(), 'jumlah_input', $semesterId, $prodiId),
            'Beasiswa' => $this->countFor(Beasiswa::query(), $semesterId, $prodiId),
            'Humas Marketing' => $this->unitCount('humas-marketing', $semesterId, $prodiId),
            'Science Center' => $this->unitCount('science-center', $semesterId, $prodiId),
            'Pengembangan Ormawa' => $this->unitCount('pengembangan-ormawa', $semesterId, $prodiId),
            'Alumni dan Pusat Karir' => $this->unitCount('alumni-pusat-karir', $semesterId, $prodiId),
        ];
    }

    private function achievementQuotas()
    {
        if (! Schema::hasTable('achievement_quotas')) {
            return collect();
        }

        return AchievementQuota::with(['semester', 'prodi'])->latest()->take(8)->get();
    }

    private function dashboardMenu(Request $request): array
    {
        $cards = $this->summaryCards($request);
        $user = $request->user();
        $items = [
            ['label' => 'Prestasi', 'desc' => 'Prestasi lomba mahasiswa', 'count' => $cards['Prestasi'] ?? 0, 'href' => route('prestasi.index'), 'icon' => 'prestasi', 'tone' => 'blue'],
            ['label' => 'Event', 'desc' => 'Kegiatan mahasiswa', 'count' => $cards['Event/Reimbursement'] ?? 0, 'href' => route('event.index'), 'icon' => 'event', 'tone' => 'teal'],
            ['label' => 'Reimburse', 'desc' => 'Pengajuan biaya kegiatan', 'count' => $cards['Event/Reimbursement'] ?? 0, 'href' => route('reimburse.table'), 'icon' => 'beasiswa', 'tone' => 'emerald'],
            ['label' => 'Beasiswa', 'desc' => 'Penerima dan pengajuan', 'count' => $cards['Beasiswa'] ?? 0, 'href' => route('beasiswa.index'), 'icon' => 'beasiswa', 'tone' => 'pink'],
            ['label' => 'Tracer', 'desc' => 'Progress input tracer', 'count' => $cards['Tracer Study Input'] ?? 0, 'href' => route('tracer.index'), 'icon' => 'tracer', 'tone' => 'violet'],
            ['label' => 'Unit', 'desc' => 'Humas, science, alumni', 'count' => ($cards['Humas Marketing'] ?? 0) + ($cards['Science Center'] ?? 0) + ($cards['Alumni dan Pusat Karir'] ?? 0), 'href' => route('unit-activities.index', 'humas-marketing'), 'icon' => 'prodi', 'tone' => 'cyan'],
            ['label' => 'Ormawa', 'desc' => 'Kegiatan dan proposal', 'count' => $cards['Pengembangan Ormawa'] ?? 0, 'href' => route('ormawa.index', 'data-ormawa'), 'icon' => 'user', 'tone' => 'amber'],
        ];

        if ($user->hasAnyRole(['super user', 'admin'])) {
            $items[] = ['label' => 'Master', 'desc' => 'Prodi, semester, kuota', 'count' => 5, 'href' => route('master-data.index', 'prodi'), 'icon' => 'semester', 'tone' => 'slate'];
        }

        if ($user->hasAnyRole(['super user', 'admin', 'kabag'])) {
            $items[] = ['label' => 'Publikasi', 'desc' => 'Berita dan karir', 'count' => 2, 'href' => route('publications.index', 'berita'), 'icon' => 'access', 'tone' => 'rose'];
        }

        if ($user->hasRole('super user')) {
            $items[] = ['label' => 'User', 'desc' => 'Akun dan role portal', 'count' => 0, 'href' => route('users.index'), 'icon' => 'user', 'tone' => 'blue'];
        }

        return $items;
    }

    private function applyFilters(Builder $query, ?int $semesterId, ?int $prodiId): Builder
    {
        return $query
            ->when($semesterId, fn ($q) => $q->where('semester_id', $semesterId))
            ->when($prodiId, fn ($q) => $q->where($q->getModel()->getTable().'.prodi_id', $prodiId));
    }

    private function visibleProdiId(Request $request): ?int
    {
        if ($request->user()->hasRole('kaprodi')) {
            return $request->user()->prodi_id;
        }

        return $request->integer('prodi_id') ?: null;
    }
}
