@props(['title' => null, 'subtitle' => null])

<section {{ $attributes->merge(['class' => 'ubp-table-shell']) }}>
    @if($title || $subtitle || isset($toolbar))
        <div class="ubp-table-toolbar">
            <div>
                @if($title)
                    <h2 class="ubp-table-title">{{ $title }}</h2>
                @endif
                @if($subtitle)
                    <p class="ubp-table-subtitle">{{ $subtitle }}</p>
                @endif
            </div>
            @isset($toolbar)
                <div class="ubp-table-toolbar-actions">{{ $toolbar }}</div>
            @endisset
        </div>
    @endif

    @isset($controls)
        <div class="ubp-table-controls">{{ $controls }}</div>
    @endisset

    <div class="ubp-table-frame">
        <div class="table-responsive ubp-table-responsive">
            {{ $slot }}
        </div>
    </div>

    @isset($pagination)
        <div class="ubp-table-pagination">{{ $pagination }}</div>
    @endisset
</section>