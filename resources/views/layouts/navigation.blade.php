@php
    $items = [
        ['label' => 'Home', 'href' => route('dashboard'), 'active' => request()->routeIs('dashboard'), 'icon' => 'home'],
        ['label' => 'Rekap', 'href' => route('dashboard.rekap'), 'active' => request()->routeIs('dashboard.rekap'), 'icon' => 'grid'],
        ['label' => 'Prestasi', 'href' => route('records.index', 'prestasi'), 'active' => request()->is('prestasi*') || request()->is('records/prestasi*'), 'icon' => 'prestasi'],
        ['label' => 'Event', 'href' => route('records.index', 'event'), 'active' => request()->is('event*') || request()->is('records/event*'), 'icon' => 'event'],
        ['label' => 'Tracer', 'href' => route('records.index', 'tracer-study'), 'active' => request()->is('tracer-study*') || request()->is('records/tracer-study*'), 'icon' => 'tracer'],
        ['label' => 'Beasiswa', 'href' => route('records.index', 'beasiswa'), 'active' => request()->is('beasiswa*') || request()->is('records/beasiswa*'), 'icon' => 'beasiswa'],
        ['label' => 'Humas', 'href' => route('unit-activities.index', 'humas-marketing'), 'active' => request()->is('unit/humas-marketing*'), 'icon' => 'grid'],
        ['label' => 'Science', 'href' => route('unit-activities.index', 'science-center'), 'active' => request()->is('unit/science-center*'), 'icon' => 'prodi'],
        ['label' => 'Ormawa', 'href' => route('unit-activities.index', 'pengembangan-ormawa'), 'active' => request()->is('unit/pengembangan-ormawa*'), 'icon' => 'user'],
        ['label' => 'Alumni', 'href' => route('unit-activities.index', 'alumni-pusat-karir'), 'active' => request()->is('unit/alumni-pusat-karir*'), 'icon' => 'access'],
    ];

    if (auth()->user()->hasAnyRole(['super user', 'admin'])) {
        $items[] = ['label' => 'Prodi', 'href' => route('master.prodi.index'), 'active' => request()->routeIs('master.prodi.*'), 'icon' => 'prodi'];
        $items[] = ['label' => 'Semester', 'href' => route('master.semester.index'), 'active' => request()->routeIs('master.semester.*'), 'icon' => 'semester'];
    }

    if (auth()->user()->hasRole('super user')) {
        $items[] = ['label' => 'User', 'href' => route('users.index'), 'active' => request()->routeIs('users.*') || request()->is('management-user'), 'icon' => 'user'];
    }
@endphp

<aside class="ubp-sidebar" aria-label="Menu portal">
    <a class="ubp-sidebar-logo" href="{{ route('dashboard') }}" aria-label="Portal Kemahasiswaan">
        <img src="{{ asset('images/logo-ubp.png') }}" alt="Logo UBP Karawang">
    </a>

    <nav class="ubp-sidebar-nav">
        @foreach($items as $item)
            <a class="ubp-sidebar-item {{ $item['active'] ? 'active' : '' }}" href="{{ $item['href'] }}" title="{{ $item['label'] }}">
                <span class="ubp-sidebar-icon"><x-ui.app-icon :name="$item['icon']" /></span>
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>

    <div class="ubp-sidebar-bottom">
        <a class="ubp-sidebar-item {{ request()->routeIs('profile.edit') ? 'active' : '' }}" href="{{ route('profile.edit') }}" title="Profil">
            <span class="ubp-sidebar-icon"><x-ui.app-icon name="user" /></span>
            <span>Profil</span>
        </a>
    </div>
</aside>

<nav class="ubp-mobile-tabs" aria-label="Menu mobile">
    <button type="button" class="ubp-mobile-launcher-btn" data-bs-toggle="offcanvas" data-bs-target="#mobileMenuLauncher" aria-controls="mobileMenuLauncher">
        <x-ui.app-icon name="grid" />
        <span>Menu Navigasi</span>
    </button>
</nav>

<div class="offcanvas offcanvas-bottom ubp-mobile-launcher-sheet" tabindex="-1" id="mobileMenuLauncher" aria-labelledby="mobileMenuLauncherLabel">
    <div class="offcanvas-header justify-content-center pb-0">
        <div class="ubp-sheet-drag-handle"></div>
    </div>
    <div class="offcanvas-body">
        <div class="ubp-launcher-grid">
            @foreach($items as $item)
                <a class="ubp-launcher-item {{ $item['active'] ? 'active' : '' }}" href="{{ $item['href'] }}">
                    <div class="ubp-launcher-icon"><x-ui.app-icon :name="$item['icon']" /></div>
                    <span class="ubp-launcher-label">{{ $item['label'] }}</span>
                </a>
            @endforeach
        </div>
    </div>
</div>
