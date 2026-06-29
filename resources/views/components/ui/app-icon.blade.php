@props(['name' => 'grid'])

@php
    $name = strtolower($name);
@endphp

<svg {{ $attributes->merge(['class' => 'ubp-svg-icon']) }} viewBox="0 0 24 24" fill="none" aria-hidden="true">
    @switch($name)
        @case('home')
            <path d="M4 11.5 12 5l8 6.5V20a1 1 0 0 1-1 1h-5v-6h-4v6H5a1 1 0 0 1-1-1v-8.5Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
            @break
        @case('prestasi')
            <path d="M8 4h8v3a4 4 0 0 1-8 0V4Z" stroke="currentColor" stroke-width="2"/>
            <path d="M8 6H5a3 3 0 0 0 3 3M16 6h3a3 3 0 0 1-3 3M12 11v4M8 20h8M10 15h4v5h-4v-5Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            @break
        @case('event')
            <path d="M7 4v3M17 4v3M5 9h14M6 6h12a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1Z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            <path d="m9 14 2 2 4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            @break
        @case('tracer')
            <path d="M5 6h14M5 12h14M5 18h8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            <path d="m15 17 2 2 4-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            @break
        @case('beasiswa')
            <path d="m4 9 8-4 8 4-8 4-8-4Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
            <path d="M7 11v4c2.8 2 7.2 2 10 0v-4M20 9v6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            @break
        @case('prodi')
            <path d="M4 20V7l8-4 8 4v13" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
            <path d="M9 20v-6h6v6M8 9h.01M12 9h.01M16 9h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            @break
        @case('semester')
            <path d="M6 4h12a1 1 0 0 1 1 1v15H5V5a1 1 0 0 1 1-1Z" stroke="currentColor" stroke-width="2"/>
            <path d="M8 8h8M8 12h8M8 16h5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            @break
        @case('user')
            <path d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM4 21a8 8 0 0 1 16 0" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            @break
        @case('access')
            <path d="M12 3 5 6v5c0 4.5 2.8 8 7 10 4.2-2 7-5.5 7-10V6l-7-3Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
            <path d="m9 12 2 2 4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            @break
        @default
            <path d="M5 5h5v5H5V5ZM14 5h5v5h-5V5ZM5 14h5v5H5v-5ZM14 14h5v5h-5v-5Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
    @endswitch
</svg>
