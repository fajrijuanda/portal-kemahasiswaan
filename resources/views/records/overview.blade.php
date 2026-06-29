<x-app-layout>
    <section class="ubp-section-hero">
        <span class="ubp-auth-eyebrow">{{ $overview['eyebrow'] }}</span>
        <h1 class="ubp-title">{{ $overview['title'] }}</h1>
        <p class="ubp-subtitle">{{ $overview['subtitle'] }}</p>
    </section>

    <div class="ubp-stat-grid">
        @foreach($stats as $stat)
            <article class="ubp-stat-card tone-{{ $stat['tone'] }}">
                <div>
                    <small>{{ $stat['label'] }}</small>
                    <strong>{{ $stat['value'] }}</strong>
                    <em>{{ $stat['caption'] }}</em>
                </div>
                <span class="ubp-stat-icon"><x-ui.app-icon :name="$stat['icon']" /></span>
            </article>
        @endforeach
    </div>

    <section class="ubp-panel ubp-record-overview-panel">
        <div class="ubp-panel-heading ubp-omnia-catalog-head">
            <div>
                <span>PILIH TABEL</span>
                <h2>{{ $overview['title'] }}</h2>
                <p>Buka salah satu tabel di bawah ini untuk melihat data detail, filter, dan aksi CRUD.</p>
            </div>
        </div>

        <div class="ubp-overview-launcher-grid">
            @foreach($overview['items'] as $item)
                <a class="ubp-overview-launcher" href="{{ $item['href'] }}">
                    <span class="ubp-icon-launcher-tile {{ $item['tone'] }}">
                        <i><x-ui.app-icon :name="$item['icon']" /></i>
                        <b>{{ $stats[$loop->index]['value'] }}</b>
                    </span>
                    <strong>{{ $item['label'] }}</strong>
                    <small>{{ $item['description'] }}</small>
                </a>
            @endforeach
        </div>
    </section>
</x-app-layout>
