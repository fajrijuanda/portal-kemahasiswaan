@php
    $loginUrl = auth()->check() ? route('home') : route('login');
    $loginLabel = auth()->check() ? 'Masuk Portal' : 'Login';
    $navItems = [
        ['label' => 'Beranda', 'route' => 'public.index', 'active' => ['public.index']],
        ['label' => 'Profil', 'route' => 'public.profile', 'active' => ['public.profile']],
        ['label' => 'Layanan', 'route' => 'public.services', 'active' => ['public.services']],
        ['label' => 'Berita', 'route' => 'public.news', 'active' => ['public.news', 'public.news.show', 'public.press.show']],
        ['label' => 'Links', 'route' => 'public.links', 'active' => ['public.links']],
    ];
@endphp

<header class="ubp-public-nav">
    <a class="ubp-public-brand" href="{{ route('public.index') }}">
        <span><img src="{{ asset('images/logo-ubp.png') }}" alt="Logo UBP Karawang"></span>
        <strong>Portal Kemahasiswaan<small>Universitas Buana Perjuangan Karawang</small></strong>
    </a>
    <nav class="ubp-public-links" aria-label="Navigasi publik">
        @foreach($navItems as $item)
            <a href="{{ route($item['route']) }}" @class(['active' => request()->routeIs($item['active'])])>
                {{ $item['label'] }}
            </a>
        @endforeach
        <span class="ubp-public-language"><i></i> English</span>
        <span class="ubp-public-language is-active"><i></i> Indonesian</span>
    </nav>
    <a class="ubp-public-login" href="{{ $loginUrl }}">{{ $loginLabel }}</a>
</header>
