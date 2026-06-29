@props(['title' => null, 'subtitle' => null, 'actions' => null])

<section {{ $attributes->merge(['class' => 'ubp-card']) }}>
    @if($title || $subtitle || $actions)
        <div class="ubp-card-header">
            <div>
                @if($title)
                    <h2 class="ubp-card-title">{{ $title }}</h2>
                @endif
                @if($subtitle)
                    <p class="ubp-card-subtitle">{{ $subtitle }}</p>
                @endif
            </div>
            @if($actions)
                <div>{{ $actions }}</div>
            @endif
        </div>
    @endif
    <div class="ubp-card-body">
        {{ $slot }}
    </div>
</section>
