<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Berita Kemahasiswaan UBP Karawang</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="portal-theme-scope ubp-public-body">
    @include('public.partials.nav')

    <main class="ubp-public-page">
        <section class="ubp-public-section">
            <div class="ubp-public-section-head">
                <div>
                    <span>Berita</span>
                    <h1>Berita dan informasi resmi kemahasiswaan.</h1>
                    <p>Berita yang tampil di halaman ini adalah konten berstatus published dari admin atau kepala bagian terkait.</p>
                </div>
            </div>
            <div class="ubp-public-news-grid">
                @forelse($pressReleases as $item)
                    <a class="ubp-public-news-card" href="{{ route('public.news.show', $item) }}">
                        @if($item->cover_path)
                            <div class="ubp-public-news-img">
                                <img src="{{ asset('storage/'.$item->cover_path) }}" alt="{{ $item->title }}">
                            </div>
                        @else
                            <div class="ubp-public-news-img" style="background: rgba(14, 165, 233, .10); display: grid; place-items: center; color: var(--portal-primary);">
                                <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 6a2 2 0 0 1 2-2h8.5L20 9.5V18a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6z"/><path d="M14 2v8h8"/></svg>
                            </div>
                        @endif
                        <div class="ubp-public-news-body">
                            <span>{{ $item->published_at?->format('d M Y') ?? $item->created_at?->format('d M Y') }}</span>
                            <strong>{{ $item->title }}</strong>
                            <small>{{ \Illuminate\Support\Str::limit($item->excerpt ?: $item->content, 150) }}</small>
                        </div>
                    </a>
                @empty
                    <article class="ubp-public-empty">
                        <strong>Belum ada berita published.</strong>
                        <small>Berita akan tampil setelah dipublikasikan oleh admin atau kabag.</small>
                    </article>
                @endforelse
            </div>
        </section>
    </main>

    <footer class="ubp-public-footer">
        <strong>Portal Kemahasiswaan UBP Karawang</strong>
        <span>Berita resmi kemahasiswaan.</span>
    </footer>
</body>
</html>
