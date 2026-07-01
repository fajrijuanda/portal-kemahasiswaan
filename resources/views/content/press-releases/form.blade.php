<x-app-layout>
    @php
        $editorContent = old('content', $record?->content ?: '<p></p>');
        $editorContent = \App\Support\RichTextSanitizer::clean($editorContent);
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
                        <option value="BLOCKQUOTE">Kutipan</option>
                        <option value="PRE">Kode</option>
                    </select>
                    <button type="button" title="Bold" aria-label="Bold" aria-pressed="false" data-editor-command="bold" data-editor-state="bold"><span class="ubp-doc-text-icon"><strong>B</strong></span></button>
                    <button type="button" title="Italic" aria-label="Italic" aria-pressed="false" data-editor-command="italic" data-editor-state="italic"><span class="ubp-doc-text-icon"><em>I</em></span></button>
                    <button type="button" title="Underline" aria-label="Underline" aria-pressed="false" data-editor-command="underline" data-editor-state="underline"><span class="ubp-doc-text-icon"><u>U</u></span></button>
                    <button type="button" title="Strikethrough" aria-label="Strikethrough" aria-pressed="false" data-editor-command="strikeThrough" data-editor-state="strikeThrough"><span class="ubp-doc-text-icon"><s>S</s></span></button>
                    <span class="ubp-doc-divider"></span>
                    <button type="button" title="Bullet list" aria-label="Bullet list" aria-pressed="false" data-editor-command="insertUnorderedList" data-editor-state="insertUnorderedList"><x-ui.app-icon name="bullet-list" /></button>
                    <button type="button" title="Numbered list" aria-label="Numbered list" aria-pressed="false" data-editor-command="insertOrderedList" data-editor-state="insertOrderedList"><x-ui.app-icon name="ordered-list" /></button>
                    <button type="button" title="Quote" aria-label="Quote" aria-pressed="false" data-editor-command="formatBlock" data-editor-value="BLOCKQUOTE" data-editor-state="formatBlock"><x-ui.app-icon name="quote" /></button>
                    <span class="ubp-doc-divider"></span>
                    <button type="button" title="Align left" aria-label="Align left" aria-pressed="false" data-editor-command="justifyLeft" data-editor-state="justifyLeft"><x-ui.app-icon name="align-left" /></button>
                    <button type="button" title="Align center" aria-label="Align center" aria-pressed="false" data-editor-command="justifyCenter" data-editor-state="justifyCenter"><x-ui.app-icon name="align-center" /></button>
                    <button type="button" title="Align right" aria-label="Align right" aria-pressed="false" data-editor-command="justifyRight" data-editor-state="justifyRight"><x-ui.app-icon name="align-right" /></button>
                    <button type="button" title="Justify" aria-label="Justify" aria-pressed="false" data-editor-command="justifyFull" data-editor-state="justifyFull"><x-ui.app-icon name="align-justify" /></button>
                    <span class="ubp-doc-divider"></span>
                    <button type="button" title="Link" aria-label="Link" data-editor-link><x-ui.app-icon name="link" /></button>
                    <button type="button" title="Insert image" aria-label="Insert image" data-editor-image-trigger><x-ui.app-icon name="image" /></button>
                    <button type="button" title="Horizontal line" aria-label="Horizontal line" data-editor-command="insertHorizontalRule"><x-ui.app-icon name="horizontal-rule" /></button>
                    <button type="button" title="Clear format" aria-label="Clear format" data-editor-command="removeFormat"><x-ui.app-icon name="clear-format" /></button>
                    <span class="ubp-doc-divider"></span>
                    <button type="button" title="Undo" aria-label="Undo" data-editor-command="undo"><x-ui.app-icon name="undo" /></button>
                    <button type="button" title="Redo" aria-label="Redo" data-editor-command="redo"><x-ui.app-icon name="redo" /></button>
                    <input type="file" class="ubp-doc-hidden-file" accept="image/png,image/jpeg,image/jpg,image/gif,image/webp" data-editor-image-input>
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
