<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $pressRelease->title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="portal-theme-scope" style="font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;">
    <main class="ubp-portal-content ubp-portal-content-wide">
        <a class="ubp-table-action mb-3 d-inline-flex" href="{{ route('public.index') }}">Kembali</a>
        <article class="ubp-table-shell">
            @if($pressRelease->cover_path)
                <img src="{{ asset('storage/'.$pressRelease->cover_path) }}" alt="{{ $pressRelease->title }}" style="width:100%;max-height:360px;object-fit:cover;border-radius:18px;margin-bottom:24px;">
            @endif
            <span class="ubp-auth-eyebrow">{{ $pressRelease->published_at?->format('d M Y') }}</span>
            <h1 class="ubp-title">{{ $pressRelease->title }}</h1>
            <p class="ubp-subtitle">{{ $pressRelease->excerpt }}</p>
            <div style="white-space: pre-line; line-height: 1.8;">{{ $pressRelease->content }}</div>
        </article>
    </main>
</body>
</html>
