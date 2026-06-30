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
                    <a class="ubp-public-service-card is-simple tone-{{ $service['tone'] }}" href="{{ route('public.services.show', $service['slug']) }}">
                        <i><x-ui.app-icon :name="$service['icon']" /></i>
                        <strong>{{ $service['title'] }}</strong>
                    </a>
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
