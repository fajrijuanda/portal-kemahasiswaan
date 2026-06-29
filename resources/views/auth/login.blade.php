<x-guest-layout>
    <x-ui.card title="Masuk ke Portal" subtitle="Gunakan akun internal UBP Karawang.">
        <x-auth-session-status class="alert alert-success" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="d-grid gap-3">
            @csrf

            <x-ui.form-field name="email" label="Email" type="email" :value="old('email')" required />
            <x-ui.form-field name="password" label="Password" type="password" required />

            <div class="d-flex align-items-center justify-content-between gap-3">
                <label class="form-check-label d-flex align-items-center gap-2 text-muted">
                    <input type="checkbox" class="form-check-input" name="remember">
                    Ingat saya
                </label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-decoration-none fw-semibold">Lupa password?</a>
                @endif
            </div>

            <x-ui.button class="w-100">Login</x-ui.button>
        </form>
    </x-ui.card>
</x-guest-layout>
