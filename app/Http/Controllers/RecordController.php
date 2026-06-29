<?php

namespace App\Http\Controllers;

use App\Models\Beasiswa;
use App\Models\ClaimFasilitas;
use App\Models\ClaimTransport;
use App\Models\Event;
use App\Models\Prestasi;
use App\Models\Prodi;
use App\Models\Semester;
use App\Models\TracerStudy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RecordController extends Controller
{
    private array $modules = [
        'prestasi' => [
            'title' => 'Prestasi Mahasiswa',
            'model' => Prestasi::class,
            'fields' => [
                'nama_mahasiswa' => ['label' => 'Nama Mahasiswa', 'type' => 'text', 'required' => true],
                'nim' => ['label' => 'NIM', 'type' => 'text'],
                'nama_kegiatan' => ['label' => 'Nama Kegiatan', 'type' => 'text', 'required' => true],
                'tingkat' => ['label' => 'Tingkat', 'type' => 'select', 'options' => ['Nasional', 'Internasional'], 'required' => true],
                'peringkat' => ['label' => 'Peringkat', 'type' => 'text'],
                'penyelenggara' => ['label' => 'Penyelenggara', 'type' => 'text'],
                'tanggal' => ['label' => 'Tanggal', 'type' => 'date'],
                'foto_path' => ['label' => 'Foto Prestasi', 'type' => 'file'],
                'publikasi_url' => ['label' => 'Link Publikasi', 'type' => 'url'],
                'status' => ['label' => 'Status', 'type' => 'select', 'options' => ['Draft', 'Terverifikasi', 'Ditolak']],
                'catatan' => ['label' => 'Catatan', 'type' => 'textarea'],
            ],
        ],
        'event' => [
            'title' => 'Event & Reimbursement',
            'model' => Event::class,
            'fields' => [
                'nama_pengaju' => ['label' => 'Nama Pengaju/Mahasiswa', 'type' => 'text', 'required' => true],
                'nim' => ['label' => 'NIM', 'type' => 'text'],
                'jenis_reimbursement' => ['label' => 'Jenis Reimbursement', 'type' => 'select', 'options' => ['Akomodasi', 'Pendaftaran', 'Transport', 'Fasilitas', 'Lainnya'], 'required' => true],
                'nama_kegiatan' => ['label' => 'Nama Kegiatan', 'type' => 'text', 'required' => true],
                'tanggal' => ['label' => 'Tanggal', 'type' => 'date'],
                'nominal' => ['label' => 'Nominal', 'type' => 'number'],
                'bukti_path' => ['label' => 'Bukti/Foto', 'type' => 'file'],
                'status' => ['label' => 'Status', 'type' => 'select', 'options' => ['Diajukan', 'Diproses', 'Disetujui', 'Ditolak']],
                'catatan' => ['label' => 'Catatan', 'type' => 'textarea'],
            ],
        ],
        'claim-transport' => [
            'title' => 'Claim Transport',
            'model' => ClaimTransport::class,
            'fields' => [
                'nama_mahasiswa' => ['label' => 'Nama Mahasiswa', 'type' => 'text', 'required' => true],
                'nim' => ['label' => 'NIM', 'type' => 'text'],
                'kegiatan' => ['label' => 'Kegiatan', 'type' => 'text', 'required' => true],
                'tujuan' => ['label' => 'Tujuan', 'type' => 'text'],
                'tanggal' => ['label' => 'Tanggal', 'type' => 'date'],
                'nominal' => ['label' => 'Nominal', 'type' => 'number'],
                'status' => ['label' => 'Status', 'type' => 'select', 'options' => ['Diajukan', 'Diproses', 'Disetujui', 'Ditolak']],
                'catatan' => ['label' => 'Catatan', 'type' => 'textarea'],
            ],
        ],
        'claim-fasilitas' => [
            'title' => 'Claim Fasilitas',
            'model' => ClaimFasilitas::class,
            'fields' => [
                'nama_pengaju' => ['label' => 'Nama Pengaju', 'type' => 'text', 'required' => true],
                'fasilitas' => ['label' => 'Fasilitas', 'type' => 'text', 'required' => true],
                'keperluan' => ['label' => 'Keperluan', 'type' => 'text'],
                'tanggal' => ['label' => 'Tanggal', 'type' => 'date'],
                'jumlah' => ['label' => 'Jumlah', 'type' => 'number'],
                'status' => ['label' => 'Status', 'type' => 'select', 'options' => ['Diajukan', 'Diproses', 'Disetujui', 'Ditolak']],
                'catatan' => ['label' => 'Catatan', 'type' => 'textarea'],
            ],
        ],
        'tracer-study' => [
            'title' => 'Tracer Study',
            'model' => TracerStudy::class,
            'fields' => [
                'jumlah_mahasiswa' => ['label' => 'Jumlah Mahasiswa', 'type' => 'number', 'required' => true],
                'jumlah_input' => ['label' => 'Sudah Input', 'type' => 'number', 'required' => true],
                'periode_yudisium' => ['label' => 'Periode Yudisium', 'type' => 'text'],
                'status' => ['label' => 'Status', 'type' => 'select', 'options' => ['Berjalan', 'Lengkap', 'Perlu Follow Up']],
                'catatan' => ['label' => 'Catatan', 'type' => 'textarea'],
            ],
        ],
        'beasiswa' => [
            'title' => 'Beasiswa',
            'model' => Beasiswa::class,
            'fields' => [
                'nama_mahasiswa' => ['label' => 'Nama Mahasiswa', 'type' => 'text', 'required' => true],
                'nim' => ['label' => 'NIM', 'type' => 'text'],
                'jenis_beasiswa' => ['label' => 'Jenis Beasiswa', 'type' => 'text', 'required' => true],
                'sumber' => ['label' => 'Sumber', 'type' => 'text'],
                'nominal' => ['label' => 'Nominal', 'type' => 'number'],
                'status' => ['label' => 'Status', 'type' => 'select', 'options' => ['Aktif', 'Selesai', 'Ditolak']],
                'catatan' => ['label' => 'Catatan', 'type' => 'textarea'],
            ],
        ],
    ];

    public function index(Request $request, string $module)
    {
        $config = $this->config($module);
        $model = $config['model'];
        $query = $model::with(['semester', 'prodi'])->latest();
        $this->applyScopeAndFilters($query, $request, $config);

        return view('records.index', [
            'module' => $module,
            'config' => $config,
            'records' => $query->paginate(request('limit', 10))->withQueryString(),
            'semesters' => Semester::orderByDesc('id')->get(),
            'prodis' => Prodi::orderBy('nama')->get(),
        ]);
    }

    public function create(string $module)
    {
        return view('records.form', $this->formData($module, null));
    }

    public function store(Request $request, string $module)
    {
        $config = $this->config($module);
        $data = $this->validated($request, $config);
        $this->handleUploads($request, $config, $data);
        $data['created_by'] = $request->user()->id;

        if ($request->user()->hasRole('kaprodi')) {
            $data['prodi_id'] = $request->user()->prodi_id;
        }

        $config['model']::create($data);

        return redirect()->route('records.index', $module)->with('status', $config['title'].' berhasil ditambahkan.');
    }

    public function edit(string $module, int $id)
    {
        $config = $this->config($module);
        $record = $this->scopedFind($config['model'], $id);

        return view('records.form', $this->formData($module, $record));
    }

    public function update(Request $request, string $module, int $id)
    {
        $config = $this->config($module);
        $record = $this->scopedFind($config['model'], $id);
        $data = $this->validated($request, $config);
        $this->handleUploads($request, $config, $data, $record);

        if ($request->user()->hasRole('kaprodi')) {
            $data['prodi_id'] = $request->user()->prodi_id;
        }

        $record->update($data);

        return redirect()->route('records.index', $module)->with('status', $config['title'].' berhasil diperbarui.');
    }

    public function destroy(string $module, int $id)
    {
        $config = $this->config($module);
        $record = $this->scopedFind($config['model'], $id);
        $this->deleteUploads($config, $record);
        $record->delete();

        return back()->with('status', $config['title'].' berhasil dihapus.');
    }

    private function formData(string $module, \Illuminate\Database\Eloquent\Model $record = null): array
    {
        return [
            'module' => $module,
            'config' => $this->config($module),
            'record' => $record,
            'semesters' => Semester::orderByDesc('id')->get(),
            'prodis' => Prodi::orderBy('nama')->get(),
        ];
    }

    private function validated(Request $request, array $config): array
    {
        $rules = [
            'semester_id' => ['required', 'exists:semesters,id'],
            'prodi_id' => [$request->user()->hasRole('kaprodi') ? 'nullable' : 'required', 'nullable', 'exists:prodis,id'],
        ];

        foreach ($config['fields'] as $name => $field) {
            $rules[$name] = [($field['required'] ?? false) ? 'required' : 'nullable'];
            $rules[$name] = array_merge($rules[$name], match ($field['type']) {
                'number' => ['numeric'],
                'date' => ['date'],
                'file' => ['image', 'max:2048'],
                'url' => ['url'],
                default => ['string'],
            });
        }

        return $request->validate($rules);
    }

    private function applyScopeAndFilters(\Illuminate\Database\Eloquent\Builder $query, Request $request, ?array $config = null): void
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
        if ($keyword !== '' && $config) {
            $searchableFields = collect($config['fields'])
                ->reject(fn ($field) => ($field['type'] ?? null) === 'file')
                ->keys()
                ->all();

            $query->where(function ($inner) use ($keyword, $searchableFields) {
                foreach ($searchableFields as $field) {
                    $inner->orWhere($field, 'like', "%{$keyword}%");
                }
                $inner->orWhereHas('prodi', fn ($prodi) => $prodi->where('nama', 'like', "%{$keyword}%"));
                $inner->orWhereHas('semester', fn ($semester) => $semester->where('nama', 'like', "%{$keyword}%"));
            });
        }
    }

    private function scopedFind(string $model, int $id)
    {
        $query = $model::query();
        if (request()->user()->hasRole('kaprodi')) {
            $query->where('prodi_id', request()->user()->prodi_id);
        }

        return $query->findOrFail($id);
    }

    private function config(string $module): array
    {
        abort_unless(isset($this->modules[$module]), 404);

        return $this->modules[$module];
    }

    private function handleUploads(Request $request, array $config, array &$data, $record = null): void
    {
        foreach ($config['fields'] as $name => $field) {
            if (($field['type'] ?? null) !== 'file') {
                continue;
            }

            unset($data[$name]);

            if (! $request->hasFile($name)) {
                continue;
            }

            if ($record?->{$name}) {
                Storage::disk('public')->delete($record->{$name});
            }

            $data[$name] = $request->file($name)->store($name, 'public');
        }
    }

    private function deleteUploads(array $config, \Illuminate\Database\Eloquent\Model $record): void
    {
        foreach ($config['fields'] as $name => $field) {
            if (($field['type'] ?? null) === 'file' && $record->{$name}) {
                Storage::disk('public')->delete($record->{$name});
            }
        }
    }
}
