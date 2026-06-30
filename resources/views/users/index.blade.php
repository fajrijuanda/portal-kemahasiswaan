<x-app-layout>
    <x-slot name="header">
        <section class="ubp-section-hero">
            <span class="ubp-auth-eyebrow">Management</span>
            <h1 class="ubp-title">Management User</h1>
            <p class="ubp-subtitle">Khusus super user untuk mengelola akun, role, prodi, dan password pengguna portal.</p>
        </section>
    </x-slot>

    <div class="ubp-stat-grid">
        <article class="ubp-stat-card tone-blue">
            <div><small>Total Akun</small><strong>{{ number_format($totalUsers) }}</strong><em>Pengguna portal</em></div>
            <span class="ubp-stat-icon"><x-ui.app-icon name="user" /></span>
        </article>
        <article class="ubp-stat-card tone-violet">
            <div><small>Super User</small><strong>{{ number_format($roleCounts['super user'] ?? 0) }}</strong><em>Akses penuh</em></div>
            <span class="ubp-stat-icon"><x-ui.app-icon name="access" /></span>
        </article>
        <article class="ubp-stat-card tone-orange">
            <div><small>Admin</small><strong>{{ number_format($roleCounts['admin'] ?? 0) }}</strong><em>Operator data</em></div>
            <span class="ubp-stat-icon"><x-ui.app-icon name="grid" /></span>
        </article>
        <article class="ubp-stat-card tone-emerald">
            <div><small>Kaprodi</small><strong>{{ number_format($roleCounts['kaprodi'] ?? 0) }}</strong><em>Scope prodi</em></div>
            <span class="ubp-stat-icon"><x-ui.app-icon name="prodi" /></span>
        </article>
    </div>

    <x-ui.table-shell class="ubp-table-shell-omnia" title="Daftar User" subtitle="Kelola akun pengguna, role, dan prodi melalui tabel berikut.">
        <x-slot:toolbar>
            <button class="ubp-btn ubp-btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#userCreateModal">+ Tambah User</button>
        </x-slot:toolbar>

        <x-slot:controls>
            <form method="GET" action="{{ route('users.index') }}" class="ubp-record-table-controls">
                <div class="ubp-record-search">
                    <span><x-ui.app-icon name="grid" /></span>
                    <input name="q" value="{{ $keyword }}" placeholder="Cari nama/email..." autocomplete="off">
                </div>
                <select class="form-select ubp-control" name="role">
                    <option value="">Semua role</option>
                    @foreach($roles as $role)
                        <option value="{{ $role }}" @selected($roleFilter === $role)>{{ ucfirst($role) }}</option>
                    @endforeach
                </select>
                <select name="limit" class="form-select ubp-control" onchange="this.form.submit()">
                    <option value="10" @selected(request('limit', 10) == 10)>10 / hal</option>
                    <option value="25" @selected(request('limit') == 25)>25 / hal</option>
                    <option value="50" @selected(request('limit') == 50)>50 / hal</option>
                    <option value="100" @selected(request('limit') == 100)>100 / hal</option>
                </select>
                <button class="ubp-table-action ubp-table-action-primary" type="submit">Filter</button>
                @if($keyword || $roleFilter)
                    <a class="ubp-table-action" href="{{ route('users.index') }}">Reset</a>
                @endif
            </form>
        </x-slot:controls>

        <table class="table align-middle ubp-table ubp-data-table">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Prodi</th>
                    <th>NIM</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td data-label="Nama"><span class="ubp-table-primary">{{ $user->name }}</span></td>
                        <td data-label="Email">{{ $user->email }}</td>
                        <td data-label="Role"><x-ui.role-badge :role="$user->roles->first()?->name ?? 'user'" /></td>
                        <td data-label="Prodi">{{ $user->prodi?->nama ?? '-' }}</td>
                        <td data-label="NIM">{{ $user->nim ?: '-' }}</td>
                        <td class="text-end" data-label="Aksi">
                            <div class="ubp-table-action-group justify-content-end">
                                <button class="ubp-table-action ubp-table-action-primary" type="button" data-bs-toggle="modal" data-bs-target="#userEditModal{{ $user->id }}">Edit</button>
                                @if(auth()->id() !== $user->id)
                                    <button class="ubp-table-action ubp-table-action-danger" type="button" onclick="triggerDeleteModal(`{{ route('users.destroy', $user) }}`, `Hapus akun pengguna ini secara permanen?`)">Hapus</button>
                                @else
                                    <span class="ubp-role-pill">Akun aktif</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <x-ui.table-empty-row colspan="6" title="Belum ada user" message="User baru akan muncul setelah ditambahkan." />
                @endforelse
            </tbody>
        </table>
        <x-slot:pagination>
            <div class="ubp-pagination-summary">Menampilkan {{ $users->firstItem() ?? 0 }}-{{ $users->lastItem() ?? 0 }} dari {{ number_format($users->total()) }} data</div>
            <div class="ubp-pagination-controls">
                @if($users->onFirstPage())
                    <span class="ubp-page-button disabled">Prev</span>
                @else
                    <a class="ubp-page-button" href="{{ $users->previousPageUrl() }}">Prev</a>
                @endif
                <span class="ubp-page-current">{{ $users->currentPage() }}/{{ max($users->lastPage(), 1) }}</span>
                @if($users->hasMorePages())
                    <a class="ubp-page-button" href="{{ $users->nextPageUrl() }}">Next</a>
                @else
                    <span class="ubp-page-button disabled">Next</span>
                @endif
            </div>
        </x-slot:pagination>
    </x-ui.table-shell>

    {{-- Create Modal --}}
    <div class="modal fade ubp-record-modal" id="userCreateModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
            <form class="modal-content" method="POST" action="{{ route('users.store') }}">
                @csrf
                <div class="modal-header">
                    <div><span class="ubp-auth-eyebrow">Tambah data</span><h5 class="modal-title">Tambah User</h5></div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <x-ui.form-field name="name" label="Nama" required />
                        </div>
                        <div class="col-md-6">
                            <x-ui.form-field name="email" label="Email" type="email" required />
                        </div>
                        <div class="col-md-6">
                            <x-ui.form-field name="password" label="Password" type="password" required />
                        </div>
                        <div class="col-md-6">
                            <x-ui.select-field name="role" label="Role" :options="$roles->mapWithKeys(fn ($role) => [$role => ucfirst($role)])->all()" required />
                        </div>
                        <div class="col-md-6">
                            <x-ui.select-field name="prodi_id" label="Prodi" :options="$prodis->pluck('nama', 'id')->all()" placeholder="-" />
                        </div>
                        <div class="col-md-6">
                            <x-ui.form-field name="nim" label="NIM" />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="ubp-table-action" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="ubp-btn ubp-btn-primary">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Edit Modals --}}
    @foreach($users as $user)
        <div class="modal fade ubp-record-modal" id="userEditModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
                <form class="modal-content" method="POST" action="{{ route('users.update', $user) }}">
                    @csrf @method('PUT')
                    <div class="modal-header">
                        <div><span class="ubp-auth-eyebrow">Edit data</span><h5 class="modal-title">Edit User</h5></div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <x-ui.form-field name="name" label="Nama" :value="$user->name" required />
                            </div>
                            <div class="col-md-6">
                                <x-ui.form-field name="email" label="Email" type="email" :value="$user->email" required />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="edit-{{ $user->id }}-role">Role</label>
                                <select id="edit-{{ $user->id }}-role" name="role" class="form-select ubp-control">
                                    @foreach($roles as $role)
                                        <option value="{{ $role }}" @selected($user->hasRole($role))>{{ ucfirst($role) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="edit-{{ $user->id }}-prodi">Prodi</label>
                                <select id="edit-{{ $user->id }}-prodi" name="prodi_id" class="form-select ubp-control">
                                    <option value="">-</option>
                                    @foreach($prodis as $prodi)
                                        <option value="{{ $prodi->id }}" @selected($user->prodi_id === $prodi->id)>{{ $prodi->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <x-ui.form-field name="nim" label="NIM" :value="$user->nim" />
                            </div>
                            <div class="col-md-6">
                                <x-ui.form-field name="password" label="Password Baru" type="password" placeholder="Kosongkan jika tetap" />
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="ubp-table-action" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="ubp-btn ubp-btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    @endforeach
</x-app-layout>
