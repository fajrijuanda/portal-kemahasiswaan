<x-app-layout>
    @unless(isset($sectionShell))
        <x-slot name="header">
            <div>
                <h1 class="ubp-title">Kuota Prestasi Prodi</h1>
                <p class="ubp-subtitle">Atur slot dukungan prestasi tiap prodi dan pantau pemakaian terverifikasi.</p>
            </div>
        </x-slot>
    @endunless

    @isset($sectionShell)
        <x-ui.section-shell :eyebrow="$sectionShell['eyebrow']" :title="$sectionShell['title']" :subtitle="$sectionShell['subtitle']" :items="$sectionShell['items']" :stats="$sectionShell['stats']">
    @endisset

    <x-ui.table-shell class="ubp-table-shell-omnia" title="Kuota Prestasi" subtitle="Slot terpakai dihitung dari prestasi berstatus Terverifikasi.">
        <x-slot:toolbar>
            <button class="ubp-btn ubp-btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#quotaCreateModal">+ Tambah Kuota</button>
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
            <thead><tr><th>Semester</th><th>Prodi</th><th>Slot</th><th>Terpakai</th><th>Sisa</th><th class="text-end">Aksi</th></tr></thead>
            <tbody>
                @forelse($quotas as $quota)
                    <tr>
                        <td>{{ $quota->semester?->nama ?? '-' }}</td>
                        <td>{{ $quota->prodi?->nama ?? '-' }}</td>
                        <td>{{ $quota->slot_prestasi }}</td>
                        <td>{{ $quota->terpakai }}</td>
                        <td>{{ max(0, $quota->slot_prestasi - $quota->terpakai) }}</td>
                        <td class="text-end">
                            <button class="ubp-table-action ubp-table-action-primary" type="button" data-bs-toggle="modal" data-bs-target="#quotaEditModal{{ $quota->id }}">Edit</button>
                            <button class="ubp-table-action ubp-table-action-danger" type="button" onclick="triggerDeleteModal(`{{ route('master.quotas.destroy', $quota) }}`, `Hapus kuota ini?`)">Hapus</button>
                        </td>
                    </tr>
                @empty
                    <x-ui.table-empty-row :colspan="6" title="Belum ada kuota" message="Tambahkan slot dukungan prestasi per prodi." />
                @endforelse
            </tbody>
        </table>
        <x-slot:pagination>{{ $quotas->links() }}</x-slot:pagination>
    </x-ui.table-shell>

    @include('master.quotas.partials.form-modal', ['id' => 'quotaCreateModal', 'title' => 'Tambah Kuota', 'action' => route('master.quotas.store'), 'method' => null, 'quota' => null])
    @foreach($quotas as $quota)
        @include('master.quotas.partials.form-modal', ['id' => 'quotaEditModal'.$quota->id, 'title' => 'Edit Kuota', 'action' => route('master.quotas.update', $quota), 'method' => 'PUT', 'quota' => $quota])
    @endforeach

    @isset($sectionShell)
        </x-ui.section-shell>
    @endisset
</x-app-layout>
