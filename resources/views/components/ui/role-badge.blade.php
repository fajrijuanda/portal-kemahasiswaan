@props(['role' => 'user'])

@php
    $normalized = strtolower(trim($role ?: 'user'));
    $label = \Illuminate\Support\Str::headline($normalized);
    $config = match ($normalized) {
        'super user' => ['tone' => 'violet', 'icon' => 'access'],
        'admin' => ['tone' => 'blue', 'icon' => 'grid'],
        'kabag' => ['tone' => 'rose', 'icon' => 'semester'],
        'kaprodi' => ['tone' => 'emerald', 'icon' => 'prodi'],
        'warek' => ['tone' => 'amber', 'icon' => 'prestasi'],
        'mahasiswa' => ['tone' => 'cyan', 'icon' => 'beasiswa'],
        'ormawa' => ['tone' => 'orange', 'icon' => 'event'],
        default => ['tone' => 'slate', 'icon' => 'user'],
    };
@endphp

<span class="ubp-role-badge ubp-role-badge-{{ $config['tone'] }}">
    <i><x-ui.app-icon :name="$config['icon']" /></i>
    {{ $label }}
</span>
