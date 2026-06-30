<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Profil Kemahasiswaan UBP Karawang</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="portal-theme-scope ubp-public-body">
    @include('public.partials.nav')

    <main class="ubp-public-page">
        <section class="ubp-public-section ubp-public-page-hero">
            <div class="ubp-public-section-head">
                <div>
                    <span>Profil</span>
                    <h1>Layanan kemahasiswaan, organisasi, karir, dan alumni.</h1>
                    <p>Halaman ini memperkenalkan lingkup kerja kemahasiswaan UBP Karawang sebagai pusat layanan prestasi, beasiswa, Ormawa, tracer study, publikasi, dan karir.</p>
                </div>
            </div>
            <div class="ubp-public-service-grid">
                <article class="ubp-public-service-card"><i><x-ui.app-icon name="prestasi" /></i><strong>Prestasi</strong><small>Monitoring capaian lomba, event, dan dukungan reimbursement mahasiswa.</small></article>
                <article class="ubp-public-service-card"><i><x-ui.app-icon name="beasiswa" /></i><strong>Kesejahteraan</strong><small>Informasi beasiswa dan kanal pengajuan bantuan mahasiswa.</small></article>
                <article class="ubp-public-service-card"><i><x-ui.app-icon name="user" /></i><strong>Ormawa</strong><small>Pendataan organisasi, kegiatan, proposal, dan reimburse acara.</small></article>
                <article class="ubp-public-service-card"><i><x-ui.app-icon name="access" /></i><strong>Karir Alumni</strong><small>Publikasi lowongan, job fair, tracer study, dan jejaring alumni.</small></article>
            </div>
        </section>

        <section class="ubp-public-split">
            <div class="ubp-public-feature">
                <span>Fokus Layanan</span>
                <h2>Menghubungkan data, pengajuan, dan publikasi.</h2>
                <p>Portal dibangun agar data layanan tidak tersebar di banyak dokumen, dan setiap pengajuan dapat dilacak dari status awal sampai selesai direview.</p>
            </div>
            <div class="ubp-public-news-list">
                <article class="ubp-public-news-card"><span>01</span><strong>Transparansi proses</strong><small>Status pengajuan mahasiswa dan Ormawa ditata dari Diajukan, Diproses, sampai Disetujui atau Ditolak.</small></article>
                <article class="ubp-public-news-card"><span>02</span><strong>Publikasi resmi</strong><small>Berita, loker, dan job fair published dapat dibaca tanpa login melalui halaman publik.</small></article>
                <article class="ubp-public-news-card"><span>03</span><strong>Data rekap pimpinan</strong><small>Dashboard internal membantu admin dan pimpinan memantau perkembangan layanan kemahasiswaan.</small></article>
            </div>
        </section>
    </main>

    <footer class="ubp-public-footer">
        <strong>Portal Kemahasiswaan UBP Karawang</strong>
        <span>Profil layanan publik kemahasiswaan.</span>
    </footer>
</body>
</html>
