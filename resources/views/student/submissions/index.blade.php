<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="ubp-title">Pengajuan Mahasiswa</h1>
            <p class="ubp-subtitle">Ajukan beasiswa dan lomba untuk direview admin kemahasiswaan.</p>
        </div>
    </x-slot>

    <div class="ubp-stat-grid">
        <article class="ubp-stat-card tone-emerald"><div><small>Beasiswa</small><strong>{{ $beasiswa->count() }}</strong><em>Pengajuan saya</em></div><span class="ubp-stat-icon"><x-ui.app-icon name="beasiswa" /></span></article>
        <article class="ubp-stat-card tone-blue"><div><small>Lomba</small><strong>{{ $prestasi->count() }}</strong><em>Pengajuan saya</em></div><span class="ubp-stat-icon"><x-ui.app-icon name="prestasi" /></span></article>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <x-ui.table-shell title="Ajukan Beasiswa" subtitle="Data akan masuk status Diajukan.">
                <form method="POST" action="{{ route('student.beasiswa.store') }}" class="row g-3">
                    @csrf
                    <div class="col-12"><label class="form-label">Jenis Beasiswa</label><select name="scholarship_type_id" class="form-select ubp-control" required>@foreach($scholarshipTypes as $type)<option value="{{ $type->id }}">{{ $type->nama }}</option>@endforeach</select></div>
                    <div class="col-12"><label class="form-label">Nominal</label><input type="number" name="nominal" class="form-control ubp-control"></div>
                    <div class="col-12"><label class="form-label">Catatan</label><textarea name="catatan" class="form-control ubp-control" rows="3"></textarea></div>
                    <div class="col-12"><button class="ubp-btn ubp-btn-primary w-100">Kirim Pengajuan Beasiswa</button></div>
                </form>
            </x-ui.table-shell>
        </div>
        <div class="col-lg-6">
            <x-ui.table-shell title="Ajukan Lomba" subtitle="Pilih master lomba, kategori, scope, dan capaian.">
                <form method="POST" action="{{ route('student.prestasi.store') }}" class="row g-3">
                    @csrf
                    <div class="col-12"><label class="form-label">Nama Lomba</label><select name="competition_id" class="form-select ubp-control" required>@foreach($competitions as $competition)<option value="{{ $competition->id }}">{{ $competition->nama }}</option>@endforeach</select></div>
                    <div class="col-12"><label class="form-label">Nama Kegiatan</label><input name="nama_kegiatan" class="form-control ubp-control" required></div>
                    <div class="col-md-4"><label class="form-label">Kategori</label><select name="kategori_event" class="form-select ubp-control">@foreach(['Kelompok', 'Perorangan'] as $item)<option>{{ $item }}</option>@endforeach</select></div>
                    <div class="col-md-4"><label class="form-label">Scope</label><select name="scope" class="form-select ubp-control">@foreach(['Lokal', 'Regional', 'Nasional', 'Internasional'] as $item)<option>{{ $item }}</option>@endforeach</select></div>
                    <div class="col-md-4"><label class="form-label">Juara</label><select name="juara" class="form-select ubp-control">@foreach(['1', '2', '3', 'Favorit', 'Finalis', 'Harapan'] as $item)<option>{{ $item }}</option>@endforeach</select></div>
                    <div class="col-md-6"><label class="form-label">Penyelenggara</label><input name="penyelenggara" class="form-control ubp-control"></div>
                    <div class="col-md-6"><label class="form-label">Tanggal</label><input type="date" name="tanggal" class="form-control ubp-control"></div>
                    <div class="col-12"><label class="form-label">Link Publikasi</label><input type="url" name="publikasi_url" class="form-control ubp-control"></div>
                    <div class="col-12"><button class="ubp-btn ubp-btn-primary w-100">Kirim Pengajuan Lomba</button></div>
                </form>
            </x-ui.table-shell>
        </div>
    </div>

    <x-ui.table-shell class="mt-4" title="Riwayat Pengajuan" subtitle="Pantau status beasiswa dan lomba yang Anda kirim.">
        <table class="table align-middle ubp-table ubp-data-table">
            <thead><tr><th>Tipe</th><th>Judul</th><th>Prodi</th><th>Status</th></tr></thead>
            <tbody>
                @foreach($beasiswa as $item)
                    <tr><td>Beasiswa</td><td>{{ $item->scholarshipType?->nama ?? $item->jenis_beasiswa }}</td><td>{{ $item->prodi?->nama }}</td><td><x-ui.status-badge :status="$item->status" /></td></tr>
                @endforeach
                @foreach($prestasi as $item)
                    <tr><td>Lomba</td><td>{{ $item->competition?->nama ?? $item->nama_kegiatan }}</td><td>{{ $item->prodi?->nama }}</td><td><x-ui.status-badge :status="$item->status" /></td></tr>
                @endforeach
            </tbody>
        </table>
    </x-ui.table-shell>
</x-app-layout>
