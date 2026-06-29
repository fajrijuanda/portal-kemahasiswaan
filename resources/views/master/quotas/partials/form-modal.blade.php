<div class="modal fade ubp-record-modal" id="{{ $id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" method="POST" action="{{ $action }}">
            @csrf
            @if($method)
                @method($method)
            @endif
            <div class="modal-header"><div><span class="ubp-auth-eyebrow">Kuota</span><h5 class="modal-title">{{ $title }}</h5></div><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <label class="form-label">Semester</label>
                <select name="semester_id" class="form-select ubp-control mb-3" required>
                    @foreach($semesters as $semester)
                        <option value="{{ $semester->id }}" @selected(old('semester_id', $quota?->semester_id) == $semester->id)>{{ $semester->nama }}</option>
                    @endforeach
                </select>
                <label class="form-label">Prodi</label>
                <select name="prodi_id" class="form-select ubp-control mb-3" required>
                    @foreach($prodis as $prodi)
                        <option value="{{ $prodi->id }}" @selected(old('prodi_id', $quota?->prodi_id) == $prodi->id)>{{ $prodi->nama }}</option>
                    @endforeach
                </select>
                <label class="form-label">Slot Prestasi</label>
                <input name="slot_prestasi" type="number" min="0" class="form-control ubp-control" value="{{ old('slot_prestasi', $quota?->slot_prestasi ?? 0) }}" required>
            </div>
            <div class="modal-footer"><button type="button" class="ubp-table-action" data-bs-dismiss="modal">Batal</button><button class="ubp-btn ubp-btn-primary">Simpan</button></div>
        </form>
    </div>
</div>
