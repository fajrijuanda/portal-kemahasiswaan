<x-app-layout>
    <x-slot name="pageTitle">Dashboard</x-slot>

    @php
        $filterParams = array_filter(['semester_id' => $selectedSemester, 'prodi_id' => $selectedProdi]);
        $cardMeta = [
            'Prestasi' => ['icon' => 'prestasi', 'tone' => 'blue', 'caption' => 'Capaian dan lomba mahasiswa', 'href' => route('prestasi.table', $filterParams)],
            'Event/Reimbursement' => ['icon' => 'event', 'tone' => 'teal', 'caption' => 'Event, transport, dan fasilitas', 'href' => route('event.table', $filterParams)],
            'Tracer Study Input' => ['icon' => 'tracer', 'tone' => 'violet', 'caption' => 'Input tracer yang terkumpul', 'href' => route('tracer.table', $filterParams)],
            'Beasiswa' => ['icon' => 'beasiswa', 'tone' => 'emerald', 'caption' => 'Data penerima beasiswa', 'href' => route('beasiswa.table', $filterParams)],
            'Humas Marketing' => ['icon' => 'grid', 'tone' => 'rose', 'caption' => 'Aktivitas promosi dan publikasi', 'href' => route('unit-data.index', array_merge(['unit' => 'humas-marketing'], $filterParams))],
            'Science Center' => ['icon' => 'prodi', 'tone' => 'cyan', 'caption' => 'Program science center', 'href' => route('unit-data.index', array_merge(['unit' => 'science-center'], $filterParams))],
            'Pengembangan Ormawa' => ['icon' => 'user', 'tone' => 'amber-soft', 'caption' => 'Kegiatan dan pembinaan ormawa', 'href' => route('ormawa-admin.index', array_merge(['section' => 'kegiatan'], $filterParams))],
            'Alumni dan Pusat Karir' => ['icon' => 'access', 'tone' => 'slate', 'caption' => 'Alumni, karir, dan relasi industri', 'href' => route('unit-data.index', array_merge(['unit' => 'alumni-pusat-karir'], $filterParams))],
        ];
        $totalRecords = collect($cards)->sum();
        $filledCards = collect($cards)->filter(fn ($value) => $value > 0)->count();
        $selectedSemesterName = $selectedSemester ? optional($semesters->firstWhere('id', $selectedSemester))->nama : 'Semua Semester';
        $selectedProdiName = $selectedProdi ? optional($prodis->firstWhere('id', $selectedProdi))->nama : 'Semua Prodi';
    @endphp

    <section class="ubp-dashboard-hero">
        <div class="ubp-rekap-hero-main">
            <span class="ubp-rekap-eyebrow">Dashboard</span>
            <h2>Pusat Monitoring Kemahasiswaan</h2>
            <p>Pantau semua layanan baru dari satu layar: prestasi, event, reimbursement, beasiswa, tracer, unit, Ormawa, master data, dan publikasi.</p>

            <div class="ubp-rekap-hero-stats">
                <span><strong>{{ number_format($totalRecords) }}</strong><small>Total data</small></span>
                <span><strong>{{ $filledCards }}/{{ count($cards) }}</strong><small>Modul terisi</small></span>
                <span><strong>{{ $selectedSemesterName }}</strong><small>Semester</small></span>
            </div>
        </div>

        <form class="ubp-rekap-filter-card ubp-dashboard-filter-card" method="GET">
            <div class="ubp-rekap-filter-title">
                <span><x-ui.app-icon name="grid" /></span>
                <div>
                    <strong>Filter Dashboard</strong>
                    <small>{{ $selectedProdiName }}</small>
                </div>
            </div>
            <label>
                <small>Semester</small>
                <select name="semester_id" class="form-select ubp-control">
                    <option value="">Semua Semester</option>
                    @foreach($semesters as $semester)
                        <option value="{{ $semester->id }}" @selected($selectedSemester === $semester->id)>{{ $semester->nama }} - {{ $semester->tahun_akademik }}</option>
                    @endforeach
                </select>
            </label>
            @unless(auth()->user()->hasRole('kaprodi'))
                <label>
                    <small>Program Studi</small>
                    <select name="prodi_id" class="form-select ubp-control">
                        <option value="">Semua Prodi</option>
                        @foreach($prodis as $prodi)
                            <option value="{{ $prodi->id }}" @selected($selectedProdi === $prodi->id)>{{ $prodi->nama }}</option>
                        @endforeach
                    </select>
                </label>
            @endunless
            <div class="ubp-rekap-filter-actions">
                <button class="ubp-btn ubp-btn-primary" type="submit">Filter</button>
                @if($selectedSemester || $selectedProdi)
                    <a href="{{ route('dashboard') }}" class="ubp-table-action">Reset</a>
                @endif
            </div>
        </form>
    </section>

    <section class="ubp-dashboard-launcher-panel">
        <div class="ubp-panel-heading ubp-dashboard-section-heading">
            <div>
                <span>MENU PORTAL</span>
                <h2>Akses cepat semua modul</h2>
                <p>Pilih modul untuk membuka overview atau tabel sesuai lingkup layanan.</p>
            </div>
        </div>
        <div class="ubp-dashboard-launcher-grid">
            @foreach($dashboardMenu as $item)
                <a class="ubp-dashboard-launcher tone-{{ $item['tone'] }}" href="{{ $item['href'] }}">
                    <span><x-ui.app-icon :name="$item['icon']" /></span>
                    <strong>{{ $item['label'] }}</strong>
                    <small>{{ $item['desc'] }}</small>
                    <em>{{ number_format($item['count']) }}</em>
                </a>
            @endforeach
        </div>
    </section>

    <section class="ubp-rekap-metric-grid">
        @foreach($cards as $label => $value)
            @php($meta = $cardMeta[$label] ?? ['icon' => 'grid', 'tone' => 'blue', 'caption' => 'Data kemahasiswaan', 'href' => '#'])
            <a href="{{ $meta['href'] }}" class="ubp-rekap-metric-card tone-{{ $meta['tone'] }}">
                <div class="ubp-rekap-metric-icon"><x-ui.app-icon :name="$meta['icon']" /></div>
                <div class="ubp-rekap-metric-copy">
                    <small>{{ $label }}</small>
                    <strong>{{ number_format($value) }}</strong>
                    <p>{{ $meta['caption'] }}</p>
                    <span class="ubp-metric-note">{{ $value > 0 ? 'Kelola data' : 'Input data' }}</span>
                </div>
                <div class="ubp-rekap-mini-chart"><canvas data-summary-label="{{ $label }}"></canvas></div>
            </a>
        @endforeach
    </section>

    @if($achievementQuotas->isNotEmpty())
        <x-ui.table-shell class="mt-4" title="Kuota Prestasi Prodi" subtitle="Pantauan slot dukungan prestasi dan jumlah yang sudah terpakai.">
            <table class="table align-middle ubp-table ubp-data-table">
                <thead><tr><th>Semester</th><th>Prodi</th><th>Slot</th><th>Terpakai</th><th>Sisa</th></tr></thead>
                <tbody>
                    @foreach($achievementQuotas as $quota)
                        <tr>
                            <td>{{ $quota->semester?->nama ?? '-' }}</td>
                            <td>{{ $quota->prodi?->nama ?? '-' }}</td>
                            <td>{{ $quota->slot_prestasi }}</td>
                            <td>{{ $quota->terpakai }}</td>
                            <td>{{ max(0, $quota->slot_prestasi - $quota->terpakai) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </x-ui.table-shell>
    @endif

    <section class="ubp-chart-grid ubp-rekap-chart-grid">
        @foreach([
            ['prestasiSemester', 'Prestasi by Semester', 'semester', route('charts.prestasi.semester')],
            ['prestasiProdi', 'Prestasi by Prodi', 'prodi', route('charts.prestasi.prodi')],
            ['claims', 'Event Reimbursement', 'event', route('charts.claims')],
            ['beasiswa', 'Beasiswa by Prodi', 'beasiswa', route('charts.beasiswa')],
            ['tracer', 'Tracer Study Sudah Input', 'tracer', route('charts.tracer')],
            ['humasMarketing', 'Humas Marketing by Status', 'grid', route('charts.unit-activities', 'humas-marketing')],
            ['scienceCenter', 'Science Center by Status', 'prodi', route('charts.unit-activities', 'science-center')],
            ['pengembanganOrmawa', 'Pengembangan Ormawa by Status', 'user', route('charts.unit-activities', 'pengembangan-ormawa')],
            ['alumniKarir', 'Alumni dan Pusat Karir by Status', 'access', route('charts.unit-activities', 'alumni-pusat-karir')],
        ] as [$id, $title, $icon, $url])
            <article class="ubp-chart-card ubp-rekap-chart-card">
                <div class="ubp-rekap-chart-head">
                    <span><x-ui.app-icon :name="$icon" /></span>
                    <h3>{{ $title }}</h3>
                </div>
                <div class="ubp-chart-canvas">
                    <canvas id="{{ $id }}" data-url="{{ $url }}"></canvas>
                </div>
            </article>
        @endforeach
    </section>

    <script>
        const query = new URLSearchParams(window.location.search);
        const colors = ['#0ea5e9', '#14b8a6', '#8b5cf6', '#10b981', '#ec4899', '#06b6d4', '#facc15', '#64748b'];

        fetch('{{ route('charts.summary.cards') }}?' + query.toString(), {headers: {'Accept': 'application/json'}})
            .then((response) => response.json())
            .then((summaryCharts) => {
                document.querySelectorAll('canvas[data-summary-label]').forEach((canvas, index) => {
                    const payload = summaryCharts[canvas.dataset.summaryLabel];
                    if (!payload) return;
                    new Chart(canvas, {
                        type: 'doughnut',
                        data: {
                            labels: payload.labels,
                            datasets: [{
                                data: payload.data,
                                backgroundColor: [colors[index % colors.length], 'rgba(255,255,255,.54)'],
                                borderColor: 'rgba(255,255,255,.78)',
                                borderWidth: 1,
                            }]
                        },
                        options: {
                            maintainAspectRatio: false,
                            responsive: true,
                            cutout: '68%',
                            plugins: { legend: { display: false }, tooltip: { enabled: true } }
                        }
                    });
                });
            });

        document.querySelectorAll('canvas[data-url]').forEach(async (canvas) => {
            const response = await fetch(canvas.dataset.url + '?' + query.toString(), {headers: {'Accept': 'application/json'}});
            const payload = await response.json();
            new Chart(canvas, {
                type: 'pie',
                data: {
                    labels: payload.labels,
                    datasets: [{
                        data: payload.data,
                        backgroundColor: payload.empty ? ['#dbe7f3'] : colors,
                        borderColor: '#ffffff',
                        borderWidth: 2,
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 12,
                                padding: 14,
                                font: {size: 11, weight: '700'}
                            }
                        }
                    },
                    onClick: (_, elements) => {
                        if (elements.length && payload.links[elements[0].index]) {
                            window.location.href = payload.links[elements[0].index];
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>
