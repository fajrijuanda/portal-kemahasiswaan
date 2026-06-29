<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Portal Publik Kemahasiswaan</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="portal-theme-scope" style="font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;">
    <main class="ubp-portal-content ubp-portal-content-wide">
        <section class="ubp-hero-panel">
            <span class="ubp-auth-eyebrow">Portal Kemahasiswaan</span>
            <h1 class="ubp-title">Informasi Kemahasiswaan UBP Karawang</h1>
            <p class="ubp-subtitle">Press release, lowongan kerja, dan job fair yang sudah dipublikasikan.</p>
            <a class="ubp-btn ubp-btn-primary" href="{{ route('login') }}">Masuk Panel</a>
        </section>

        <x-ui.table-shell class="mt-4" title="Press Release" subtitle="Berita terbaru dari bagian kemahasiswaan.">
            <div class="row g-3">
                @forelse($pressReleases as $item)
                    <div class="col-md-4">
                        <a class="ubp-service-card d-block text-decoration-none h-100" href="{{ route('public.press.show', $item) }}">
                            <span class="ubp-service-icon"><x-ui.app-icon name="grid" /></span>
                            <strong>{{ $item->title }}</strong>
                            <small>{{ \Illuminate\Support\Str::limit($item->excerpt ?: $item->content, 110) }}</small>
                        </a>
                    </div>
                @empty
                    <p class="text-muted mb-0">Belum ada press release published.</p>
                @endforelse
            </div>
        </x-ui.table-shell>

        <x-ui.table-shell class="mt-4" title="Karir" subtitle="Lowongan kerja dan job fair terbaru.">
            <div class="row g-3">
                @forelse($careerPosts as $item)
                    <div class="col-md-3">
                        <article class="ubp-service-card h-100">
                            <span class="ubp-service-icon"><x-ui.app-icon name="access" /></span>
                            <strong>{{ $item->title }}</strong>
                            <small>{{ $item->type }}{{ $item->company ? ' - '.$item->company : '' }}</small>
                            @if($item->external_url)
                                <a class="ubp-table-link mt-2 d-inline-block" href="{{ $item->external_url }}" target="_blank">Buka link</a>
                            @endif
                        </article>
                    </div>
                @empty
                    <p class="text-muted mb-0">Belum ada informasi karir published.</p>
                @endforelse
            </div>
        </x-ui.table-shell>
    </main>
</body>
</html>
