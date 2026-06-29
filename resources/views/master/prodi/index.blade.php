<x-app-layout>
    <x-slot name="header">
        <h1 class="ubp-title">Master Prodi</h1>
        <p class="ubp-subtitle">Kelola daftar program studi dan fakultas.</p>
    </x-slot>

    <div class="ubp-stat-grid">
        <article class="ubp-stat-card tone-blue">
            <div><small>Total Prodi</small><strong>{{ number_format($prodis->total()) }}</strong><em>Program studi</em></div>
            <span class="ubp-stat-icon"><x-ui.app-icon name="prodi" /></span>
        </article>
        <article class="ubp-stat-card tone-violet">
            <div><small>Ditampilkan</small><strong>{{ number_format($prodis->count()) }}</strong><em>Halaman ini</em></div>
            <span class="ubp-stat-icon"><x-ui.app-icon name="grid" /></span>
        </article>
        <article class="ubp-stat-card tone-emerald">
            <div><small>Master Data</small><strong>Aktif</strong><em>Siap dipakai filter</em></div>
            <span class="ubp-stat-icon"><x-ui.app-icon name="access" /></span>
        </article>
    </div>

    <x-ui.table-shell class="ubp-table-shell-omnia" title="Daftar Prodi" subtitle="Data master program studi yang digunakan untuk filter dan scope user.">
        <x-slot:toolbar>
            <button class="ubp-btn ubp-btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#prodiCreateModal">+ Tambah Prodi</button>
        </x-slot:toolbar>

        <x-slot:controls>
            <form method="GET" class="ubp-record-table-controls">
                <div class="ubp-record-search d-none d-md-flex" style="opacity: 0; pointer-events: none;"><input></div>
                <select name="limit" class="form-select ubp-control" onchange="this.form.submit()">
                    <option value="10" @selected(request('limit', 10) == 10)>10 / hal</option>
                    <option value="25" @selected(request('limit') == 25)>25 / hal</option>
                    <option value="50" @selected(request('limit') == 50)>50 / hal</option>
                    <option value="100" @selected(request('limit') == 100)>100 / hal</option>
                </select>
            </form>
        </x-slot:controls>

        <table class="table align-middle ubp-table ubp-data-table">
            <thead><tr><th>Nama</th><th>Kode</th><th>Fakultas</th><th class="text-end">Aksi</th></tr></thead>
            <tbody>
                @forelse($prodis as $prodi)
                    <tr>
                        <td data-label="Nama"><span class="ubp-table-primary">{{ $prodi->nama }}</span></td>
                        <td data-label="Kode">{{ $prodi->kode ?: '-' }}</td>
                        <td data-label="Fakultas">{{ $prodi->fakultas ?: '-' }}</td>
                        <td class="text-end" data-label="Aksi">
                            <div class="ubp-table-action-group justify-content-end">
                                <button class="ubp-table-action ubp-table-action-primary" type="button" data-bs-toggle="modal" data-bs-target="#prodiEditModal{{ $prodi->id }}">Edit</button>
                                <button class="ubp-table-action ubp-table-action-danger" type="button" onclick="triggerDeleteModal(`{{ route('master.prodi.destroy', $prodi) }}`, `Hapus program studi ini?`)">Hapus</button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <x-ui.table-empty-row colspan="4" title="Belum ada prodi" message="Program studi baru akan muncul setelah ditambahkan." />
                @endforelse
            </tbody>
        </table>
        <x-slot:pagination>
            <div class="ubp-pagination-summary">Menampilkan {{ $prodis->firstItem() ?? 0 }}-{{ $prodis->lastItem() ?? 0 }} dari {{ number_format($prodis->total()) }} data</div>
            <div class="ubp-pagination-controls">
                @if($prodis->onFirstPage())
                    <span class="ubp-page-button disabled">Prev</span>
                @else
                    <a class="ubp-page-button" href="{{ $prodis->previousPageUrl() }}">Prev</a>
                @endif
                <span class="ubp-page-current">{{ $prodis->currentPage() }}/{{ max($prodis->lastPage(), 1) }}</span>
                @if($prodis->hasMorePages())
                    <a class="ubp-page-button" href="{{ $prodis->nextPageUrl() }}">Next</a>
                @else
                    <span class="ubp-page-button disabled">Next</span>
                @endif
            </div>
        </x-slot:pagination>
    </x-ui.table-shell>

    {{-- Create Modal --}}
    <div class="modal fade ubp-record-modal" id="prodiCreateModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
            <form class="modal-content" method="POST" action="{{ route('master.prodi.store') }}">
                @csrf
                <div class="modal-header">
                    <div><span class="ubp-auth-eyebrow">Tambah data</span><h5 class="modal-title">Tambah Prodi</h5></div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="create-nama">Nama Prodi</label>
                            <input id="create-nama" name="nama" class="form-control ubp-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="create-kode">Kode</label>
                            <input id="create-kode" name="kode" class="form-control ubp-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="create-fakultas">Fakultas</label>
                            <input id="create-fakultas" name="fakultas" class="form-control ubp-control">
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
    @foreach($prodis as $prodi)
        <div class="modal fade ubp-record-modal" id="prodiEditModal{{ $prodi->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
                <form class="modal-content" method="POST" action="{{ route('master.prodi.update', $prodi) }}">
                    @csrf @method('PUT')
                    <div class="modal-header">
                        <div><span class="ubp-auth-eyebrow">Edit data</span><h5 class="modal-title">Edit Prodi</h5></div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="edit-{{ $prodi->id }}-nama">Nama Prodi</label>
                                <input id="edit-{{ $prodi->id }}-nama" name="nama" class="form-control ubp-control" value="{{ $prodi->nama }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="edit-{{ $prodi->id }}-kode">Kode</label>
                                <input id="edit-{{ $prodi->id }}-kode" name="kode" class="form-control ubp-control" value="{{ $prodi->kode }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="edit-{{ $prodi->id }}-fakultas">Fakultas</label>
                                <input id="edit-{{ $prodi->id }}-fakultas" name="fakultas" class="form-control ubp-control" value="{{ $prodi->fakultas }}">
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
</x-app-layout>