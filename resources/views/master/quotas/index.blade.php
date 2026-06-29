<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="ubp-title">Kuota Prestasi Prodi</h1>
            <p class="ubp-subtitle">Atur slot dukungan prestasi tiap prodi dan pantau pemakaian terverifikasi.</p>
        </div>
    </x-slot>

    @isset($sectionShell)
        <x-ui.section-shell :eyebrow="$sectionShell['eyebrow']" :title="$sectionShell['title']" :subtitle="$sectionShell['subtitle']" :items="$sectionShell['items']" :stats="$sectionShell['stats']">
    @endisset

    <x-ui.table-shell class="ubp-table-shell-omnia" title="Kuota Prestasi" subtitle="Slot terpakai dihitung dari prestasi berstatus Terverifikasi.">
        <x-slot:toolbar>
            <button class="ubp-btn ubp-btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#quotaCreateModal">+ Tambah Kuota</button>
        </x-slot:toolbar>
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
