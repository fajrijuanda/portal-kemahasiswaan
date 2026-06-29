@props([
    'eyebrow' => null,
    'title',
    'subtitle' => null,
    'items' => [],
    'stats' => [],
])

<section class="ubp-section-hero">
    @if($eyebrow)
        <span class="ubp-auth-eyebrow">{{ $eyebrow }}</span>
    @endif
    <h1 class="ubp-title">{{ $title }}</h1>
    @if($subtitle)
        <p class="ubp-subtitle">{{ $subtitle }}</p>
    @endif
</section>

@if(count($stats))
    <div class="ubp-stat-grid">
        @foreach($stats as $stat)
            <article class="ubp-stat-card tone-{{ $stat['tone'] ?? 'blue' }}">
                <div>
                    <small>{{ $stat['label'] }}</small>
                    <strong>{{ $stat['value'] }}</strong>
                    <em>{{ $stat['caption'] ?? '' }}</em>
                </div>
                <span class="ubp-stat-icon"><x-ui.app-icon :name="$stat['icon'] ?? 'grid'" /></span>
            </article>
        @endforeach
    </div>
@endif

<section class="ubp-section-layout">
    <aside class="ubp-mini-sidebar" aria-label="{{ $title }}">
        @foreach($items as $item)
            <a class="ubp-mini-sidebar-item {{ ($item['active'] ?? false) ? 'active' : '' }}" href="{{ $item['href'] }}">
                <span class="ubp-mini-sidebar-icon"><x-ui.app-icon :name="$item['icon'] ?? 'grid'" /></span>
                <span>
                    <strong>{{ $item['label'] }}</strong>
                    <small>{{ $item['count'] ?? 0 }} data</small>
                </span>
            </a>
        @endforeach
    </aside>

    <div class="ubp-section-content">
        {{ $slot }}
    </div>
</section>
