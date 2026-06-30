<x-app-layout>
    @php
        $editorContent = old('content', $record?->content ?: '<p></p>');
        $editorContent = strip_tags($editorContent, '<p><br><strong><b><em><i><u><s><h2><h3><h4><blockquote><ol><ul><li><a><hr><pre><code><div><span>');
        $editorContent = preg_replace('/\s(on\w+|style)\s*=\s*(".*?"|\'.*?\'|[^\s>]+)/i', '', $editorContent);
        $editorContent = preg_replace('/href\s*=\s*("|\')\s*javascript:[^"\']*("|\')/i', 'href="#"', $editorContent);
    @endphp

    <x-slot name="header">
        <h1 class="ubp-title">{{ $title }}</h1>
        <p class="ubp-subtitle">{{ $subtitle }}</p>
    </x-slot>

    <form method="POST" enctype="multipart/form-data" action="{{ $action }}" class="ubp-editor-page" data-rich-editor-form>
        @csrf
        @if($method)
            @method($method)
        @endif

        <section class="ubp-editor-main">
            <div class="ubp-editor-titlebar">
                <div>
                    <span class="ubp-auth-eyebrow">Konten Berita</span>
                    <h2>{{ $record ? 'Perbarui naskah berita' : 'Tulis naskah berita baru' }}</h2>
                    <p>Susun judul, ringkasan, dan isi berita dalam satu halaman editorial.</p>
                </div>
                <a class="ubp-table-action" href="{{ route('publications.index', 'berita') }}">Kembali</a>
            </div>

            <div class="ubp-editor-fields">
                <div class="ubp-editor-field">
                    <label class="form-label" for="title">Judul</label>
                    <input id="title" name="title" class="form-control ubp-control ubp-editor-title-input" value="{{ old('title', $record?->title) }}" placeholder="Tulis judul berita..." required>
                    @error('title')<div class="ubp-form-error">{{ $message }}</div>@enderror
                </div>

                <div class="ubp-editor-field">
                    <label class="form-label" for="excerpt">Ringkasan</label>
                    <textarea id="excerpt" name="excerpt" class="form-control ubp-control" rows="3" placeholder="Ringkasan singkat yang tampil di kartu berita...">{{ old('excerpt', $record?->excerpt) }}</textarea>
                    @error('excerpt')<div class="ubp-form-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="ubp-doc-editor" data-rich-editor>
                <div class="ubp-doc-toolbar" role="toolbar" aria-label="Toolbar editor berita">
                    <select class="ubp-doc-select" data-editor-block aria-label="Format paragraf">
                        <option value="P">Paragraf</option>
                        <option value="H2">Heading 2</option>
                        <option value="H3">Heading 3</option>
                        <option value="H4">Heading 4</option>
                        <option value="PRE">Kode</option>
                    </select>
                    <button type="button" title="Bold" data-editor-command="bold"><strong>B</strong></button>
                    <button type="button" title="Italic" data-editor-command="italic"><em>I</em></button>
                    <button type="button" title="Underline" data-editor-command="underline"><u>U</u></button>
                    <button type="button" title="Strikethrough" data-editor-command="strikeThrough"><s>S</s></button>
                    <span class="ubp-doc-divider"></span>
                    <button type="button" title="Bullet list" data-editor-command="insertUnorderedList">Bullet</button>
                    <button type="button" title="Numbered list" data-editor-command="insertOrderedList">1. List</button>
                    <button type="button" title="Quote" data-editor-command="formatBlock" data-editor-value="BLOCKQUOTE">Quote</button>
                    <span class="ubp-doc-divider"></span>
                    <button type="button" title="Align left" data-editor-command="justifyLeft">Left</button>
                    <button type="button" title="Align center" data-editor-command="justifyCenter">Center</button>
                    <button type="button" title="Align right" data-editor-command="justifyRight">Right</button>
                    <span class="ubp-doc-divider"></span>
                    <button type="button" title="Link" data-editor-link>Link</button>
                    <button type="button" title="Horizontal line" data-editor-command="insertHorizontalRule">Line</button>
                    <button type="button" title="Clear format" data-editor-command="removeFormat">Clear</button>
                    <button type="button" title="Undo" data-editor-command="undo">Undo</button>
                    <button type="button" title="Redo" data-editor-command="redo">Redo</button>
                </div>

                <div class="ubp-doc-paper" contenteditable="true" data-editor-area aria-label="Isi berita">{!! $editorContent !!}</div>
                <textarea name="content" class="d-none" data-editor-input>{{ $editorContent }}</textarea>
            </div>
            @error('content')<div class="ubp-form-error">{{ $message }}</div>@enderror
        </section>

        <aside class="ubp-editor-side">
            <section class="ubp-editor-panel">
                <span class="ubp-auth-eyebrow">Publikasi</span>
                <h3>Status Berita</h3>
                <label class="form-label" for="status">Status</label>
                <select id="status" name="status" class="form-select ubp-control">
                    @foreach(['Draft', 'Published'] as $status)
                        <option value="{{ $status }}" @selected(old('status', $record?->status ?? 'Draft') === $status)>{{ $status }}</option>
                    @endforeach
                </select>
                @error('status')<div class="ubp-form-error">{{ $message }}</div>@enderror

                <div class="ubp-editor-meta">
                    <strong>Slug</strong>
                    <span>{{ $record?->slug ?? 'Otomatis dibuat dari judul' }}</span>
                </div>
                <div class="ubp-editor-meta">
                    <strong>Published</strong>
                    <span>{{ $record?->published_at?->format('d M Y H:i') ?? 'Belum published' }}</span>
                </div>
            </section>

            <section class="ubp-editor-panel">
                <span class="ubp-auth-eyebrow">Media</span>
                <h3>Cover Berita</h3>
                @if($record?->cover_path)
                    <img class="ubp-editor-cover-preview" src="{{ asset('storage/'.$record->cover_path) }}" alt="Cover {{ $record->title }}">
                @endif
                <input type="file" name="cover_path" class="form-control ubp-control" accept="image/*">
                <small class="ubp-editor-help">Format gambar, maksimal 2 MB. Kosongkan jika cover lama tetap dipakai.</small>
                @error('cover_path')<div class="ubp-form-error">{{ $message }}</div>@enderror
            </section>

            <section class="ubp-editor-panel">
                <span class="ubp-auth-eyebrow">Checklist</span>
                <h3>Sebelum Simpan</h3>
                <ul class="ubp-editor-checklist">
                    <li>Judul jelas dan tidak terlalu panjang.</li>
                    <li>Ringkasan cukup untuk kartu berita.</li>
                    <li>Konten memakai heading/list agar mudah dibaca.</li>
                    <li>Status Draft jika belum siap tampil publik.</li>
                </ul>
            </section>

            <div class="ubp-editor-submit">
                <button type="submit" class="ubp-btn ubp-btn-primary">Simpan Berita</button>
                <a class="ubp-table-action" href="{{ route('publications.index', 'berita') }}">Batal</a>
            </div>
        </aside>
    </form>
</x-app-layout>
