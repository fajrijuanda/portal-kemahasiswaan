<x-app-layout>
    @unless(isset($sectionShell))
        <x-slot name="header">
            <div>
                <h1 class="ubp-title">Karir</h1>
                <p class="ubp-subtitle">Kelola lowongan kerja dan job fair untuk halaman publik.</p>
            </div>
        </x-slot>
    @endunless

    @isset($sectionShell)
        <x-ui.section-shell :eyebrow="$sectionShell['eyebrow']" :title="$sectionShell['title']" :subtitle="$sectionShell['subtitle']" :items="$sectionShell['items']" :stats="$sectionShell['stats']">
    @endisset

    <x-ui.table-shell class="ubp-table-shell-omnia" title="Loker dan Job Fair" subtitle="Konten Published akan tampil di landing publik.">
        <x-slot:toolbar><button class="ubp-btn ubp-btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#careerCreateModal">+ Tambah Konten</button></x-slot:toolbar>
        <table class="table align-middle ubp-table ubp-data-table">
            <thead><tr><th>Judul</th><th>Tipe</th><th>Perusahaan</th><th>Deadline</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
            <tbody>
                @forelse($records as $record)
                    <tr>
                        <td><span class="ubp-table-primary">{{ $record->title }}</span><span class="ubp-table-muted">{{ $record->location ?: '-' }}</span></td>
                        <td>{{ $record->type }}</td>
                        <td>{{ $record->company ?: '-' }}</td>
                        <td>{{ $record->deadline?->format('d M Y') ?? '-' }}</td>
                        <td><x-ui.status-badge :status="$record->status" /></td>
                        <td class="text-end">
                            <button class="ubp-table-action ubp-table-action-primary" type="button" data-bs-toggle="modal" data-bs-target="#careerEditModal{{ $record->id }}">Edit</button>
                            <button class="ubp-table-action ubp-table-action-danger" type="button" onclick="triggerDeleteModal(`{{ route('careers.destroy', $record) }}`, `Hapus konten karir ini?`)">Hapus</button>
                        </td>
                    </tr>
                @empty
                    <x-ui.table-empty-row :colspan="6" title="Belum ada konten karir" message="Tambahkan lowongan atau job fair pertama." />
                @endforelse
            </tbody>
        </table>
        <x-slot:pagination>{{ $records->links() }}</x-slot:pagination>
    </x-ui.table-shell>

    @include('content.careers.partials.form-modal', ['id' => 'careerCreateModal', 'title' => 'Tambah Konten Karir', 'action' => route('careers.store'), 'method' => null, 'record' => null])
    @foreach($records as $record)
        @include('content.careers.partials.form-modal', ['id' => 'careerEditModal'.$record->id, 'title' => 'Edit Konten Karir', 'action' => route('careers.update', $record), 'method' => 'PUT', 'record' => $record])
    @endforeach

    @isset($sectionShell)
        </x-ui.section-shell>
    @endisset
</x-app-layout>
