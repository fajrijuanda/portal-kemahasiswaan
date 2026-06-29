<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="ubp-title">Panel Ormawa</h1>
            <p class="ubp-subtitle">{{ $ormawa->nama }} - proposal kegiatan dan reimbursement acara.</p>
        </div>
    </x-slot>

    <div class="ubp-stat-grid">
        <article class="ubp-stat-card tone-amber"><div><small>Proposal</small><strong>{{ $ormawa->proposals->count() }}</strong><em>Diajukan</em></div><span class="ubp-stat-icon"><x-ui.app-icon name="event" /></span></article>
        <article class="ubp-stat-card tone-teal"><div><small>Reimbursement</small><strong>{{ $ormawa->reimbursements->count() }}</strong><em>Acara Ormawa</em></div><span class="ubp-stat-icon"><x-ui.app-icon name="beasiswa" /></span></article>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <x-ui.table-shell title="Ajukan Proposal" subtitle="Proposal kegiatan akan direview admin/kabag.">
                <form method="POST" enctype="multipart/form-data" action="{{ route('ormawa.proposals.store') }}" class="row g-3">
                    @csrf
                    <div class="col-12"><label class="form-label">Judul Kegiatan</label><input name="judul" class="form-control ubp-control" required></div>
                    <div class="col-md-6"><label class="form-label">Tanggal</label><input type="date" name="tanggal" class="form-control ubp-control"></div>
                    <div class="col-md-6"><label class="form-label">Lokasi</label><input name="lokasi" class="form-control ubp-control"></div>
                    <div class="col-12"><label class="form-label">Deskripsi</label><textarea name="deskripsi" class="form-control ubp-control" rows="3"></textarea></div>
                    <div class="col-12"><label class="form-label">File Proposal</label><input type="file" name="proposal_path" class="form-control ubp-control" accept=".pdf,image/*"></div>
                    <div class="col-12"><button class="ubp-btn ubp-btn-primary w-100">Kirim Proposal</button></div>
                </form>
            </x-ui.table-shell>
        </div>
        <div class="col-lg-6">
            <x-ui.table-shell title="Ajukan Reimbursement" subtitle="Foto, surat tugas, sertifikat, dan link penyelenggara wajib diisi.">
                <form method="POST" enctype="multipart/form-data" action="{{ route('ormawa.reimbursements.store') }}" class="row g-3">
                    @csrf
                    <div class="col-md-6"><label class="form-label">Jenis</label><select name="jenis_reimbursement" class="form-select ubp-control">@foreach(['Akomodasi', 'Pendaftaran', 'Transport', 'Fasilitas', 'Lainnya'] as $item)<option>{{ $item }}</option>@endforeach</select></div>
                    <div class="col-md-6"><label class="form-label">Nominal</label><input type="number" name="nominal" class="form-control ubp-control"></div>
                    <div class="col-12"><label class="form-label">Nama Kegiatan</label><input name="nama_kegiatan" class="form-control ubp-control" required></div>
                    <div class="col-md-6"><label class="form-label">Tanggal</label><input type="date" name="tanggal" class="form-control ubp-control"></div>
                    <div class="col-md-6"><label class="form-label">Link Penyelenggara</label><input type="url" name="link_penyelenggara" class="form-control ubp-control" required></div>
                    <div class="col-md-4"><label class="form-label">Foto</label><input type="file" name="foto_path" class="form-control ubp-control" accept=".pdf,image/*" required></div>
                    <div class="col-md-4"><label class="form-label">Surat Tugas</label><input type="file" name="surat_tugas_path" class="form-control ubp-control" accept=".pdf,image/*" required></div>
                    <div class="col-md-4"><label class="form-label">Sertifikat</label><input type="file" name="sertifikat_path" class="form-control ubp-control" accept=".pdf,image/*" required></div>
                    <div class="col-12"><button class="ubp-btn ubp-btn-primary w-100">Kirim Reimbursement</button></div>
                </form>
            </x-ui.table-shell>
        </div>
    </div>

    <x-ui.table-shell class="mt-4" title="Riwayat Ormawa" subtitle="Pantau status proposal dan reimbursement.">
        <table class="table align-middle ubp-table ubp-data-table">
            <thead><tr><th>Tipe</th><th>Judul</th><th>Semester</th><th>Status</th></tr></thead>
            <tbody>
                @foreach($ormawa->proposals as $item)
                    <tr><td>Proposal</td><td>{{ $item->judul }}</td><td>{{ $item->semester?->nama }}</td><td><x-ui.status-badge :status="$item->status" /></td></tr>
                @endforeach
                @foreach($ormawa->reimbursements as $item)
                    <tr><td>Reimbursement</td><td>{{ $item->nama_kegiatan }}</td><td>{{ $item->semester?->nama }}</td><td><x-ui.status-badge :status="$item->status" /></td></tr>
                @endforeach
            </tbody>
        </table>
    </x-ui.table-shell>
</x-app-layout>
