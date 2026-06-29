<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="ubp-title">{{ $config['title'] }}</h1>
            <p class="ubp-subtitle">{{ $config['subtitle'] }}</p>
        </div>
    </x-slot>

    @php
        $columnCount = 7;
    @endphp

    <div class="ubp-stat-grid">
        <article class="ubp-stat-card tone-{{ $config['tone'] }}">
            <div><small>Total Data</small><strong>{{ number_format($totalRecords) }}</strong><em>{{ $config['title'] }}</em></div>
            <span class="ubp-stat-icon"><x-ui.app-icon :name="$config['icon']" /></span>
        </article>
        <article class="ubp-stat-card tone-blue">
            <div><small>Ditampilkan</small><strong>{{ number_format($records->count()) }}</strong><em>Halaman ini</em></div>
            <span class="ubp-stat-icon"><x-ui.app-icon name="grid" /></span>
        </article>
        <article class="ubp-stat-card tone-emerald">
            <div><small>Selesai</small><strong>{{ number_format($completedRecords) }}</strong><em>Aktivitas tuntas</em></div>
            <span class="ubp-stat-icon"><x-ui.app-icon name="event" /></span>
        </article>
        <article class="ubp-stat-card tone-slate">
            <div><small>Scope Prodi</small><strong>{{ auth()->user()->hasRole('kaprodi') || request('prodi_id') ? '1' : 'All' }}</strong><em>{{ auth()->user()->hasRole('kaprodi') ? 'Kaprodi' : 'Portal' }}</em></div>
            <span class="ubp-stat-icon"><x-ui.app-icon name="prodi" /></span>
        </article>
    </div>

    <x-ui.table-shell class="ubp-table-shell-omnia" :title="$config['title']" subtitle="Kelola data unit untuk kebutuhan rekap dan grafik dashboard.">
        <x-slot:toolbar>
            <button class="ubp-btn ubp-btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#unitCreateModal">+ Tambah Data</button>
        </x-slot:toolbar>

        <x-slot:controls>
            <form class="ubp-record-table-controls" method="GET">
                <div class="ubp-record-search">
                    <span><x-ui.app-icon name="grid" /></span>
                    <input name="q" value="{{ request('q') }}" placeholder="Cari data..." autocomplete="off">
                </div>
                <select name="semester_id" class="form-select ubp-control">
                    <option value="">Semua semester</option>
                    @foreach($semesters as $semester)
                        <option value="{{ $semester->id }}" @selected(request('semester_id') == $semester->id)>{{ $semester->nama }}</option>
                    @endforeach
                </select>
                @unless(auth()->user()->hasRole('kaprodi'))
                    <select name="prodi_id" class="form-select ubp-control">
                        <option value="">Semua prodi</option>
                        @foreach($prodis as $prodi)
                            <option value="{{ $prodi->id }}" @selected(request('prodi_id') == $prodi->id)>{{ $prodi->nama }}</option>
                        @endforeach
                    </select>
                @endunless
                <select name="limit" class="form-select ubp-control" onchange="this.form.submit()">
                    <option value="10" @selected(request('limit', 10) == 10)>10 / hal</option>
                    <option value="25" @selected(request('limit') == 25)>25 / hal</option>
                    <option value="50" @selected(request('limit') == 50)>50 / hal</option>
                    <option value="100" @selected(request('limit') == 100)>100 / hal</option>
                </select>
                <button class="ubp-table-action ubp-table-action-primary" type="submit">Filter</button>
                @if(request('q') || request('semester_id') || request('prodi_id'))
                    <a href="{{ route('unit-activities.index', $unit) }}" class="ubp-table-action">Reset</a>
                @endif
            </form>
        </x-slot:controls>

        <table class="table align-middle ubp-table ubp-data-table">
            <thead>
                <tr>
                    <th>Semester</th>
                    <th>Prodi</th>
                    <th>Kegiatan</th>
                    <th>PIC</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($records as $record)
                    <tr>
                        <td data-label="Semester"><span class="ubp-table-primary">{{ $record->semester?->nama ?? '-' }}</span><span class="ubp-table-muted">{{ $record->semester?->tahun_akademik }}</span></td>
                        <td data-label="Prodi">{{ $record->prodi?->nama ?? '-' }}</td>
                        <td data-label="Kegiatan"><span class="ubp-table-primary">{{ $record->judul }}</span><span class="ubp-table-muted">{{ $record->catatan ? \Illuminate\Support\Str::limit($record->catatan, 54) : 'Tanpa catatan' }}</span></td>
                        <td data-label="PIC">{{ $record->penanggung_jawab ?: '-' }}</td>
                        <td data-label="Tanggal">{{ $record->tanggal?->format('d M Y') ?? '-' }}</td>
                        <td data-label="Status"><x-ui.status-badge :status="$record->status" /></td>
                        <td class="text-end" data-label="Aksi">
                            <div class="ubp-table-action-group justify-content-end">
                                <button class="ubp-table-action ubp-table-action-primary" type="button" data-bs-toggle="modal" data-bs-target="#unitEditModal{{ $record->id }}">Edit</button>
                                <button class="ubp-table-action ubp-table-action-danger" type="button" onclick="triggerDeleteModal(`{{ route('unit-activities.destroy', [$unit, $record]) }}`, `Hapus aktivitas {{ $config['title'] }} ini?`)">Hapus</button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <x-ui.table-empty-row :colspan="$columnCount" title="Belum ada data" message="Tambahkan aktivitas pertama supaya kartu rekap dan grafik mulai terisi." />
                @endforelse
            </tbody>
        </table>

        <x-slot:pagination>
            <div class="ubp-pagination-summary">Menampilkan {{ $records->firstItem() ?? 0 }}-{{ $records->lastItem() ?? 0 }} dari {{ number_format($records->total()) }} data</div>
            <div class="ubp-pagination-controls">
                @if($records->onFirstPage())
                    <span class="ubp-page-button disabled">Prev</span>
                @else
                    <a class="ubp-page-button" href="{{ $records->previousPageUrl() }}">Prev</a>
                @endif
                <span class="ubp-page-current">{{ $records->currentPage() }}/{{ $records->lastPage() }}</span>
                @if($records->hasMorePages())
                    <a class="ubp-page-button" href="{{ $records->nextPageUrl() }}">Next</a>
                @else
                    <span class="ubp-page-button disabled">Next</span>
                @endif
            </div>
        </x-slot:pagination>
    </x-ui.table-shell>

    <div class="modal fade ubp-record-modal" id="unitCreateModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
            <form class="modal-content" method="POST" action="{{ route('unit-activities.store', $unit) }}">
                @csrf
                <div class="modal-header">
                    <div><span class="ubp-auth-eyebrow">Tambah data</span><h5 class="modal-title">Tambah {{ $config['title'] }}</h5></div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @include('unit-activities.partials.fields', ['record' => null, 'prefix' => 'create'])
                </div>
                <div class="modal-footer">
                    <button type="button" class="ubp-table-action" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="ubp-btn ubp-btn-primary">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>

    @foreach($records as $record)
        <div class="modal fade ubp-record-modal" id="unitEditModal{{ $record->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
                <form class="modal-content" method="POST" action="{{ route('unit-activities.update', [$unit, $record]) }}">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <div><span class="ubp-auth-eyebrow">Edit data</span><h5 class="modal-title">Edit {{ $config['title'] }}</h5></div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @include('unit-activities.partials.fields', ['record' => $record, 'prefix' => 'edit-'.$record->id])
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
