<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Portal Kemahasiswaan') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="portal-theme-scope ubp-auth-screen">
        <main class="ubp-auth-shell">
            <section class="ubp-auth-showcase">
                <div class="ubp-color-strip"></div>
                <div class="ubp-auth-brand">
                    <span class="ubp-auth-logo"><img src="{{ asset('images/logo-ubp.png') }}" alt="Logo UBP Karawang"></span>
                    <span>
                        <strong>Portal Kemahasiswaan</strong>
                        <small>Universitas Buana Perjuangan Karawang</small>
                    </span>
                </div>
                <div class="ubp-auth-copy">
                    <span class="ubp-auth-eyebrow">Portal internal</span>
                    <h1>Kelola layanan kemahasiswaan dalam satu portal.</h1>
                    <p>Prestasi, event reimbursement, tracer study, beasiswa, dan dashboard rekap prodi tersaji untuk Kaprodi, Kabag, Warek, Admin, dan Super User.</p>
                </div>
                <div class="ubp-auth-stats">
                    <span><i><x-ui.app-icon name="grid" /></i><strong>5</strong><small>Layanan</small></span>
                    <span><i><x-ui.app-icon name="user" /></i><strong>12</strong><small>Kaprodi</small></span>
                    <span><i><x-ui.app-icon name="prodi" /></i><strong>UBP</strong><small>Karawang</small></span>
                </div>
            </section>

            <section class="ubp-auth-card-panel">
                <div class="ubp-auth-card">
                    {{ $slot }}
                </div>
            </section>
        </main>
    </body>
</html>
