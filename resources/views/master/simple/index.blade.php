<x-app-layout>
    @unless(isset($sectionShell))
        <x-slot name="header">
            <div>
                <h1 class="ubp-title">{{ $config['title'] }}</h1>
                <p class="ubp-subtitle">Kelola pilihan master yang digunakan pada form pengajuan.</p>
            </div>
        </x-slot>
    @endunless

    @isset($sectionShell)
        <x-ui.section-shell :eyebrow="$sectionShell['eyebrow']" :title="$sectionShell['title']" :subtitle="$sectionShell['subtitle']" :items="$sectionShell['items']" :stats="$sectionShell['stats']">
    @endisset

    <x-ui.table-shell class="ubp-table-shell-omnia" :title="$config['title']" subtitle="Data master aktif akan muncul sebagai dropdown pada form.">
        <x-slot:toolbar>
            <button class="ubp-btn ubp-btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#masterCreateModal">+ Tambah Data</button>
        </x-slot:toolbar>

        <table class="table align-middle ubp-table ubp-data-table">
            <thead><tr><th>Nama</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
            <tbody>
                @forelse($records as $record)
                    <tr>
                        <td data-label="Nama"><span class="ubp-table-primary">{{ $record->nama }}</span></td>
                        <td data-label="Status"><x-ui.status-badge :status="$record->is_active ? 'Aktif' : 'Nonaktif'" /></td>
                        <td class="text-end" data-label="Aksi">
                            <button class="ubp-table-action ubp-table-action-primary" type="button" data-bs-toggle="modal" data-bs-target="#masterEditModal{{ $record->id }}">Edit</button>
                            <button class="ubp-table-action ubp-table-action-danger" type="button" onclick="triggerDeleteModal(`{{ route('master.simple.destroy', [$master, $record]) }}`, `Hapus data master ini?`)">Hapus</button>
                        </td>
                    </tr>
                @empty
                    <x-ui.table-empty-row :colspan="3" title="Belum ada data" message="Tambahkan data master pertama." />
                @endforelse
            </tbody>
        </table>

        <x-slot:pagination>{{ $records->links() }}</x-slot:pagination>
    </x-ui.table-shell>

    <div class="modal fade ubp-record-modal" id="masterCreateModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form class="modal-content" method="POST" action="{{ route('master.simple.store', $master) }}">
                @csrf
                <div class="modal-header"><div><span class="ubp-auth-eyebrow">Tambah</span><h5 class="modal-title">{{ $config['title'] }}</h5></div><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <label class="form-label">Nama</label>
                    <input name="nama" class="form-control ubp-control" required>
                    <label class="form-check-label d-flex gap-2 mt-3"><input type="checkbox" class="form-check-input" name="is_active" value="1" checked> Aktif</label>
                </div>
                <div class="modal-footer"><button type="button" class="ubp-table-action" data-bs-dismiss="modal">Batal</button><button class="ubp-btn ubp-btn-primary">Simpan</button></div>
            </form>
        </div>
    </div>

    @foreach($records as $record)
        <div class="modal fade ubp-record-modal" id="masterEditModal{{ $record->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form class="modal-content" method="POST" action="{{ route('master.simple.update', [$master, $record]) }}">
                    @csrf @method('PUT')
                    <div class="modal-header"><div><span class="ubp-auth-eyebrow">Edit</span><h5 class="modal-title">{{ $config['title'] }}</h5></div><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body">
                        <label class="form-label">Nama</label>
                        <input name="nama" class="form-control ubp-control" value="{{ $record->nama }}" required>
                        <label class="form-check-label d-flex gap-2 mt-3"><input type="checkbox" class="form-check-input" name="is_active" value="1" @checked($record->is_active)> Aktif</label>
                    </div>
                    <div class="modal-footer"><button type="button" class="ubp-table-action" data-bs-dismiss="modal">Batal</button><button class="ubp-btn ubp-btn-primary">Simpan</button></div>
                </form>
            </div>
        </div>
    @endforeach

    @isset($sectionShell)
        </x-ui.section-shell>
    @endisset
</x-app-layout>
