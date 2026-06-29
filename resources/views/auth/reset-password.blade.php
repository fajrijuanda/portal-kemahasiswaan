<x-guest-layout>
    <x-ui.card title="Buat Password Baru" subtitle="Gunakan password yang mudah diingat dan aman.">
        <form method="POST" action="{{ route('password.store') }}" class="d-grid gap-3">
            @csrf
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <x-ui.form-field name="email" label="Email" type="email" :value="old('email', $request->email)" required />
            <x-ui.form-field name="password" label="Password Baru" type="password" required />
            <x-ui.form-field name="password_confirmation" label="Konfirmasi Password" type="password" required />

            <x-ui.button class="w-100">Simpan Password</x-ui.button>
        </form>
    </x-ui.card>
</x-guest-layout>
