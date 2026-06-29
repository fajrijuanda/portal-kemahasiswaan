<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="ubp-title">Press Release</h1>
            <p class="ubp-subtitle">Kabag dapat membuat dan mempublikasikan berita untuk halaman publik.</p>
        </div>
    </x-slot>

    @isset($sectionShell)
        <x-ui.section-shell :eyebrow="$sectionShell['eyebrow']" :title="$sectionShell['title']" :subtitle="$sectionShell['subtitle']" :items="$sectionShell['items']" :stats="$sectionShell['stats']">
    @endisset

    <x-ui.table-shell class="ubp-table-shell-omnia" title="Daftar Press Release" subtitle="Status Published akan tampil di halaman publik.">
        <x-slot:toolbar><button class="ubp-btn ubp-btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#pressCreateModal">+ Tambah Press Release</button></x-slot:toolbar>
        <table class="table align-middle ubp-table ubp-data-table">
            <thead><tr><th>Judul</th><th>Status</th><th>Published</th><th>Pembuat</th><th class="text-end">Aksi</th></tr></thead>
            <tbody>
                @forelse($records as $record)
                    <tr>
                        <td><span class="ubp-table-primary">{{ $record->title }}</span><span class="ubp-table-muted">{{ \Illuminate\Support\Str::limit($record->excerpt, 70) }}</span></td>
                        <td><x-ui.status-badge :status="$record->status" /></td>
                        <td>{{ $record->published_at?->format('d M Y') ?? '-' }}</td>
                        <td>{{ $record->creator?->name ?? '-' }}</td>
                        <td class="text-end">
                            <button class="ubp-table-action ubp-table-action-primary" type="button" data-bs-toggle="modal" data-bs-target="#pressEditModal{{ $record->id }}">Edit</button>
                            <button class="ubp-table-action ubp-table-action-danger" type="button" onclick="triggerDeleteModal(`{{ route('press-releases.destroy', $record) }}`, `Hapus press release ini?`)">Hapus</button>
                        </td>
                    </tr>
                @empty
                    <x-ui.table-empty-row :colspan="5" title="Belum ada press release" message="Tambahkan konten pertama." />
                @endforelse
            </tbody>
        </table>
        <x-slot:pagination>{{ $records->links() }}</x-slot:pagination>
    </x-ui.table-shell>

    @include('content.press-releases.partials.form-modal', ['id' => 'pressCreateModal', 'title' => 'Tambah Press Release', 'action' => route('press-releases.store'), 'method' => null, 'record' => null])
    @foreach($records as $record)
        @include('content.press-releases.partials.form-modal', ['id' => 'pressEditModal'.$record->id, 'title' => 'Edit Press Release', 'action' => route('press-releases.update', $record), 'method' => 'PUT', 'record' => $record])
    @endforeach

    @isset($sectionShell)
        </x-ui.section-shell>
    @endisset
</x-app-layout>
