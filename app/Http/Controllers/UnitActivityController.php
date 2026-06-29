<?php

namespace App\Http\Controllers;

use App\Models\Ormawa;
use App\Models\Prodi;
use App\Models\Semester;
use App\Models\UnitActivity;
use Illuminate\Http\Request;

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

    public function index(Request $request, string $unit)
    {
        $config = $this->config($unit);
        $query = UnitActivity::with(['semester', 'prodi', 'ormawa', 'creator'])
            ->where('unit', $unit)
            ->latest();

        $this->applyScopeAndFilters($query, $request);

        $baseQuery = UnitActivity::query()->where('unit', $unit);
        if ($request->user()->hasRole('kaprodi')) {
            $baseQuery->where('prodi_id', $request->user()->prodi_id);
        }

        return view('unit-activities.index', [
            'unit' => $unit,
            'config' => $config,
            'records' => $query->paginate(request('limit', 10))->withQueryString(),
            'totalRecords' => (clone $baseQuery)->count(),
            'completedRecords' => (clone $baseQuery)->where('status', 'Selesai')->count(),
            'semesters' => Semester::orderByDesc('id')->get(),
            'prodis' => Prodi::orderBy('nama')->get(),
            'ormawas' => Ormawa::where('status', 'Aktif')->orderBy('nama')->get(),
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

        return redirect()->route('unit-activities.index', $unit)->with('status', $config['title'].' berhasil ditambahkan.');
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

        return redirect()->route('unit-activities.index', $unit)->with('status', $config['title'].' berhasil diperbarui.');
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
            'ormawa_id' => ['nullable', 'exists:ormawas,id'],
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
}
