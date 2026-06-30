<x-app-layout>
    <x-slot name="header">
        <section class="ubp-section-hero">
            <span class="ubp-auth-eyebrow">Pengaturan</span>
            <h1 class="ubp-title">Profil Akun</h1>
            <p class="ubp-subtitle">Kelola identitas, email, password, dan keamanan akun portal.</p>
        </section>
    </x-slot>

    <div class="ubp-stat-grid">
        <article class="ubp-stat-card tone-blue">
            <div><small>Nama Akun</small><strong>{{ $user->name }}</strong><em>{{ $user->email }}</em></div>
            <span class="ubp-stat-icon"><x-ui.app-icon name="user" /></span>
        </article>
        <article class="ubp-stat-card tone-violet">
            <div><small>Role</small><strong>{{ \Illuminate\Support\Str::headline($user->roles->first()?->name ?? 'User') }}</strong><em>Akses portal</em></div>
            <span class="ubp-stat-icon"><x-ui.app-icon name="access" /></span>
        </article>
        <article class="ubp-stat-card tone-emerald">
            <div><small>Prodi</small><strong>{{ $user->prodi?->nama ?? '-' }}</strong><em>Scope pengguna</em></div>
            <span class="ubp-stat-icon"><x-ui.app-icon name="prodi" /></span>
        </article>
        <article class="ubp-stat-card tone-orange">
            <div><small>NIM</small><strong>{{ $user->nim ?: '-' }}</strong><em>Identitas mahasiswa</em></div>
            <span class="ubp-stat-icon"><x-ui.app-icon name="semester" /></span>
        </article>
    </div>

    <div class="ubp-profile-grid">
        <x-ui.table-shell class="ubp-table-shell-omnia" title="Informasi Profil" subtitle="Perbarui nama dan email akun portal.">
            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <form id="send-verification" method="POST" action="{{ route('verification.send') }}">@csrf</form>
            @endif

            <form method="POST" action="{{ route('profile.update') }}" class="ubp-profile-form">
                @csrf
                @method('patch')

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="name">Nama</label>
                        <input id="name" name="name" class="form-control ubp-control" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
                        @error('name')<div class="ubp-form-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="email">Email</label>
                        <input id="email" name="email" type="email" class="form-control ubp-control" value="{{ old('email', $user->email) }}" required autocomplete="username">
                        @error('email')<div class="ubp-form-error">{{ $message }}</div>@enderror
                    </div>
                </div>

                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <div class="ubp-profile-note">
                        Email belum terverifikasi.
                        <button form="send-verification" type="submit">Kirim ulang verifikasi</button>
                    </div>
                @endif

                <div class="ubp-profile-actions">
                    <button type="submit" class="ubp-btn ubp-btn-primary">Simpan Profil</button>
                    @if (session('status') === 'profile-updated')
                        <span class="ubp-role-pill">Profil tersimpan</span>
                    @endif
                </div>
            </form>
        </x-ui.table-shell>

        <x-ui.table-shell class="ubp-table-shell-omnia" title="Update Password" subtitle="Gunakan password kuat untuk menjaga keamanan akun.">
            <form method="POST" action="{{ route('password.update') }}" class="ubp-profile-form">
                @csrf
                @method('put')

                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label" for="update_password_current_password">Password Saat Ini</label>
                        <input id="update_password_current_password" name="current_password" type="password" class="form-control ubp-control" autocomplete="current-password">
                        @foreach($errors->updatePassword->get('current_password') as $message)<div class="ubp-form-error">{{ $message }}</div>@endforeach
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="update_password_password">Password Baru</label>
                        <input id="update_password_password" name="password" type="password" class="form-control ubp-control" autocomplete="new-password">
                        @foreach($errors->updatePassword->get('password') as $message)<div class="ubp-form-error">{{ $message }}</div>@endforeach
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="update_password_password_confirmation">Konfirmasi Password</label>
                        <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="form-control ubp-control" autocomplete="new-password">
                        @foreach($errors->updatePassword->get('password_confirmation') as $message)<div class="ubp-form-error">{{ $message }}</div>@endforeach
                    </div>
                </div>

                <div class="ubp-profile-actions">
                    <button type="submit" class="ubp-btn ubp-btn-primary">Simpan Password</button>
                    @if (session('status') === 'password-updated')
                        <span class="ubp-role-pill">Password tersimpan</span>
                    @endif
                </div>
            </form>
        </x-ui.table-shell>
    </div>

    <x-ui.table-shell class="ubp-table-shell-omnia ubp-profile-danger" title="Hapus Akun" subtitle="Tindakan ini akan menghapus akun secara permanen.">
        <div class="ubp-profile-danger-row">
            <div>
                <strong>Area berisiko</strong>
                <span>Pastikan akun ini memang tidak lagi digunakan sebelum melanjutkan.</span>
            </div>
            <button class="ubp-table-action ubp-table-action-danger" type="button" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">Hapus Akun</button>
        </div>
    </x-ui.table-shell>

    <div class="modal fade ubp-record-modal" id="deleteAccountModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form method="POST" action="{{ route('profile.destroy') }}" class="modal-content">
                @csrf
                @method('delete')
                <div class="modal-header">
                    <div><span class="ubp-auth-eyebrow">Konfirmasi</span><h5 class="modal-title">Hapus Akun</h5></div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="ubp-subtitle mb-3">Masukkan password untuk menghapus akun ini secara permanen.</p>
                    <label class="form-label" for="delete-password">Password</label>
                    <input id="delete-password" name="password" type="password" class="form-control ubp-control" required>
                    @foreach($errors->userDeletion->get('password') as $message)<div class="ubp-form-error">{{ $message }}</div>@endforeach
                </div>
                <div class="modal-footer">
                    <button type="button" class="ubp-table-action" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="ubp-table-action ubp-table-action-danger">Ya, Hapus Akun</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
