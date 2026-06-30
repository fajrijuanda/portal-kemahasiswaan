<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $pressRelease->title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="portal-theme-scope ubp-public-body">
    @include('public.partials.nav')

    <main class="ubp-public-page">
        <section class="ubp-public-section">
            <div class="ubp-public-section-head">
                <div>
                    <a class="ubp-table-action mb-4 d-inline-flex" href="{{ route('public.news') }}">Kembali ke Berita</a>
                    <span>Berita Resmi</span>
                    <h1>{{ $pressRelease->title }}</h1>
                    <p>{{ $pressRelease->published_at?->format('d M Y') }} - {{ $pressRelease->excerpt }}</p>
                </div>
            </div>

            <article class="ubp-public-news-detail-card">
                @if($pressRelease->cover_path)
                    <img src="{{ asset('storage/'.$pressRelease->cover_path) }}" alt="{{ $pressRelease->title }}" class="ubp-public-article-cover">
                @endif
                <div class="ubp-public-article-content">{!! $pressRelease->content !!}</div>
            </article>
        </section>
    </main>

    <footer class="ubp-public-footer">
        <strong>Portal Kemahasiswaan UBP Karawang</strong>
        <span>Berita resmi kemahasiswaan.</span>
    </footer>
</body>
</html>
