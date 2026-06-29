<div class="modal fade ubp-record-modal" id="{{ $id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <form class="modal-content" method="POST" enctype="multipart/form-data" action="{{ $action }}">
            @csrf
            @if($method)
                @method($method)
            @endif
            <div class="modal-header"><div><span class="ubp-auth-eyebrow">Konten Publik</span><h5 class="modal-title">{{ $title }}</h5></div><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-12"><label class="form-label">Judul</label><input name="title" class="form-control ubp-control" value="{{ old('title', $record?->title) }}" required></div>
                    <div class="col-12"><label class="form-label">Ringkasan</label><textarea name="excerpt" class="form-control ubp-control" rows="2">{{ old('excerpt', $record?->excerpt) }}</textarea></div>
                    <div class="col-12"><label class="form-label">Konten</label><textarea name="content" class="form-control ubp-control" rows="8">{{ old('content', $record?->content) }}</textarea></div>
                    <div class="col-md-6"><label class="form-label">Cover</label><input type="file" name="cover_path" class="form-control ubp-control" accept="image/*">@if($record?->cover_path)<a class="small d-inline-block mt-2" href="{{ asset('storage/'.$record->cover_path) }}" target="_blank">Lihat cover</a>@endif</div>
                    <div class="col-md-6"><label class="form-label">Status</label><select name="status" class="form-select ubp-control">@foreach(['Draft', 'Published'] as $status)<option value="{{ $status }}" @selected(old('status', $record?->status ?? 'Draft') === $status)>{{ $status }}</option>@endforeach</select></div>
                </div>
            </div>
            <div class="modal-footer"><button type="button" class="ubp-table-action" data-bs-dismiss="modal">Batal</button><button class="ubp-btn ubp-btn-primary">Simpan</button></div>
        </form>
    </div>
</div>
