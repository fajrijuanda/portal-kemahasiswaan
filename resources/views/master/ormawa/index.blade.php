<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="ubp-title">Master Ormawa</h1>
            <p class="ubp-subtitle">Kelola profil Ormawa dan relasi akun login masing-masing.</p>
        </div>
    </x-slot>

    <x-ui.table-shell class="ubp-table-shell-omnia" title="Daftar Ormawa" subtitle="Klik overview untuk melihat profil dan ringkasan kegiatan.">
        <x-slot:toolbar>
            <button class="ubp-btn ubp-btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#ormawaCreateModal">+ Tambah Ormawa</button>
        </x-slot:toolbar>
        <table class="table align-middle ubp-table ubp-data-table">
            <thead><tr><th>Ormawa</th><th>Jenis</th><th>Akun</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
            <tbody>
                @forelse($ormawas as $ormawa)
                    <tr>
                        <td><span class="ubp-table-primary">{{ $ormawa->nama }}</span><span class="ubp-table-muted">{{ $ormawa->pembina ?: 'Pembina belum diisi' }}</span></td>
                        <td>{{ $ormawa->jenis ?: '-' }}</td>
                        <td>{{ $ormawa->user?->email ?? '-' }}</td>
                        <td><x-ui.status-badge :status="$ormawa->status" /></td>
                        <td class="text-end">
                            <button class="ubp-table-action" type="button" data-bs-toggle="modal" data-bs-target="#ormawaOverviewModal{{ $ormawa->id }}">Overview</button>
                            <button class="ubp-table-action ubp-table-action-primary" type="button" data-bs-toggle="modal" data-bs-target="#ormawaEditModal{{ $ormawa->id }}">Edit</button>
                            <button class="ubp-table-action ubp-table-action-danger" type="button" onclick="triggerDeleteModal(`{{ route('master.ormawa.destroy', $ormawa) }}`, `Hapus Ormawa ini?`)">Hapus</button>
                        </td>
                    </tr>
                @empty
                    <x-ui.table-empty-row :colspan="5" title="Belum ada Ormawa" message="Tambahkan master Ormawa pertama." />
                @endforelse
            </tbody>
        </table>
        <x-slot:pagination>{{ $ormawas->links() }}</x-slot:pagination>
    </x-ui.table-shell>

    @include('master.ormawa.partials.form-modal', ['id' => 'ormawaCreateModal', 'title' => 'Tambah Ormawa', 'action' => route('master.ormawa.store'), 'method' => null, 'ormawa' => null])

    @foreach($ormawas as $ormawa)
        <div class="modal fade ubp-record-modal" id="ormawaOverviewModal{{ $ormawa->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header"><div><span class="ubp-auth-eyebrow">Overview Ormawa</span><h5 class="modal-title">{{ $ormawa->nama }}</h5></div><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6"><small class="text-muted d-block">Jenis</small><strong>{{ $ormawa->jenis ?: '-' }}</strong></div>
                            <div class="col-md-6"><small class="text-muted d-block">Kontak</small><strong>{{ $ormawa->kontak ?: '-' }}</strong></div>
                            <div class="col-md-6"><small class="text-muted d-block">Pembina/PIC</small><strong>{{ $ormawa->pembina ?: '-' }}</strong></div>
                            <div class="col-md-6"><small class="text-muted d-block">Akun Login</small><strong>{{ $ormawa->user?->email ?? '-' }}</strong></div>
                            <div class="col-12"><small class="text-muted d-block">Deskripsi</small><strong>{{ $ormawa->deskripsi ?: '-' }}</strong></div>
                            <div class="col-md-4"><small class="text-muted d-block">Kegiatan</small><strong>{{ $ormawa->activities->count() }}</strong></div>
                            <div class="col-md-4"><small class="text-muted d-block">Proposal</small><strong>{{ $ormawa->proposals->count() }}</strong></div>
                            <div class="col-md-4"><small class="text-muted d-block">Reimbursement</small><strong>{{ $ormawa->reimbursements->count() }}</strong></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('master.ormawa.partials.form-modal', ['id' => 'ormawaEditModal'.$ormawa->id, 'title' => 'Edit Ormawa', 'action' => route('master.ormawa.update', $ormawa), 'method' => 'PUT', 'ormawa' => $ormawa])
    @endforeach
</x-app-layout>
