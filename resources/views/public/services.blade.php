<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Layanan Kemahasiswaan UBP Karawang</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="portal-theme-scope ubp-public-body">
    @include('public.partials.nav')

    @php
        $services = [
            ['title' => 'Prestasi Mahasiswa', 'desc' => 'Pendataan lomba, kategori event, scope, juara, dan kuota prestasi prodi.', 'icon' => 'prestasi'],
            ['title' => 'Event Mahasiswa', 'desc' => 'Pengajuan kegiatan, dokumentasi, dan status review kegiatan mahasiswa.', 'icon' => 'event'],
            ['title' => 'Reimbursement', 'desc' => 'Pengajuan klaim dengan foto, surat tugas, sertifikat, dan link penyelenggara.', 'icon' => 'tracer'],
            ['title' => 'Beasiswa', 'desc' => 'Pengajuan KIP, Kacer, Tahfidz, dan jenis beasiswa lainnya.', 'icon' => 'beasiswa'],
            ['title' => 'Ormawa', 'desc' => 'Profil organisasi, proposal kegiatan, dan reimbursement acara Ormawa.', 'icon' => 'user'],
            ['title' => 'Tracer Study', 'desc' => 'Kanal pengumpulan data alumni untuk kebutuhan evaluasi dan akreditasi.', 'icon' => 'access'],
            ['title' => 'Publikasi', 'desc' => 'Press release resmi yang disusun dan dipublikasikan bagian terkait.', 'icon' => 'master'],
            ['title' => 'Karir', 'desc' => 'Kurasi lowongan kerja dan job fair untuk mahasiswa serta alumni.', 'icon' => 'science'],
        ];
    @endphp

    <main class="ubp-public-page">
        <section class="ubp-public-section">
            <div class="ubp-public-section-head">
                <div>
                    <span>Layanan</span>
                    <h1>Layanan publik dan kanal internal yang saling terhubung.</h1>
                    <p>Setiap layanan memiliki halaman internal untuk input, verifikasi, dan rekap, sementara ringkasan publik tetap mudah diakses dari luar portal.</p>
                </div>
            </div>
            <div class="ubp-public-service-grid">
                @foreach($services as $service)
                    <article class="ubp-public-service-card">
                        <i><x-ui.app-icon :name="$service['icon']" /></i>
                        <strong>{{ $service['title'] }}</strong>
                        <small>{{ $service['desc'] }}</small>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="ubp-public-band">
            <div>
                <span>Panel Internal</span>
                <h2>Mahasiswa dan Ormawa dapat mengajukan layanan melalui akun portal masing-masing.</h2>
            </div>
            <a class="ubp-btn ubp-btn-primary" href="{{ auth()->check() ? route('home') : route('login') }}">Login</a>
        </section>
    </main>

    <footer class="ubp-public-footer">
        <strong>Portal Kemahasiswaan UBP Karawang</strong>
        <span>Katalog layanan kemahasiswaan.</span>
    </footer>
</body>
</html>
