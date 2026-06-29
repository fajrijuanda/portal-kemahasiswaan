<x-guest-layout>
    <x-ui.card title="Konfirmasi Password" subtitle="Masukkan password untuk melanjutkan aksi sensitif.">
        <form method="POST" action="{{ route('password.confirm', absolute: false) }}" class="d-grid gap-3">
            @csrf
            <x-ui.form-field name="password" label="Password" type="password" required />
            <x-ui.button class="w-100">Konfirmasi</x-ui.button>
        </form>
    </x-ui.card>
</x-guest-layout>
