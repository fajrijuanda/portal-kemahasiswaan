<x-guest-layout>
    <x-ui.card title="Registrasi Dinonaktifkan" subtitle="Akun dibuat oleh super user melalui halaman manajemen user.">
        <div class="alert alert-info mb-3">Silakan hubungi pengelola sistem jika membutuhkan akses.</div>
        <x-ui.button :href="route('login')" class="w-100">Kembali Login</x-ui.button>
    </x-ui.card>
</x-guest-layout>
