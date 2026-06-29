<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Links Kemahasiswaan UBP Karawang</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="portal-theme-scope ubp-public-body">
    @include('public.partials.nav')

    @php
        $links = [
            ['label' => 'Login Portal', 'desc' => 'Masuk ke panel internal sesuai role akun.', 'url' => auth()->check() ? route('home') : route('login')],
            ['label' => 'Layanan Kemahasiswaan', 'desc' => 'Buka katalog layanan publik kemahasiswaan.', 'url' => route('public.services')],
            ['label' => 'Berita Kemahasiswaan', 'desc' => 'Lihat press release resmi yang sudah published.', 'url' => route('public.news')],
        ];
    @endphp

    <main class="ubp-public-page">
        <section class="ubp-public-section">
            <div class="ubp-public-section-head">
                <div>
                    <span>Links</span>
                    <h1>Tautan cepat publik, karir, dan job fair.</h1>
                    <p>Halaman ini menjadi pintu ringkas menuju kanal penting, termasuk informasi loker dan job fair yang sudah dipublikasikan.</p>
                </div>
            </div>
            <div class="ubp-public-service-grid">
                @foreach($links as $link)
                    <a class="ubp-public-link-card" href="{{ $link['url'] }}">
                        <strong>{{ $link['label'] }}</strong>
                        <small>{{ $link['desc'] }}</small>
                    </a>
                @endforeach
            </div>
        </section>

        <section class="ubp-public-section">
            <div class="ubp-public-section-head">
                <div>
                    <span>Karir</span>
                    <h2>Lowongan kerja dan job fair.</h2>
                    <p>Informasi karir yang dikurasi untuk mahasiswa dan alumni UBP Karawang.</p>
                </div>
            </div>
            <div class="ubp-public-career-grid">
                @forelse($careerPosts as $item)
                    <article class="ubp-public-career-card">
                        <span>{{ $item->type }}</span>
                        <strong>{{ $item->title }}</strong>
                        <small>{{ $item->company ?: 'Mitra Karir' }}{{ $item->location ? ' - '.$item->location : '' }}</small>
                        @if($item->deadline)
                            <em>Deadline {{ $item->deadline->format('d M Y') }}</em>
                        @endif
                        @if($item->external_url)
                            <a href="{{ $item->external_url }}" target="_blank" rel="noreferrer">Buka link</a>
                        @endif
                    </article>
                @empty
                    <article class="ubp-public-empty">
                        <strong>Belum ada informasi karir published.</strong>
                        <small>Loker dan job fair akan tampil setelah dipublikasikan admin.</small>
                    </article>
                @endforelse
            </div>
        </section>
    </main>

    <footer class="ubp-public-footer">
        <strong>Portal Kemahasiswaan UBP Karawang</strong>
        <span>Links, loker, dan job fair.</span>
    </footer>
</body>
</html>
