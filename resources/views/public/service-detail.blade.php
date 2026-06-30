<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $service['title'] }} | Portal Kemahasiswaan UBP Karawang</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="portal-theme-scope ubp-public-body">
    @include('public.partials.nav')

    <main class="ubp-public-page">
        <section class="ubp-public-section ubp-public-service-detail">
            <div class="ubp-public-service-detail-icon tone-{{ $service['tone'] }}">
                <x-ui.app-icon :name="$service['icon']" />
            </div>
            <div class="ubp-public-section-head">
                <div>
                    <span>Layanan</span>
                    <h1>{{ $service['title'] }}</h1>
                    <p>{{ $service['desc'] }}</p>
                </div>
            </div>
            <div class="ubp-public-news-grid">
                <article class="ubp-public-news-card">
                    <span>01</span>
                    <strong>Informasi layanan</strong>
                    <small>Halaman ini menjadi ringkasan publik untuk memahami ruang lingkup layanan sebelum masuk ke panel internal.</small>
                </article>
                <article class="ubp-public-news-card">
                    <span>02</span>
                    <strong>Pengajuan lewat panel</strong>
                    <small>Proses input, verifikasi, dan tindak lanjut dilakukan melalui akun portal sesuai role pengguna.</small>
                </article>
                <article class="ubp-public-news-card">
                    <span>03</span>
                    <strong>Rekap terpusat</strong>
                    <small>Data layanan yang sudah masuk akan terhubung ke dashboard dan rekap internal kemahasiswaan.</small>
                </article>
            </div>
        </section>

        <section class="ubp-public-band">
            <div>
                <span>Portal Internal</span>
                <h2>Masuk ke panel untuk melanjutkan pengajuan atau pengelolaan {{ strtolower($service['title']) }}.</h2>
            </div>
            <a class="ubp-btn ubp-btn-primary" href="{{ auth()->check() ? route('home') : route('login') }}">Login</a>
        </section>
    </main>

    <footer class="ubp-public-footer">
        <strong>Portal Kemahasiswaan UBP Karawang</strong>
        <span>{{ $service['title'] }}</span>
    </footer>
</body>
</html>
