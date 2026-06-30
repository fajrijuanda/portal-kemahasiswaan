<x-app-layout>
    @unless(isset($sectionShell))
        <x-slot name="header">
            <h1 class="ubp-title">Kuota Prestasi</h1>
            <p class="ubp-subtitle">Kelola slot dukungan prestasi per program studi.</p>
        </x-slot>
    @endunless

    @isset($sectionShell)
        <x-ui.section-shell :eyebrow="$sectionShell['eyebrow']" :title="$sectionShell['title']" :subtitle="$sectionShell['subtitle']" :items="$sectionShell['items']" :stats="$sectionShell['stats']">
    @endisset

    <div class="ubp-stat-grid">
        <article class="ubp-stat-card tone-violet">
            <div><small>Total Kuota</small><strong>{{ number_format($quotas->total()) }}</strong><em>Slot tercatat</em></div>
            <span class="ubp-stat-icon"><x-ui.app-icon name="semester" /></span>
        </article>
        <article class="ubp-stat-card tone-blue">
            <div><small>Total Slot</small><strong>{{ number_format($quotas->sum('slot_prestasi')) }}</strong><em>Halaman ini</em></div>
            <span class="ubp-stat-icon"><x-ui.app-icon name="grid" /></span>
        </article>
        <article class="ubp-stat-card tone-emerald">
            <div><small>Terpakai</small><strong>{{ number_format($quotas->sum('terpakai')) }}</strong><em>Prestasi disetujui</em></div>
            <span class="ubp-stat-icon"><x-ui.app-icon name="access" /></span>
        </article>
    </div>

    <x-ui.table-shell class="ubp-table-shell-omnia" title="Daftar Kuota Prestasi" subtitle="Slot dukungan prestasi yang dipakai untuk pemantauan per prodi dan semester.">
        <x-slot:toolbar>
            <a class="ubp-btn ubp-btn-primary" href="{{ route('kuota-prestasi.table') }}">Kelola Kuota</a>
        </x-slot:toolbar>

        <x-slot:controls>
            <form method="GET" class="ubp-record-table-controls">
                <label class="ubp-record-search">
                    <x-ui.app-icon name="grid" />
                    <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari prodi atau semester...">
                </label>
                <select name="semester_id" class="form-select ubp-control">
                    <option value="">Semua semester</option>
                    @foreach($semesters as $semester)
                        <option value="{{ $semester->id }}" @selected((string) request('semester_id') === (string) $semester->id)>{{ $semester->nama }}</option>
                    @endforeach
                </select>
                <select name="prodi_id" class="form-select ubp-control">
                    <option value="">Semua prodi</option>
                    @foreach($prodis as $prodi)
                        <option value="{{ $prodi->id }}" @selected((string) request('prodi_id') === (string) $prodi->id)>{{ $prodi->nama }}</option>
                    @endforeach
                </select>
                <select name="limit" class="form-select ubp-control" onchange="this.form.submit()">
                    <option value="10" @selected(request('limit', 10) == 10)>10 / hal</option>
                    <option value="25" @selected(request('limit') == 25)>25 / hal</option>
                    <option value="50" @selected(request('limit') == 50)>50 / hal</option>
                    <option value="100" @selected(request('limit') == 100)>100 / hal</option>
                </select>
                <button class="ubp-table-action ubp-table-action-primary" type="submit">Filter</button>
                @if(request()->hasAny(['q', 'semester_id', 'prodi_id']))
                    <a class="ubp-table-action" href="{{ route('master-data.index', 'quotas') }}">Reset</a>
                @endif
            </form>
        </x-slot:controls>

        <table class="table align-middle ubp-table ubp-data-table">
            <thead>
                <tr>
                    <th>Semester</th>
                    <th>Prodi</th>
                    <th>Slot Prestasi</th>
                    <th>Terpakai</th>
                    <th>Sisa Slot</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($quotas as $quota)
                    <tr>
                        <td data-label="Semester"><span class="ubp-table-primary">{{ $quota->semester?->nama ?? '-' }}</span></td>
                        <td data-label="Prodi">{{ $quota->prodi?->nama ?? '-' }}</td>
                        <td data-label="Slot Prestasi">{{ number_format($quota->slot_prestasi) }}</td>
                        <td data-label="Terpakai">{{ number_format($quota->terpakai) }}</td>
                        <td data-label="Sisa Slot">{{ number_format(max((int) $quota->slot_prestasi - (int) $quota->terpakai, 0)) }}</td>
                        <td class="text-end" data-label="Aksi">
                            <a class="ubp-table-action ubp-table-action-primary" href="{{ route('kuota-prestasi.table') }}">Edit</a>
                        </td>
                    </tr>
                @empty
                    <x-ui.table-empty-row colspan="6" title="Belum ada kuota" message="Kuota prestasi akan muncul setelah ditambahkan dari menu Prestasi." />
                @endforelse
            </tbody>
        </table>
        <x-slot:pagination>
            <div class="ubp-pagination-summary">Menampilkan {{ $quotas->firstItem() ?? 0 }}-{{ $quotas->lastItem() ?? 0 }} dari {{ number_format($quotas->total()) }} data</div>
            <div class="ubp-pagination-controls">
                @if($quotas->onFirstPage())
                    <span class="ubp-page-button disabled">Prev</span>
                @else
                    <a class="ubp-page-button" href="{{ $quotas->previousPageUrl() }}">Prev</a>
                @endif
                <span class="ubp-page-current">{{ $quotas->currentPage() }}/{{ max($quotas->lastPage(), 1) }}</span>
                @if($quotas->hasMorePages())
                    <a class="ubp-page-button" href="{{ $quotas->nextPageUrl() }}">Next</a>
                @else
                    <span class="ubp-page-button disabled">Next</span>
                @endif
            </div>
        </x-slot:pagination>
    </x-ui.table-shell>

    @isset($sectionShell)
        </x-ui.section-shell>
    @endisset
</x-app-layout>
