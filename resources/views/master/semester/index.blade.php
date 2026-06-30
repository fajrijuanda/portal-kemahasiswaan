<x-app-layout>
    @unless(isset($sectionShell))
        <x-slot name="header">
            <h1 class="ubp-title">Master Semester</h1>
            <p class="ubp-subtitle">Atur semester aktif dan periode akademik.</p>
        </x-slot>
    @endunless

    @isset($sectionShell)
        <x-ui.section-shell :eyebrow="$sectionShell['eyebrow']" :title="$sectionShell['title']" :subtitle="$sectionShell['subtitle']" :items="$sectionShell['items']" :stats="$sectionShell['stats']">
    @endisset

    <div class="ubp-stat-grid">
        <article class="ubp-stat-card tone-violet">
            <div><small>Total Semester</small><strong>{{ number_format($semesters->total()) }}</strong><em>Periode akademik</em></div>
            <span class="ubp-stat-icon"><x-ui.app-icon name="semester" /></span>
        </article>
        <article class="ubp-stat-card tone-emerald">
            <div><small>Semester Aktif</small><strong>{{ $semesters->firstWhere('is_active', true)?->periode ?? '-' }}</strong><em>{{ $semesters->firstWhere('is_active', true)?->tahun_akademik ?? 'Belum diset' }}</em></div>
            <span class="ubp-stat-icon"><x-ui.app-icon name="access" /></span>
        </article>
        <article class="ubp-stat-card tone-blue">
            <div><small>Ditampilkan</small><strong>{{ number_format($semesters->count()) }}</strong><em>Halaman ini</em></div>
            <span class="ubp-stat-icon"><x-ui.app-icon name="grid" /></span>
        </article>
    </div>

    <x-ui.table-shell class="ubp-table-shell-omnia" title="Daftar Semester" subtitle="Kelola periode akademik yang tersedia di seluruh modul.">
        <x-slot:toolbar>
            <button class="ubp-btn ubp-btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#semesterCreateModal">+ Tambah Semester</button>
        </x-slot:toolbar>

        <x-slot:controls>
            <form method="GET" class="ubp-record-table-controls">
                <label class="ubp-record-search">
                    <x-ui.app-icon name="grid" />
                    <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari semester, tahun, atau periode...">
                </label>
                <select name="periode" class="form-select ubp-control">
                    <option value="">Semua periode</option>
                    <option value="Ganjil" @selected(request('periode') === 'Ganjil')>Ganjil</option>
                    <option value="Genap" @selected(request('periode') === 'Genap')>Genap</option>
                </select>
                <select name="status" class="form-select ubp-control">
                    <option value="">Semua status</option>
                    <option value="active" @selected(request('status') === 'active')>Aktif</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Nonaktif</option>
                </select>
                <select name="limit" class="form-select ubp-control" onchange="this.form.submit()">
                    <option value="10" @selected(request('limit', 10) == 10)>10 / hal</option>
                    <option value="25" @selected(request('limit') == 25)>25 / hal</option>
                    <option value="50" @selected(request('limit') == 50)>50 / hal</option>
                    <option value="100" @selected(request('limit') == 100)>100 / hal</option>
                </select>
                <button class="ubp-table-action ubp-table-action-primary" type="submit">Filter</button>
                @if(request()->hasAny(['q', 'periode', 'status']))
                    <a class="ubp-table-action" href="{{ route('master-data.index', 'semester') }}">Reset</a>
                @endif
            </form>
        </x-slot:controls>

        <table class="table align-middle ubp-table ubp-data-table">
            <thead><tr><th>Nama</th><th>Tahun</th><th>Periode</th><th>Aktif</th><th class="text-end">Aksi</th></tr></thead>
            <tbody>
                @forelse($semesters as $semester)
                    <tr>
                        <td data-label="Nama"><span class="ubp-table-primary">{{ $semester->nama }}</span></td>
                        <td data-label="Tahun">{{ $semester->tahun_akademik }}</td>
                        <td data-label="Periode">{{ $semester->periode }}</td>
                        <td data-label="Aktif">
                            @if($semester->is_active)
                                <x-ui.status-badge status="Aktif" />
                            @else
                                <x-ui.status-badge status="Nonaktif" />
                            @endif
                        </td>
                        <td class="text-end" data-label="Aksi">
                            <div class="ubp-table-action-group justify-content-end">
                                <button class="ubp-table-action ubp-table-action-primary" type="button" data-bs-toggle="modal" data-bs-target="#semesterEditModal{{ $semester->id }}">Edit</button>
                                <button class="ubp-table-action ubp-table-action-danger" type="button" onclick="triggerDeleteModal(`{{ route('master.semester.destroy', $semester) }}`, `Hapus data semester akademik ini?`)">Hapus</button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <x-ui.table-empty-row colspan="5" title="Belum ada semester" message="Semester baru akan muncul setelah ditambahkan." />
                @endforelse
            </tbody>
        </table>
        <x-slot:pagination>
            <div class="ubp-pagination-summary">Menampilkan {{ $semesters->firstItem() ?? 0 }}-{{ $semesters->lastItem() ?? 0 }} dari {{ number_format($semesters->total()) }} data</div>
            <div class="ubp-pagination-controls">
                @if($semesters->onFirstPage())
                    <span class="ubp-page-button disabled">Prev</span>
                @else
                    <a class="ubp-page-button" href="{{ $semesters->previousPageUrl() }}">Prev</a>
                @endif
                <span class="ubp-page-current">{{ $semesters->currentPage() }}/{{ max($semesters->lastPage(), 1) }}</span>
                @if($semesters->hasMorePages())
                    <a class="ubp-page-button" href="{{ $semesters->nextPageUrl() }}">Next</a>
                @else
                    <span class="ubp-page-button disabled">Next</span>
                @endif
            </div>
        </x-slot:pagination>
    </x-ui.table-shell>

    {{-- Create Modal --}}
    <div class="modal fade ubp-record-modal" id="semesterCreateModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
            <form class="modal-content" method="POST" action="{{ route('master.semester.store') }}">
                @csrf
                <div class="modal-header">
                    <div><span class="ubp-auth-eyebrow">Tambah data</span><h5 class="modal-title">Tambah Semester</h5></div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="create-nama">Nama</label>
                            <input id="create-nama" name="nama" class="form-control ubp-control" placeholder="Ganjil 2026/2027" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="create-tahun">Tahun Akademik</label>
                            <input id="create-tahun" name="tahun_akademik" class="form-control ubp-control" placeholder="2026/2027" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="create-periode">Periode</label>
                            <select id="create-periode" name="periode" class="form-select ubp-control">
                                <option>Ganjil</option>
                                <option>Genap</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label d-block">Status</label>
                            <div class="form-check mt-2">
                                <input type="hidden" name="is_active" value="0">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="activeNew">
                                <label class="form-check-label" for="activeNew">Aktifkan semester ini</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="ubp-table-action" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="ubp-btn ubp-btn-primary">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Edit Modals --}}
    @foreach($semesters as $semester)
        <div class="modal fade ubp-record-modal" id="semesterEditModal{{ $semester->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
                <form class="modal-content" method="POST" action="{{ route('master.semester.update', $semester) }}">
                    @csrf @method('PUT')
                    <div class="modal-header">
                        <div><span class="ubp-auth-eyebrow">Edit data</span><h5 class="modal-title">Edit Semester</h5></div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="edit-{{ $semester->id }}-nama">Nama</label>
                                <input id="edit-{{ $semester->id }}-nama" name="nama" class="form-control ubp-control" value="{{ $semester->nama }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="edit-{{ $semester->id }}-tahun">Tahun Akademik</label>
                                <input id="edit-{{ $semester->id }}-tahun" name="tahun_akademik" class="form-control ubp-control" value="{{ $semester->tahun_akademik }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="edit-{{ $semester->id }}-periode">Periode</label>
                                <select id="edit-{{ $semester->id }}-periode" name="periode" class="form-select ubp-control">
                                    <option @selected($semester->periode === 'Ganjil')>Ganjil</option>
                                    <option @selected($semester->periode === 'Genap')>Genap</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label d-block">Status</label>
                                <div class="form-check mt-2">
                                    <input type="hidden" name="is_active" value="0">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="activeEdit{{ $semester->id }}" @checked($semester->is_active)>
                                    <label class="form-check-label" for="activeEdit{{ $semester->id }}">Aktifkan semester ini</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="ubp-table-action" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="ubp-btn ubp-btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    @endforeach

    @isset($sectionShell)
        </x-ui.section-shell>
    @endisset
</x-app-layout>
