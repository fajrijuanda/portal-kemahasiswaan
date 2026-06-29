<x-app-layout>
    @unless(isset($sectionShell))
        <x-slot name="header">
            <div>
                <h1 class="ubp-title">{{ $title }}</h1>
                <p class="ubp-subtitle">{{ $subtitle }}</p>
            </div>
        </x-slot>
    @endunless

    <x-ui.section-shell
        :eyebrow="$sectionShell['eyebrow']"
        :title="$sectionShell['title']"
        :subtitle="$sectionShell['subtitle']"
        :items="$sectionShell['items']"
        :stats="$sectionShell['stats']"
    >
        <x-ui.table-shell class="ubp-table-shell-omnia" :title="$title" :subtitle="$subtitle">
            <table class="table align-middle ubp-table ubp-data-table">
                @if($section === 'proposal')
                    <thead><tr><th>Proposal</th><th>Ormawa</th><th>Semester</th><th>Tanggal</th><th>Status</th><th>File</th></tr></thead>
                    <tbody>
                        @forelse($records as $record)
                            <tr>
                                <td><span class="ubp-table-primary">{{ $record->judul }}</span><span class="ubp-table-muted">{{ \Illuminate\Support\Str::limit($record->deskripsi, 70) }}</span></td>
                                <td>{{ $record->ormawa?->nama ?? '-' }}</td>
                                <td>{{ $record->semester?->nama ?? '-' }}</td>
                                <td>{{ $record->tanggal?->format('d M Y') ?? '-' }}</td>
                                <td><x-ui.status-badge :status="$record->status" /></td>
                                <td>@if($record->proposal_path)<a class="ubp-table-link" href="{{ asset('storage/'.$record->proposal_path) }}" target="_blank">Lihat file</a>@else - @endif</td>
                            </tr>
                        @empty
                            <x-ui.table-empty-row :colspan="6" title="Belum ada proposal" message="Proposal dari akun Ormawa akan muncul di sini." />
                        @endforelse
                    </tbody>
                @else
                    <thead><tr><th>Kegiatan</th><th>Ormawa</th><th>Jenis</th><th>Nominal</th><th>Status</th><th>Link</th></tr></thead>
                    <tbody>
                        @forelse($records as $record)
                            <tr>
                                <td><span class="ubp-table-primary">{{ $record->nama_kegiatan }}</span><span class="ubp-table-muted">{{ $record->tanggal?->format('d M Y') ?? 'Tanpa tanggal' }}</span></td>
                                <td>{{ $record->ormawa?->nama ?? '-' }}</td>
                                <td>{{ $record->jenis_reimbursement }}</td>
                                <td>Rp{{ number_format((float) $record->nominal, 0, ',', '.') }}</td>
                                <td><x-ui.status-badge :status="$record->status" /></td>
                                <td>@if($record->link_penyelenggara)<a class="ubp-table-link" href="{{ $record->link_penyelenggara }}" target="_blank">Penyelenggara</a>@else - @endif</td>
                            </tr>
                        @empty
                            <x-ui.table-empty-row :colspan="6" title="Belum ada reimbursement Ormawa" message="Pengajuan reimbursement Ormawa akan muncul di sini." />
                        @endforelse
                    </tbody>
                @endif
            </table>

            <x-slot:pagination>
                {{ $records->links() }}
            </x-slot:pagination>
        </x-ui.table-shell>
    </x-ui.section-shell>
</x-app-layout>
