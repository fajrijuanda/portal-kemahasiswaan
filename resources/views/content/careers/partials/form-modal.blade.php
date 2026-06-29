<div class="modal fade ubp-record-modal" id="{{ $id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <form class="modal-content" method="POST" action="{{ $action }}">
            @csrf
            @if($method)
                @method($method)
            @endif
            <div class="modal-header"><div><span class="ubp-auth-eyebrow">Karir</span><h5 class="modal-title">{{ $title }}</h5></div><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label">Tipe</label><select name="type" class="form-select ubp-control">@foreach(['Loker', 'Job Fair'] as $type)<option value="{{ $type }}" @selected(old('type', $record?->type ?? 'Loker') === $type)>{{ $type }}</option>@endforeach</select></div>
                    <div class="col-md-6"><label class="form-label">Status</label><select name="status" class="form-select ubp-control">@foreach(['Draft', 'Published'] as $status)<option value="{{ $status }}" @selected(old('status', $record?->status ?? 'Draft') === $status)>{{ $status }}</option>@endforeach</select></div>
                    <div class="col-12"><label class="form-label">Judul</label><input name="title" class="form-control ubp-control" value="{{ old('title', $record?->title) }}" required></div>
                    <div class="col-md-6"><label class="form-label">Perusahaan/Penyelenggara</label><input name="company" class="form-control ubp-control" value="{{ old('company', $record?->company) }}"></div>
                    <div class="col-md-6"><label class="form-label">Lokasi</label><input name="location" class="form-control ubp-control" value="{{ old('location', $record?->location) }}"></div>
                    <div class="col-md-6"><label class="form-label">Deadline</label><input type="date" name="deadline" class="form-control ubp-control" value="{{ old('deadline', $record?->deadline?->format('Y-m-d')) }}"></div>
                    <div class="col-md-6"><label class="form-label">Link Eksternal</label><input type="url" name="external_url" class="form-control ubp-control" value="{{ old('external_url', $record?->external_url) }}"></div>
                    <div class="col-12"><label class="form-label">Deskripsi</label><textarea name="content" class="form-control ubp-control" rows="7">{{ old('content', $record?->content) }}</textarea></div>
                </div>
            </div>
            <div class="modal-footer"><button type="button" class="ubp-table-action" data-bs-dismiss="modal">Batal</button><button class="ubp-btn ubp-btn-primary">Simpan</button></div>
        </form>
    </div>
</div>
