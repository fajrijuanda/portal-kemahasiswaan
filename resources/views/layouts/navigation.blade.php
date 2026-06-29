@php
    $items = [
        ['label' => 'Home', 'href' => route('home'), 'active' => request()->routeIs('home'), 'icon' => 'home'],
        ['label' => 'Dashboard', 'href' => route('dashboard'), 'active' => request()->routeIs('dashboard') || request()->routeIs('dashboard.rekap'), 'icon' => 'grid'],
    ];

    if (auth()->user()->hasAnyRole(['super user', 'admin', 'kaprodi', 'kabag', 'warek'])) {
        $items = array_merge($items, [
            ['label' => 'Prestasi', 'href' => route('prestasi.index'), 'active' => request()->is('prestasi*') || request()->is('records/prestasi*'), 'icon' => 'prestasi'],
            ['label' => 'Event', 'href' => route('event.index'), 'active' => request()->is('event*') || request()->is('reimburse*') || request()->is('records/event*') || request()->is('records/reimburse*'), 'icon' => 'event'],
            ['label' => 'Beasiswa', 'href' => route('beasiswa.index'), 'active' => request()->is('beasiswa*') || request()->is('records/beasiswa*'), 'icon' => 'beasiswa'],
            ['label' => 'Tracer', 'href' => route('tracer.index'), 'active' => request()->is('tracer*') || request()->is('records/tracer-study*'), 'icon' => 'tracer'],
            ['label' => 'Unit', 'href' => route('unit-activities.index', 'humas-marketing'), 'active' => request()->is('unit-data*') || request()->is('unit/humas-marketing*') || request()->is('unit/science-center*') || request()->is('unit/alumni-pusat-karir*'), 'icon' => 'prodi'],
            ['label' => 'Ormawa', 'href' => route('ormawa-admin.index', 'data-ormawa'), 'active' => request()->is('ormawa*') || request()->is('ormawa-admin*') || request()->is('unit/pengembangan-ormawa*') || request()->is('master-ormawa*'), 'icon' => 'user'],
        ]);
    }

    if (auth()->user()->hasRole('mahasiswa')) {
        $items[] = ['label' => 'Pengajuan', 'href' => route('student.submissions'), 'active' => request()->routeIs('student.*'), 'icon' => 'beasiswa'];
    }

    if (auth()->user()->hasRole('ormawa')) {
        $items[] = ['label' => 'Panel Ormawa', 'href' => route('ormawa.panel'), 'active' => request()->routeIs('ormawa.*'), 'icon' => 'event'];
    }

    if (auth()->user()->hasAnyRole(['super user', 'admin'])) {
        $items[] = ['label' => 'Master', 'href' => route('master-data.index', 'prodi'), 'active' => request()->is('master-data*') || request()->is('master/*') || request()->is('master-kuota-prestasi*'), 'icon' => 'semester'];
    }

    if (auth()->user()->hasAnyRole(['super user', 'admin', 'kabag'])) {
        $items[] = ['label' => 'Publikasi', 'href' => route('publications.index', 'press-releases'), 'active' => request()->is('publikasi*') || request()->is('press-releases*') || request()->is('karir*'), 'icon' => 'access'];
    }

    if (auth()->user()->hasRole('super user')) {
        $items[] = ['label' => 'User', 'href' => route('users.index'), 'active' => request()->routeIs('users.*') || request()->is('management-user'), 'icon' => 'user'];
    }
@endphp

<aside class="ubp-sidebar" aria-label="Menu portal">
    <a class="ubp-sidebar-logo" href="{{ route('home') }}" aria-label="Portal Kemahasiswaan">
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
