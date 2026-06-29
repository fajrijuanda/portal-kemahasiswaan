<x-guest-layout>
    <x-ui.card title="Reset Password" subtitle="Masukkan email akun Anda untuk menerima tautan reset.">
        <x-auth-session-status class="alert alert-success" :status="session('status')" />

        <form method="POST" action="{{ route('password.email', absolute: false) }}" class="d-grid gap-3">
            @csrf
            <x-ui.form-field name="email" label="Email" type="email" :value="old('email')" required />
            <x-ui.button class="w-100">Kirim Link Reset</x-ui.button>
            <x-ui.button variant="outline" :href="route('login')" class="w-100">Kembali Login</x-ui.button>
        </form>
    </x-ui.card>
</x-guest-layout>
