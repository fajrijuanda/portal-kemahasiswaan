<x-app-layout>
    <x-slot name="pageTitle">Home</x-slot>

    @php
        $services = [
            ['title' => 'Dashboard', 'count' => count($cards), 'desc' => 'Ringkasan data dan grafik kemahasiswaan.', 'href' => route('dashboard'), 'icon' => 'grid', 'tone' => 'cyan'],
            ['title' => 'Prestasi', 'count' => $cards['Prestasi'] ?? 0, 'desc' => 'Prestasi lomba dan verifikasi capaian mahasiswa.', 'href' => route('prestasi.index'), 'icon' => 'prestasi', 'tone' => 'blue'],
            ['title' => 'Event & Reimburse', 'count' => $cards['Event/Reimbursement'] ?? 0, 'desc' => 'Event kegiatan dan pengajuan reimbursement.', 'href' => route('event.index'), 'icon' => 'event', 'tone' => 'teal'],
            ['title' => 'Beasiswa', 'count' => $cards['Beasiswa'] ?? 0, 'desc' => 'Penerima, jenis, nominal, dan status beasiswa.', 'href' => route('beasiswa.index'), 'icon' => 'beasiswa', 'tone' => 'pink'],
            ['title' => 'Tracer Study', 'count' => $cards['Tracer Study Input'] ?? 0, 'desc' => 'Monitoring input tracer sebelum yudisium.', 'href' => route('tracer.index'), 'icon' => 'tracer', 'tone' => 'violet'],
            ['title' => 'Unit Kemahasiswaan', 'count' => ($cards['Humas Marketing'] ?? 0) + ($cards['Science Center'] ?? 0) + ($cards['Alumni dan Pusat Karir'] ?? 0), 'desc' => 'Humas, Science Center, Alumni, dan Pusat Karir.', 'href' => route('unit-data.index', 'humas-marketing'), 'icon' => 'prodi', 'tone' => 'cyan'],
            ['title' => 'Ormawa', 'count' => $cards['Pengembangan Ormawa'] ?? 0, 'desc' => 'Data ormawa, kegiatan, proposal, dan reimbursement.', 'href' => route('ormawa-admin.index', 'data-ormawa'), 'icon' => 'user', 'tone' => 'amber'],
        ];

        if (auth()->user()->hasAnyRole(['super user', 'admin'])) {
            $services[] = ['title' => 'Master Data', 'count' => 5, 'desc' => 'Kelola prodi, semester, lomba, beasiswa, dan kuota.', 'href' => route('master-data.index', 'prodi'), 'icon' => 'semester', 'tone' => 'emerald'];
            $services[] = ['title' => 'Publikasi', 'count' => 2, 'desc' => 'Kelola press release, loker, dan job fair.', 'href' => route('publications.index', 'press-releases'), 'icon' => 'access', 'tone' => 'slate'];
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
                <p>Kelola rekap, prestasi, beasiswa, tracer study, unit, Ormawa, master data, dan publikasi dari satu portal.</p>
                <a class="ubp-hero-action" href="#layanan">Buka katalog layanan <span>-&gt;</span></a>
            </div>
            <div class="ubp-hero-actions-grid">
                <a href="{{ route('prestasi.index') }}">
                    <i><x-ui.app-icon name="prestasi" /></i>
                    <strong>Prestasi</strong>
                    <small>Prestasi lomba dan reimbursement mahasiswa.</small>
                </a>
                <a href="{{ route('unit-data.index', 'humas-marketing') }}">
                    <i><x-ui.app-icon name="grid" /></i>
                    <strong>Unit</strong>
                    <small>Humas, Science Center, Alumni, dan karir.</small>
                </a>
                <a href="{{ route('dashboard') }}">
                    <i><x-ui.app-icon name="grid" /></i>
                    <strong>Dashboard</strong>
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
            <a href="{{ route('dashboard') }}" class="ubp-gradient-card cyan">
                <span><x-ui.app-icon name="grid" /></span>
                <strong>Dashboard</strong>
                <small>Data grafik kemahasiswaan</small>
                <em>Unlocked</em>
            </a>
            <a href="{{ route('unit-data.index', 'alumni-pusat-karir') }}" class="ubp-gradient-card blue">
                <span><x-ui.app-icon name="access" /></span>
                <strong>Unit Kemahasiswaan</strong>
                <small>Humas, science, alumni, dan karir</small>
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
