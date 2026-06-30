<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Portal Kemahasiswaan UBP Karawang</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="portal-theme-scope ubp-public-body">
    @php
        $loginUrl = auth()->check() ? route('home') : route('login');
        $loginLabel = auth()->check() ? 'Masuk Portal' : 'Login';
        $stats = [
            ['value' => '12+', 'label' => 'Layanan'],
            ['value' => '4', 'label' => 'Unit Aktif'],
            ['value' => '24/7', 'label' => 'Portal Digital'],
        ];
    @endphp

    @include('public.partials.nav')

    <main>
        <section class="ubp-landing-hero">
            {{-- Decorative floating shapes --}}
            <div class="ubp-landing-orb ubp-landing-orb--1"></div>
            <div class="ubp-landing-orb ubp-landing-orb--2"></div>
            <div class="ubp-landing-orb ubp-landing-orb--3"></div>
            <div class="ubp-landing-grid-bg"></div>

            <div class="ubp-landing-hero-inner">
                <div class="ubp-landing-badge">
                    <span class="ubp-landing-badge-dot"></span>
                    Portal Kemahasiswaan — UBP Karawang
                </div>

                <h1 class="ubp-landing-heading">
                    Wujudkan <span class="ubp-landing-gradient-text">Prestasi</span> &
                    <span class="ubp-landing-gradient-text">Karir</span> Terbaikmu
                </h1>

                <p class="ubp-landing-sub">
                    Akses layanan beasiswa, organisasi, publikasi, dan karir dalam satu portal terpadu.
                </p>

                <div class="ubp-landing-cta">
                    <a class="ubp-landing-btn-primary" href="{{ $loginUrl }}">
                        <span>{{ $loginLabel }} Panel</span>
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </a>
                    <a class="ubp-landing-btn-ghost" href="{{ route('public.news') }}">Lihat Berita</a>
                </div>
            </div>

            <div class="ubp-landing-stats">
                @foreach($stats as $stat)
                    <div class="ubp-landing-stat">
                        <strong>{{ $stat['value'] }}</strong>
                        <small>{{ $stat['label'] }}</small>
                    </div>
                @endforeach
            </div>
        </section>

        <section id="layanan" class="ubp-public-section">
            <div class="ubp-public-section-head">
                <h2>Layanan</h2>
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

        <section id="publikasi" class="ubp-public-section">
            <div class="ubp-public-section-head">
                <h2>Berita</h2>
            </div>
            <div class="ubp-public-news-grid">
                @forelse($pressReleases as $item)
                    <a class="ubp-public-news-card" href="{{ route('public.news.show', $item) }}">
                        <span>{{ $item->published_at?->format('d M Y') ?? $item->created_at?->format('d M Y') }}</span>
                        <strong>{{ $item->title }}</strong>
                        <small>{{ \Illuminate\Support\Str::limit($item->excerpt ?: $item->content, 128) }}</small>
                    </a>
                @empty
                    <article class="ubp-public-empty">
                        <strong>Belum ada berita published.</strong>
                        <small>Konten publik akan tampil setelah kabag/admin mempublikasikan berita.</small>
                    </article>
                @endforelse
            </div>
        </section>

        <section id="karir" class="ubp-public-section">
            <div class="ubp-public-section-head">
                <h2>Karir</h2>
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

        <section class="ubp-public-band">
            <div>
                <span>Portal Internal</span>
                <h2>Mahasiswa, Ormawa, dan admin dapat melanjutkan proses pengajuan melalui panel login</h2>
            </div>
            <a class="ubp-btn ubp-btn-primary" href="{{ $loginUrl }}">{{ $loginLabel }}</a>
        </section>

        <section id="faq" class="ubp-public-section ubp-public-faq">
            <div class="ubp-public-section-head">
                <h2>FAQ</h2>
            </div>
            <div class="ubp-public-faq-grid">
                <article><strong>Apakah halaman ini perlu login?</strong><small>Tidak. Halaman publik bisa dibuka langsung dari route `/`.</small></article>
                <article><strong>Di mana mengajukan beasiswa atau proposal?</strong><small>Pengajuan dilakukan lewat panel sesuai role setelah login.</small></article>
                <article><strong>Siapa yang mengelola publikasi?</strong><small>Berita, loker, dan job fair dikelola admin/kabag melalui portal internal.</small></article>
            </div>
        </section>
    </main>

    <footer class="ubp-public-footer">
        <strong>Portal Kemahasiswaan UBP Karawang</strong>
        <span>Prestasi, beasiswa, Ormawa, tracer study, publikasi, dan karir.</span>
    </footer>
</body>
</html>
