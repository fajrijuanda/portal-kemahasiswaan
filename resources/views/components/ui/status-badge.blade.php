@props(['status'])

@php
    $tone = match ($status) {
        'Terverifikasi', 'Disetujui', 'Aktif', 'Lengkap', 'Selesai' => 'success',
        'Ditolak', 'Perlu Follow Up' => 'danger',
        'Diproses', 'Berjalan' => 'warning',
        default => 'neutral',
    };
@endphp

<span class="ubp-badge ubp-badge-{{ $tone }}">{{ $status }}</span>
