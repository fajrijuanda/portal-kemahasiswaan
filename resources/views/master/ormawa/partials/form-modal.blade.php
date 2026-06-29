<div class="modal fade ubp-record-modal" id="{{ $id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <form class="modal-content" method="POST" action="{{ $action }}">
            @csrf
            @if($method)
                @method($method)
            @endif
            <div class="modal-header"><div><span class="ubp-auth-eyebrow">Ormawa</span><h5 class="modal-title">{{ $title }}</h5></div><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label">Nama Ormawa</label><input name="nama" class="form-control ubp-control" value="{{ old('nama', $ormawa?->nama) }}" required></div>
                    <div class="col-md-6"><label class="form-label">Jenis</label><input name="jenis" class="form-control ubp-control" value="{{ old('jenis', $ormawa?->jenis) }}"></div>
                    <div class="col-md-6"><label class="form-label">Pembina/PIC</label><input name="pembina" class="form-control ubp-control" value="{{ old('pembina', $ormawa?->pembina) }}"></div>
                    <div class="col-md-6"><label class="form-label">Kontak</label><input name="kontak" class="form-control ubp-control" value="{{ old('kontak', $ormawa?->kontak) }}"></div>
                    <div class="col-md-6">
                        <label class="form-label">Akun Login Ormawa</label>
                        <select name="user_id" class="form-select ubp-control">
                            <option value="">Belum ditautkan</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" @selected(old('user_id', $ormawa?->user_id) == $user->id)>{{ $user->name }} - {{ $user->email }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select ubp-control" required>
                            @foreach(['Aktif', 'Nonaktif'] as $status)
                                <option value="{{ $status }}" @selected(old('status', $ormawa?->status ?? 'Aktif') === $status)>{{ $status }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12"><label class="form-label">Deskripsi</label><textarea name="deskripsi" class="form-control ubp-control" rows="4">{{ old('deskripsi', $ormawa?->deskripsi) }}</textarea></div>
                </div>
            </div>
            <div class="modal-footer"><button type="button" class="ubp-table-action" data-bs-dismiss="modal">Batal</button><button class="ubp-btn ubp-btn-primary">Simpan</button></div>
        </form>
    </div>
</div>
