<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Portal Kemahasiswaan') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="portal-theme-scope">
        <div class="ubp-app-shell">
            <div class="ubp-color-strip"></div>
            @include('layouts.navigation')

            <section class="ubp-portal-area">
                <header class="ubp-portal-header">
                    <div class="ubp-header-brand">
                        <span class="ubp-header-logo"><img src="{{ asset('images/logo-ubp.png') }}" alt="Logo UBP Karawang"></span>
                        <span>
                            <strong>Portal Kemahasiswaan</strong>
                            <small>{{ $pageTitle ?? 'Portal Layanan' }} - {{ auth()->user()->roles->first()?->name ?? 'user' }}</small>
                        </span>
                    </div>
                    <div class="ubp-header-search">
                        <span class="text-muted" style="width: 18px; height: 18px; display: inline-flex;"><x-ui.app-icon name="search" /></span>
                        <input type="search" placeholder="Search portal..." aria-label="Search portal">
                    </div>
                    <div class="dropdown ms-auto">
                        <button class="ubp-profile-button" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </button>
                        <div class="dropdown-menu dropdown-menu-end ubp-profile-menu">
                            <div class="d-flex gap-3 px-3 py-3">
                                <span class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                                <div class="min-w-0">
                                    <strong class="d-block">{{ auth()->user()->name }}</strong>
                                    <small class="text-muted d-block text-truncate">{{ auth()->user()->email }}</small>
                                    <x-ui.role-badge :role="auth()->user()->roles->first()?->name ?? 'user'" />
                                </div>
                            </div>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM4 21a8 8 0 0 1 16 0"/></svg>
                                Profil
                            </a>
                            <button class="dropdown-item text-danger" type="button" data-bs-toggle="modal" data-bs-target="#logoutConfirmModal">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/></svg>
                                Logout
                            </button>
                        </div>
                    </div>
                </header>

                @isset($header)
                    <div class="ubp-page-header">
                        {{ $header }}
                    </div>
                @endisset

                <main class="ubp-portal-content {{ isset($fullWidth) ? 'ubp-portal-content-wide' : '' }}">
                    <x-ui.alert-modal />
                    {{ $slot }}
                </main>
            </section>
        </div>
        <x-ui.logout-modal />
        <x-ui.delete-modal />
    </body>
</html>
