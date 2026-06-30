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
                    <a class="ubp-table-action mb-4 d-inline-flex" href="{{ route('public.news') }}">← Kembali ke Berita</a>
                    <h1 class="ubp-highlight" style="margin-bottom:14px; display:inline-block; font-size: clamp(24px, 2.5vw, 36px);">Berita Resmi</h1>
                    <h2 style="color: var(--portal-text); font-size: clamp(28px, 3.5vw, 48px); font-weight: 950; margin: 0; line-height: 1.1;">{{ $pressRelease->title }}</h2>
                    <p style="margin-top: 14px; font-size: 16px;">{{ $pressRelease->published_at?->format('d M Y') }} - {{ $pressRelease->excerpt }}</p>
                </div>
            </div>
            
            <article class="ubp-public-news-detail-card" style="background: transparent; border: none; box-shadow: none;">
                @if($pressRelease->cover_path)
                    <img src="{{ asset('storage/'.$pressRelease->cover_path) }}" alt="{{ $pressRelease->title }}" style="width:100%;max-height:480px;object-fit:cover;border-radius:20px;border: 1.5px solid var(--portal-text);">
                @endif
                <div style="white-space: pre-line; line-height: 1.85; font-size: 16px; margin-top: 32px; color: var(--portal-text); max-width: 860px;">{{ $pressRelease->content }}</div>
            </article>
        </section>
    </main>
    
    <footer class="ubp-public-footer">
        <strong>Portal Kemahasiswaan UBP Karawang</strong>
        <span>Berita resmi kemahasiswaan.</span>
    </footer>
</body>
</html>
