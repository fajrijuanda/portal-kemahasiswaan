<x-app-layout>
    <x-slot name="pageTitle">Home</x-slot>

    @php
        $services = [
            ['title' => 'Dashboard Rekap', 'count' => count($cards), 'desc' => 'Ringkasan data dan grafik layanan.', 'href' => route('dashboard.rekap'), 'icon' => 'grid', 'tone' => 'cyan'],
            ['title' => 'Prestasi Mahasiswa', 'count' => $cards['Prestasi'] ?? 0, 'desc' => 'Input prestasi nasional dan internasional.', 'href' => route('data.index', 'prestasi'), 'icon' => 'prestasi', 'tone' => 'blue'],
            ['title' => 'Event Reimbursement', 'count' => $cards['Event/Reimbursement'] ?? 0, 'desc' => 'Akomodasi, pendaftaran, transport, fasilitas.', 'href' => route('data.index', 'event'), 'icon' => 'event', 'tone' => 'teal'],
            ['title' => 'Tracer Study', 'count' => $cards['Tracer Study Input'] ?? 0, 'desc' => 'Monitoring input tracer sebelum yudisium.', 'href' => route('data.index', 'tracer-study'), 'icon' => 'tracer', 'tone' => 'violet'],
            ['title' => 'Beasiswa', 'count' => $cards['Beasiswa'] ?? 0, 'desc' => 'Rekap penerima dan status beasiswa.', 'href' => route('data.index', 'beasiswa'), 'icon' => 'beasiswa', 'tone' => 'pink'],
            ['title' => 'Humas Marketing', 'count' => $cards['Humas Marketing'] ?? 0, 'desc' => 'Aktivitas promosi dan publikasi.', 'href' => route('unit-data.index', 'humas-marketing'), 'icon' => 'grid', 'tone' => 'rose'],
            ['title' => 'Science Center', 'count' => $cards['Science Center'] ?? 0, 'desc' => 'Program science center.', 'href' => route('unit-data.index', 'science-center'), 'icon' => 'prodi', 'tone' => 'cyan'],
            ['title' => 'Pengembangan Ormawa', 'count' => $cards['Pengembangan Ormawa'] ?? 0, 'desc' => 'Kegiatan dan pembinaan ormawa.', 'href' => route('ormawa-admin.index', 'kegiatan'), 'icon' => 'user', 'tone' => 'amber'],
            ['title' => 'Alumni dan Pusat Karir', 'count' => $cards['Alumni dan Pusat Karir'] ?? 0, 'desc' => 'Alumni, karir, dan relasi industri.', 'href' => route('unit-data.index', 'alumni-pusat-karir'), 'icon' => 'access', 'tone' => 'slate'],
        ];

        if (auth()->user()->hasAnyRole(['super user', 'admin'])) {
            $services[] = ['title' => 'Master Data', 'count' => 5, 'desc' => 'Kelola prodi, semester, lomba, beasiswa, dan kuota.', 'href' => route('master-data.index', 'prodi'), 'icon' => 'semester', 'tone' => 'emerald'];
            $services[] = ['title' => 'Publikasi Karir', 'count' => 2, 'desc' => 'Kelola press release, loker, dan job fair.', 'href' => route('publications.index', 'press-releases'), 'icon' => 'access', 'tone' => 'slate'];
        }

        if (auth()->user()->hasRole('super user')) {
            $services[] = ['title' => 'Management User', 'count' => 17, 'desc' => 'Tambah, ubah, dan hapus akun pengguna.', 'href' => route('users.index'), 'icon' => 'user', 'tone' => 'amber'];
        }
    @endphp

    <section class="ubp-omnia-home-grid">
        <div class="ubp-hero-panel">
            <div class="ubp-hero-glow"></div>
            <div class="position-relative">
                <span class="ubp-auth-eyebrow">PORTAL KEMAHASISWAAN</span>
                <h1>Portal Kemahasiswaan.</h1>
                <p>Kelola prestasi, layanan event reimbursement, tracer study, beasiswa, prodi, dan akses user dari satu portal.</p>
                <a class="ubp-hero-action" href="#layanan">Buka katalog layanan <span>-&gt;</span></a>
            </div>
            <div class="ubp-hero-actions-grid">
                <a href="{{ route('data.index', 'prestasi') }}">
                    <i><x-ui.app-icon name="prestasi" /></i>
                    <strong>Input Data</strong>
                    <small>Tambah prestasi, event, tracer, atau beasiswa.</small>
                </a>
                <a href="{{ route('unit-data.index', 'humas-marketing') }}">
                    <i><x-ui.app-icon name="grid" /></i>
                    <strong>Unit Khusus</strong>
                    <small>Input Humas, Science Center, Ormawa, atau Alumni.</small>
                </a>
                <a href="{{ route('dashboard.rekap') }}">
                    <i><x-ui.app-icon name="grid" /></i>
                    <strong>Lihat Rekap</strong>
                    <small>Buka grafik dan ringkasan kemahasiswaan.</small>
                </a>
                @if(auth()->user()->hasRole('super user'))
                    <a href="{{ route('users.index') }}">
                        <i><x-ui.app-icon name="access" /></i>
                        <strong>Akses User</strong>
                        <small>Kelola role dan akun internal.</small>
                    </a>
                @endif
            </div>
        </div>

        <div class="ubp-omnia-feature-stack">
            <a href="{{ route('dashboard.rekap') }}" class="ubp-gradient-card cyan">
                <span><x-ui.app-icon name="grid" /></span>
                <strong>Dashboard Rekap</strong>
                <small>Data grafik kemahasiswaan</small>
                <em>Unlocked</em>
            </a>
            <a href="{{ route('unit-data.index', 'alumni-pusat-karir') }}" class="ubp-gradient-card blue">
                <span><x-ui.app-icon name="access" /></span>
                <strong>Unit Data Baru</strong>
                <small>Humas, science, ormawa, alumni, dan karir</small>
                <em>CRUD Ready</em>
            </a>
        </div>
    </section>

    <section id="layanan" class="ubp-panel ubp-omnia-catalog">
        <div class="ubp-panel-heading ubp-omnia-catalog-head">
            <div>
                <span>HOME ACCESS</span>
                <h2>Katalog layanan Kemahasiswaan.</h2>
                <p>Klik icon layanan untuk membuka modul yang tersedia di portal internal UBP.</p>
            </div>
            <div class="ubp-catalog-stat-row">
                <span><strong>{{ count($services) }}</strong><small>Layanan</small></span>
                <span><strong>{{ number_format($cards['Prestasi'] ?? 0) }}</strong><small>Prestasi</small></span>
                <span><strong>All</strong><small>Terbuka</small></span>
            </div>
        </div>

        <div class="ubp-icon-launcher-grid">
            @foreach($services as $service)
                <a class="ubp-icon-launcher" href="{{ $service['href'] }}">
                    <span class="ubp-icon-launcher-tile {{ $service['tone'] }}">
                        <i><x-ui.app-icon :name="$service['icon']" /></i>
                        <b>{{ number_format($service['count']) }}</b>
                    </span>
                    <strong>{{ $service['title'] }}</strong>
                    <small></small>
                </a>
            @endforeach
        </div>
    </section>

</x-app-layout>
