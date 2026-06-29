<?php

namespace App\Http\Controllers;

use App\Models\AchievementQuota;
use App\Models\Beasiswa;
use App\Models\ClaimFasilitas;
use App\Models\ClaimTransport;
use App\Models\Competition;
use App\Models\Event;
use App\Models\Ormawa;
use App\Models\Prestasi;
use App\Models\Prodi;
use App\Models\ScholarshipType;
use App\Models\Semester;
use App\Models\TracerStudy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class RecordController extends Controller
{
    private array $modules = [
        'prestasi' => [
            'title' => 'Prestasi Mahasiswa',
            'model' => Prestasi::class,
            'with' => ['semester', 'prodi', 'competition', 'creator'],
            'fields' => [
                'nama_mahasiswa' => ['label' => 'Nama Mahasiswa', 'type' => 'text', 'required' => true],
                'nim' => ['label' => 'NIM', 'type' => 'text'],
                'competition_id' => ['label' => 'Nama Lomba', 'type' => 'select', 'options' => [], 'relation' => 'competition.nama', 'rules' => ['nullable', 'exists:competitions,id']],
                'nama_kegiatan' => ['label' => 'Nama Kegiatan', 'type' => 'text', 'required' => true],
                'kategori_event' => ['label' => 'Kategori Event', 'type' => 'select', 'options' => ['Kelompok', 'Perorangan']],
                'scope' => ['label' => 'Scope', 'type' => 'select', 'options' => ['Lokal', 'Regional', 'Nasional', 'Internasional']],
                'juara' => ['label' => 'Juara', 'type' => 'select', 'options' => ['1', '2', '3', 'Favorit', 'Finalis', 'Harapan']],
                'tingkat' => ['label' => 'Tingkat Lama', 'type' => 'select', 'options' => ['Lokal', 'Regional', 'Nasional', 'Internasional']],
                'peringkat' => ['label' => 'Peringkat Lama', 'type' => 'text'],
                'penyelenggara' => ['label' => 'Penyelenggara', 'type' => 'text'],
                'tanggal' => ['label' => 'Tanggal', 'type' => 'date'],
                'foto_path' => ['label' => 'Foto Prestasi', 'type' => 'file'],
                'publikasi_url' => ['label' => 'Link Publikasi', 'type' => 'url'],
                'status' => ['label' => 'Status', 'type' => 'select', 'options' => ['Diajukan', 'Draft', 'Terverifikasi', 'Ditolak']],
                'catatan' => ['label' => 'Catatan', 'type' => 'textarea'],
            ],
        ],
        'event' => [
            'title' => 'Event Kegiatan',
            'model' => Event::class,
            'with' => ['semester', 'prodi', 'ormawa', 'creator'],
            'fields' => [
                'nama_pengaju' => ['label' => 'Nama Pengaju/Mahasiswa', 'type' => 'text', 'required' => true],
                'nim' => ['label' => 'NIM', 'type' => 'text'],
                'ormawa_id' => ['label' => 'Ormawa', 'type' => 'select', 'options' => [], 'relation' => 'ormawa.nama', 'rules' => ['nullable', 'exists:ormawas,id']],
                'jenis_reimbursement' => ['label' => 'Jenis Reimbursement', 'type' => 'select', 'options' => ['Akomodasi', 'Pendaftaran', 'Transport', 'Fasilitas', 'Lainnya'], 'required' => true],
                'nama_kegiatan' => ['label' => 'Nama Kegiatan', 'type' => 'text', 'required' => true],
                'tanggal' => ['label' => 'Tanggal', 'type' => 'date'],
                'nominal' => ['label' => 'Nominal', 'type' => 'number'],
                'bukti_path' => ['label' => 'Bukti/Foto', 'type' => 'file'],
                'foto_path' => ['label' => 'Foto Kegiatan', 'type' => 'file'],
                'surat_tugas_path' => ['label' => 'Surat Tugas', 'type' => 'file'],
                'sertifikat_path' => ['label' => 'Sertifikat', 'type' => 'file'],
                'link_penyelenggara' => ['label' => 'Link Penyelenggara', 'type' => 'url'],
                'status' => ['label' => 'Status', 'type' => 'select', 'options' => ['Diajukan', 'Diproses', 'Disetujui', 'Ditolak']],
                'catatan' => ['label' => 'Catatan', 'type' => 'textarea'],
            ],
        ],
        'reimburse' => [
            'title' => 'Reimbursement',
            'model' => Event::class,
            'with' => ['semester', 'prodi', 'ormawa', 'creator'],
            'fields' => [
                'nama_pengaju' => ['label' => 'Nama Pengaju/Mahasiswa', 'type' => 'text', 'required' => true],
                'nim' => ['label' => 'NIM', 'type' => 'text'],
                'ormawa_id' => ['label' => 'Ormawa', 'type' => 'select', 'options' => [], 'relation' => 'ormawa.nama', 'rules' => ['nullable', 'exists:ormawas,id']],
                'jenis_reimbursement' => ['label' => 'Jenis Reimbursement', 'type' => 'select', 'options' => ['Akomodasi', 'Pendaftaran', 'Transport', 'Fasilitas', 'Lainnya'], 'required' => true],
                'nama_kegiatan' => ['label' => 'Nama Kegiatan', 'type' => 'text', 'required' => true],
                'tanggal' => ['label' => 'Tanggal', 'type' => 'date'],
                'nominal' => ['label' => 'Nominal', 'type' => 'number'],
                'bukti_path' => ['label' => 'Bukti/Foto', 'type' => 'file'],
                'foto_path' => ['label' => 'Foto Kegiatan', 'type' => 'file'],
                'surat_tugas_path' => ['label' => 'Surat Tugas', 'type' => 'file'],
                'sertifikat_path' => ['label' => 'Sertifikat', 'type' => 'file'],
                'link_penyelenggara' => ['label' => 'Link Penyelenggara', 'type' => 'url'],
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
            'with' => ['semester', 'prodi', 'scholarshipType', 'creator'],
            'fields' => [
                'nama_mahasiswa' => ['label' => 'Nama Mahasiswa', 'type' => 'text', 'required' => true],
                'scholarship_type_id' => ['label' => 'Jenis Beasiswa', 'type' => 'select', 'options' => [], 'relation' => 'scholarshipType.nama', 'rules' => ['required', 'exists:scholarship_types,id']],
                'nominal' => ['label' => 'Nominal', 'type' => 'number'],
                'status' => ['label' => 'Status', 'type' => 'select', 'options' => ['Diajukan', 'Aktif', 'Selesai', 'Ditolak']],
                'nim' => ['label' => 'NIM', 'type' => 'text'],
                'jenis_beasiswa' => ['label' => 'Jenis Beasiswa Lainnya', 'type' => 'text'],
                'sumber' => ['label' => 'Sumber', 'type' => 'text'],
                'catatan' => ['label' => 'Catatan', 'type' => 'textarea'],
            ],
        ],
    ];

    public function overview(Request $request, string $group)
    {
        $overview = $this->groupOverview($group);
        abort_unless($overview, 404);

        return view('records.overview', [
            'overview' => $overview,
            'stats' => collect($overview['items'])->map(fn ($item) => [
                'label' => $item['label'],
                'value' => number_format($this->moduleCount($item['module'])),
                'caption' => 'data',
                'icon' => $item['icon'],
                'tone' => $item['tone'] ?? 'blue',
            ])->values()->all(),
        ]);
    }

    public function index(Request $request, string $module)
    {
        $config = $this->config($module);
        $model = $config['model'];
        $query = $model::with($config['with'] ?? ['semester', 'prodi'])->latest();
        $this->applyScopeAndFilters($query, $request, $config);

        return view('records.index', [
            'module' => $module,
            'config' => $config,
            'canonicalRoute' => $this->canonicalRoute($module),
            'records' => $query->paginate(request('limit', 10))->withQueryString(),
            'semesters' => Semester::orderByDesc('id')->get(),
            'prodis' => Prodi::orderBy('nama')->get(),
            'achievementQuotas' => $module === 'prestasi' && Schema::hasTable('achievement_quotas')
                ? AchievementQuota::with(['semester', 'prodi'])->latest()->take(8)->get()
                : collect(),
            'sectionShell' => [
                'eyebrow' => $this->dataSectionMeta($module)['eyebrow'],
                'title' => $config['title'],
                'subtitle' => $this->dataSectionMeta($module)['subtitle'],
                'items' => $this->dataSectionItems($module),
                'stats' => [
                    ['label' => 'Total Data', 'value' => number_format($query->toBase()->getCountForPagination()), 'caption' => $config['title'], 'icon' => 'grid', 'tone' => 'blue'],
                    ['label' => 'Lingkup', 'value' => count($this->dataSectionItems($module)), 'caption' => 'tabel terkait', 'icon' => 'semester', 'tone' => 'emerald'],
                ],
            ],
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

        $record = $config['model']::create($data);
        $this->syncComputedFields($module, $record);

        return redirect()->route($this->canonicalRoute($module))->with('status', $config['title'].' berhasil ditambahkan.');
    }

    public function edit(string $module, int $id)
    {
        $config = $this->config($module);
        $record = $this->scopedFind($config['model'], $id);

        return view('records.form', $this->formData($module, $record));
    }

    public function update(Request $request, string|int $module, string|int $id)
    {
        [$module, $id] = $this->normalizeModuleAndId($module, $id);
        $config = $this->config($module);
        $record = $this->scopedFind($config['model'], $id);
        $oldSemesterId = $record->semester_id ?? null;
        $oldProdiId = $record->prodi_id ?? null;
        $data = $this->validated($request, $config);
        $this->handleUploads($request, $config, $data, $record);

        if ($request->user()->hasRole('kaprodi')) {
            $data['prodi_id'] = $request->user()->prodi_id;
        }

        $record->update($data);
        $this->syncComputedFields($module, $record);
        if ($module === 'prestasi' && $oldSemesterId && $oldProdiId && ($oldSemesterId !== $record->semester_id || $oldProdiId !== $record->prodi_id)) {
            $this->syncPrestasiQuota($oldSemesterId, $oldProdiId);
        }

        return redirect()->route($this->canonicalRoute($module))->with('status', $config['title'].' berhasil diperbarui.');
    }

    public function destroy(string|int $module, string|int $id)
    {
        [$module, $id] = $this->normalizeModuleAndId($module, $id);
        $config = $this->config($module);
        $record = $this->scopedFind($config['model'], $id);
        $this->deleteUploads($config, $record);
        $semesterId = $record->semester_id;
        $prodiId = $record->prodi_id;
        $record->delete();
        if ($module === 'prestasi') {
            $this->syncPrestasiQuota($semesterId, $prodiId);
        }

        return back()->with('status', $config['title'].' berhasil dihapus.');
    }

    private function formData(string $module, ?\Illuminate\Database\Eloquent\Model $record = null): array
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
            if (isset($field['rules'])) {
                $rules[$name] = $field['rules'];
                continue;
            }

            $rules[$name] = [($field['required'] ?? false) ? 'required' : 'nullable'];
            $rules[$name] = array_merge($rules[$name], match ($field['type']) {
                'number' => ['numeric'],
                'date' => ['date'],
                'file' => ['file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
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

        $config = $this->modules[$module];

        if ($module === 'prestasi') {
            if (Schema::hasTable('competitions')) {
                $config['fields']['competition_id']['options'] = Competition::where('is_active', true)->orderBy('nama')->pluck('nama', 'id')->all();
            } else {
                $config['with'] = array_values(array_diff($config['with'] ?? [], ['competition']));
                unset($config['fields']['competition_id']);
            }
        }

        if ($module === 'beasiswa') {
            if (Schema::hasTable('scholarship_types')) {
                $config['fields']['scholarship_type_id']['options'] = ScholarshipType::where('is_active', true)->orderBy('nama')->pluck('nama', 'id')->all();
            } else {
                $config['with'] = array_values(array_diff($config['with'] ?? [], ['scholarshipType']));
                unset($config['fields']['scholarship_type_id']);
            }
        }

        if (in_array($module, ['event', 'reimburse'], true)) {
            if (Schema::hasTable('ormawas')) {
                $config['fields']['ormawa_id']['options'] = Ormawa::where('status', 'Aktif')->orderBy('nama')->pluck('nama', 'id')->all();
            } else {
                $config['with'] = array_values(array_diff($config['with'] ?? [], ['ormawa']));
                unset($config['fields']['ormawa_id']);
            }
        }

        $table = (new $config['model'])->getTable();
        if (Schema::hasTable($table)) {
            $config['fields'] = collect($config['fields'])
                ->filter(fn ($field, $name) => Schema::hasColumn($table, $name))
                ->all();
        }

        return $config;
    }

    private function normalizeModuleAndId(string|int $module, string|int $id): array
    {
        if (is_numeric($module) && ! is_numeric($id)) {
            return [(string) $id, (int) $module];
        }

        return [(string) $module, (int) $id];
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

    private function syncComputedFields(string $module, $record): void
    {
        if ($module === 'beasiswa' && $record->scholarshipType) {
            $record->forceFill(['jenis_beasiswa' => $record->scholarshipType->nama])->save();
        }

        if ($module === 'prestasi') {
            $record->forceFill([
                'tingkat' => $record->scope ?: $record->tingkat,
                'peringkat' => $record->juara ? 'Juara '.$record->juara : $record->peringkat,
            ])->save();
            $this->syncPrestasiQuota($record->semester_id, $record->prodi_id);
        }
    }

    private function syncPrestasiQuota(int $semesterId, int $prodiId): void
    {
        $quota = AchievementQuota::firstOrCreate([
            'semester_id' => $semesterId,
            'prodi_id' => $prodiId,
        ]);

        $quota->update([
            'terpakai' => Prestasi::where('semester_id', $semesterId)
                ->where('prodi_id', $prodiId)
                ->where('status', 'Terverifikasi')
                ->count(),
        ]);
    }

    private function deleteUploads(array $config, \Illuminate\Database\Eloquent\Model $record): void
    {
        foreach ($config['fields'] as $name => $field) {
            if (($field['type'] ?? null) === 'file' && $record->{$name}) {
                Storage::disk('public')->delete($record->{$name});
            }
        }
    }

    private function dataSectionItems(string $activeModule): array
    {
        $groups = [
            'prestasi' => [
                'prestasi' => ['label' => 'Prestasi', 'icon' => 'prestasi'],
            ],
            'event' => [
                'event' => ['label' => 'Event', 'icon' => 'event'],
                'reimburse' => ['label' => 'Reimburse', 'icon' => 'beasiswa'],
            ],
            'reimburse' => [
                'event' => ['label' => 'Event', 'icon' => 'event'],
                'reimburse' => ['label' => 'Reimburse', 'icon' => 'beasiswa'],
            ],
            'beasiswa' => [
                'beasiswa' => ['label' => 'Beasiswa', 'icon' => 'beasiswa'],
            ],
            'tracer-study' => [
                'tracer-study' => ['label' => 'Tracer Study', 'icon' => 'tracer'],
            ],
        ];

        return collect($groups[$activeModule] ?? [])->map(function ($item, $module) use ($activeModule) {
            $config = $this->config($module);
            $model = $config['model'];

            return [
                'label' => $item['label'],
                'icon' => $item['icon'],
                'href' => route($this->canonicalRoute($module)),
                'active' => $module === $activeModule,
                'count' => $model::count(),
            ];
        })->values()->all();
    }

    private function dataSectionMeta(string $module): array
    {
        return match ($module) {
            'prestasi' => [
                'eyebrow' => 'Prestasi',
                'subtitle' => 'Kelola prestasi lomba mahasiswa dan data pendukungnya.',
            ],
            'event', 'reimburse' => [
                'eyebrow' => 'Event & Reimbursement',
                'subtitle' => 'Kelola event kegiatan dan pengajuan reimbursement dalam satu lingkup.',
            ],
            'beasiswa' => [
                'eyebrow' => 'Beasiswa',
                'subtitle' => 'Kelola penerima, jenis, nominal, dan status beasiswa mahasiswa.',
            ],
            'tracer-study' => [
                'eyebrow' => 'Tracer Study',
                'subtitle' => 'Pantau input tracer study dan progres tiap program studi.',
            ],
            default => [
                'eyebrow' => 'Data Layanan',
                'subtitle' => 'Kelola modul layanan kemahasiswaan dari satu halaman dengan navigasi ringkas.',
            ],
        };
    }

    private function groupOverview(string $group): ?array
    {
        return match ($group) {
            'prestasi' => [
                'eyebrow' => 'Prestasi',
                'title' => 'Prestasi Mahasiswa',
                'subtitle' => 'Pilih tabel prestasi yang ingin dibuka. Halaman ini menjadi overview sebelum masuk ke data detail.',
                'items' => [
                    ['label' => 'Prestasi Mahasiswa', 'module' => 'prestasi', 'icon' => 'prestasi', 'tone' => 'blue', 'href' => route('prestasi.table'), 'description' => 'Data lomba, kategori, scope, juara, dan verifikasi prestasi.'],
                ],
            ],
            'event' => [
                'eyebrow' => 'Event & Reimbursement',
                'title' => 'Event dan Reimbursement',
                'subtitle' => 'Pilih tabel Event atau Reimburse terlebih dulu. Keduanya berada dalam satu lingkup layanan kegiatan mahasiswa.',
                'items' => [
                    ['label' => 'Event', 'module' => 'event', 'icon' => 'event', 'tone' => 'teal', 'href' => route('event.table'), 'description' => 'Daftar kegiatan/event mahasiswa dan status pelaksanaannya.'],
                    ['label' => 'Reimburse', 'module' => 'reimburse', 'icon' => 'beasiswa', 'tone' => 'emerald', 'href' => route('reimburse.table'), 'description' => 'Pengajuan akomodasi, pendaftaran, transport, fasilitas, dan bukti pendukung.'],
                ],
            ],
            'beasiswa' => [
                'eyebrow' => 'Beasiswa',
                'title' => 'Beasiswa',
                'subtitle' => 'Overview data beasiswa sebelum membuka tabel penerima dan pengajuan.',
                'items' => [
                    ['label' => 'Data Beasiswa', 'module' => 'beasiswa', 'icon' => 'beasiswa', 'tone' => 'pink', 'href' => route('beasiswa.table'), 'description' => 'Nama mahasiswa, jenis beasiswa, nominal, prodi, dan status.'],
                ],
            ],
            'tracer' => [
                'eyebrow' => 'Tracer Study',
                'title' => 'Tracer Study',
                'subtitle' => 'Overview input tracer study sebelum membuka tabel progres per prodi.',
                'items' => [
                    ['label' => 'Data Tracer', 'module' => 'tracer-study', 'icon' => 'tracer', 'tone' => 'violet', 'href' => route('tracer.table'), 'description' => 'Jumlah mahasiswa, jumlah input, periode yudisium, dan status follow up.'],
                ],
            ],
            default => null,
        };
    }

    private function canonicalRoute(string $module): string
    {
        return match ($module) {
            'prestasi' => 'prestasi.table',
            'event' => 'event.table',
            'reimburse' => 'reimburse.table',
            'beasiswa' => 'beasiswa.table',
            'tracer-study' => 'tracer.table',
            default => 'prestasi.index',
        };
    }

    private function moduleCount(string $module): int
    {
        $config = $this->config($module);

        return $config['model']::count();
    }
}
