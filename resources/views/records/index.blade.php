<x-app-layout>
    @unless(isset($sectionShell))
        <x-slot name="header">
            <div>
                <h1 class="ubp-title">{{ $config['title'] }}</h1>
                <p class="ubp-subtitle">Input, filter, dan verifikasi data {{ strtolower($config['title']) }}.</p>
            </div>
        </x-slot>
    @endunless

    @php
        $moduleIcon = match($module) {
            'prestasi' => 'prestasi',
            'event' => 'event',
            'tracer-study' => 'tracer',
            'beasiswa' => 'beasiswa',
            default => 'grid',
        };
        $moduleTone = match($module) {
            'prestasi' => 'blue',
            'event' => 'orange',
            'tracer-study' => 'violet',
            'beasiswa' => 'emerald',
            default => 'slate',
        };
        $tableFields = $module === 'beasiswa'
            ? array_intersect_key($config['fields'], array_flip(['nama_mahasiswa', 'scholarship_type_id', 'nominal', 'status']))
            : array_slice($config['fields'], 0, 4, true);
        $columnCount = count($tableFields) + 3;
    @endphp

    @isset($sectionShell)
        <x-ui.section-shell
            :eyebrow="$sectionShell['eyebrow']"
            :title="$sectionShell['title']"
            :subtitle="$sectionShell['subtitle']"
            :items="$sectionShell['items']"
            :stats="$sectionShell['stats']"
        >
    @endisset

    <div class="ubp-stat-grid">
        <article class="ubp-stat-card tone-{{ $moduleTone }}">
            <div><small>Total Data</small><strong>{{ number_format($records->total()) }}</strong><em>{{ $config['title'] }}</em></div>
            <span class="ubp-stat-icon"><x-ui.app-icon :name="$moduleIcon" /></span>
        </article>
        <article class="ubp-stat-card tone-blue">
            <div><small>Ditampilkan</small><strong>{{ number_format($records->count()) }}</strong><em>Halaman ini</em></div>
            <span class="ubp-stat-icon"><x-ui.app-icon name="grid" /></span>
        </article>
        <article class="ubp-stat-card tone-violet">
            <div><small>Semester</small><strong>{{ request('semester_id') ? '1' : 'All' }}</strong><em>{{ request('semester_id') ? 'Terfilter' : 'Semua data' }}</em></div>
            <span class="ubp-stat-icon"><x-ui.app-icon name="semester" /></span>
        </article>
        <article class="ubp-stat-card tone-slate">
            <div><small>Scope Prodi</small><strong>{{ auth()->user()->hasRole('kaprodi') || request('prodi_id') ? '1' : 'All' }}</strong><em>{{ auth()->user()->hasRole('kaprodi') ? 'Kaprodi' : 'Portal' }}</em></div>
            <span class="ubp-stat-icon"><x-ui.app-icon name="prodi" /></span>
        </article>
    </div>

    @if($module === 'prestasi' && $achievementQuotas->isNotEmpty())
        <x-ui.table-shell class="mb-4" title="Kuota Prestasi Prodi" subtitle="Slot dukungan dan pemakaian prestasi terverifikasi.">
            <table class="table align-middle ubp-table ubp-data-table">
                <thead><tr><th>Semester</th><th>Prodi</th><th>Slot</th><th>Terpakai</th><th>Sisa</th></tr></thead>
                <tbody>
                    @foreach($achievementQuotas as $quota)
                        <tr>
                            <td>{{ $quota->semester?->nama ?? '-' }}</td>
                            <td>{{ $quota->prodi?->nama ?? '-' }}</td>
                            <td>{{ $quota->slot_prestasi }}</td>
                            <td>{{ $quota->terpakai }}</td>
                            <td>{{ max(0, $quota->slot_prestasi - $quota->terpakai) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </x-ui.table-shell>
    @endif

    <x-ui.table-shell class="ubp-table-shell-omnia" :title="'Daftar '.$config['title']" subtitle="Data terbaru tersaji dalam format ringkas untuk verifikasi cepat.">
        <x-slot:toolbar>
            <button class="ubp-btn ubp-btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#recordCreateModal">+ Tambah Data</button>
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
                    <a href="{{ route('data.index', $module) }}" class="ubp-table-action">Reset</a>
                @endif
            </form>
        </x-slot:controls>

        <table class="table align-middle ubp-table ubp-data-table">
            <thead>
                <tr>
                    <th>Semester</th>
                    <th>Prodi</th>
                    @foreach($tableFields as $field)
                        <th>{{ $field['label'] }}</th>
                    @endforeach
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($records as $record)
                    <tr>
                        <td data-label="Semester"><span class="ubp-table-primary">{{ $record->semester?->nama ?? '-' }}</span><span class="ubp-table-muted">{{ $record->semester?->tahun_akademik }}</span></td>
                        <td data-label="Prodi">{{ $record->prodi?->nama ?? '-' }}</td>
                        @foreach($tableFields as $name => $field)
                            <td data-label="{{ $field['label'] }}">
                                @if(isset($field['relation']))
                                    {{ data_get($record, $field['relation']) ?: '-' }}
                                @elseif($field['type'] === 'file' && $record->{$name})
                                    <a class="ubp-table-link" href="{{ asset('storage/'.$record->{$name}) }}" target="_blank">Lihat file</a>
                                @elseif($field['type'] === 'url' && $record->{$name})
                                    <a class="ubp-table-link" href="{{ $record->{$name} }}" target="_blank">Publikasi</a>
                                @elseif($name === 'status')
                                    <x-ui.status-badge :status="$record->{$name} ?? 'Draft'" />
                                @else
                                    {{ filled($record->{$name}) ? $record->{$name} : '-' }}
                                @endif
                            </td>
                        @endforeach
                        <td class="text-end" data-label="Aksi">
                            <div class="ubp-table-action-group justify-content-end">
                                <button class="ubp-table-action" type="button" data-bs-toggle="modal" data-bs-target="#recordOverviewModal{{ $record->id }}">Overview</button>
                                <button class="ubp-table-action ubp-table-action-primary" type="button" data-bs-toggle="modal" data-bs-target="#recordEditModal{{ $record->id }}">Edit</button>
                                <button class="ubp-table-action ubp-table-action-danger" type="button" onclick="triggerDeleteModal(`{{ route('records.destroy', [$module, $record]) }}`, `Hapus data {{ $config['title'] }} ini?`)">Hapus</button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <x-ui.table-empty-row :colspan="$columnCount" title="Tidak ada data yang cocok" message="Coba ubah filter atau tambahkan data baru." />
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

    <div class="modal fade ubp-record-modal" id="recordCreateModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
            <form class="modal-content" method="POST" enctype="multipart/form-data" action="{{ route('records.store', $module) }}">
                @csrf
                <div class="modal-header">
                    <div><span class="ubp-auth-eyebrow">Tambah data</span><h5 class="modal-title">Tambah {{ $config['title'] }}</h5></div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @include('records.partials.fields', ['record' => null, 'prefix' => 'create'])
                </div>
                <div class="modal-footer">
                    <button type="button" class="ubp-table-action" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="ubp-btn ubp-btn-primary">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>

    @foreach($records as $record)
        <div class="modal fade ubp-record-modal" id="recordOverviewModal{{ $record->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <div><span class="ubp-auth-eyebrow">Overview</span><h5 class="modal-title">{{ $config['title'] }}</h5></div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6"><small class="text-muted d-block">Semester</small><strong>{{ $record->semester?->nama ?? '-' }}</strong></div>
                            <div class="col-md-6"><small class="text-muted d-block">Prodi</small><strong>{{ $record->prodi?->nama ?? '-' }}</strong></div>
                            @foreach($config['fields'] as $name => $field)
                                <div class="{{ $field['type'] === 'textarea' ? 'col-12' : 'col-md-6' }}">
                                    <small class="text-muted d-block">{{ $field['label'] }}</small>
                                    @if(isset($field['relation']))
                                        <strong>{{ data_get($record, $field['relation']) ?: '-' }}</strong>
                                    @elseif($field['type'] === 'file' && $record->{$name})
                                        <a class="ubp-table-link" href="{{ asset('storage/'.$record->{$name}) }}" target="_blank">Lihat file</a>
                                    @elseif($field['type'] === 'url' && $record->{$name})
                                        <a class="ubp-table-link" href="{{ $record->{$name} }}" target="_blank">{{ $record->{$name} }}</a>
                                    @elseif($name === 'status')
                                        <x-ui.status-badge :status="$record->{$name} ?? 'Draft'" />
                                    @else
                                        <strong>{{ filled($record->{$name}) ? $record->{$name} : '-' }}</strong>
                                    @endif
                                </div>
                            @endforeach
                            <div class="col-12"><small class="text-muted d-block">Dibuat oleh</small><strong>{{ $record->creator?->name ?? '-' }}</strong></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade ubp-record-modal" id="recordEditModal{{ $record->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
                <form class="modal-content" method="POST" enctype="multipart/form-data" action="{{ route('records.update', [$module, $record]) }}">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <div><span class="ubp-auth-eyebrow">Edit data</span><h5 class="modal-title">Edit {{ $config['title'] }}</h5></div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @include('records.partials.fields', ['record' => $record, 'prefix' => 'edit-'.$record->id])
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
