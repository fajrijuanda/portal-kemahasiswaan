<x-app-layout>
    <x-slot name="pageTitle">Dashboard</x-slot>

    @php
        $filterParams = array_filter(['semester_id' => $selectedSemester, 'prodi_id' => $selectedProdi]);
        $cardMeta = [
            'Prestasi' => ['icon' => 'prestasi', 'tone' => 'blue', 'caption' => 'Capaian dan lomba mahasiswa', 'href' => route('prestasi.table', $filterParams)],
            'Event/Reimbursement' => ['icon' => 'event', 'tone' => 'teal', 'caption' => 'Event, transport, dan fasilitas', 'href' => route('event.table', $filterParams)],
            'Tracer Study Input' => ['icon' => 'tracer', 'tone' => 'violet', 'caption' => 'Input tracer yang terkumpul', 'href' => route('tracer.table', $filterParams)],
            'Beasiswa' => ['icon' => 'beasiswa', 'tone' => 'emerald', 'caption' => 'Data penerima beasiswa', 'href' => route('beasiswa.table', $filterParams)],
            'Humas Marketing' => ['icon' => 'grid', 'tone' => 'rose', 'caption' => 'Aktivitas promosi dan publikasi', 'href' => route('unit-activities.index', array_merge(['unit' => 'humas-marketing'], $filterParams))],
            'Science Center' => ['icon' => 'prodi', 'tone' => 'cyan', 'caption' => 'Program science center', 'href' => route('unit-activities.index', array_merge(['unit' => 'science-center'], $filterParams))],
            'Pengembangan Ormawa' => ['icon' => 'user', 'tone' => 'amber-soft', 'caption' => 'Kegiatan dan pembinaan ormawa', 'href' => route('ormawa.index', array_merge(['section' => 'kegiatan'], $filterParams))],
            'Alumni dan Pusat Karir' => ['icon' => 'access', 'tone' => 'slate', 'caption' => 'Alumni, karir, dan relasi industri', 'href' => route('unit-activities.index', array_merge(['unit' => 'alumni-pusat-karir'], $filterParams))],
        ];
        $totalRecords = collect($cards)->sum();
        $filledCards = collect($cards)->filter(fn ($value) => $value > 0)->count();
        $selectedSemesterName = $selectedSemester ? optional($semesters->firstWhere('id', $selectedSemester))->nama : 'Semua Semester';
        $selectedProdiName = $selectedProdi ? optional($prodis->firstWhere('id', $selectedProdi))->nama : 'Semua Prodi';
    @endphp

    <div class="ubp-table-shell mb-4">
        <div class="ubp-table-toolbar">
            <div>
                <h2 class="ubp-table-title">Dashboard Monitoring</h2>
                <p class="ubp-table-subtitle">Pantau seluruh modul layanan kemahasiswaan: prestasi, event, beasiswa, tracer, unit, dan publikasi.</p>
            </div>
            <div class="ubp-table-toolbar-actions">
                <span class="ubp-badge ubp-badge-neutral">{{ number_format($totalRecords) }} Total Data</span>
                <span class="ubp-badge ubp-badge-neutral">{{ $selectedSemesterName }}</span>
            </div>
        </div>
        <div class="ubp-table-controls" style="padding: 1rem 1.15rem; border-bottom: 1px solid var(--ubp-line); background: #f8fafc;">
            <form class="ubp-table-action-group" method="GET">
                <div class="d-flex align-items-center gap-2 me-3">
                    <span class="text-muted"><x-ui.app-icon name="grid" /></span>
                    <strong style="font-size: 0.9rem;">Filter:</strong>
                </div>
                <select name="semester_id" class="form-select ubp-control w-auto">
                    <option value="">Semua Semester</option>
                    @foreach($semesters as $semester)
                        <option value="{{ $semester->id }}" @selected($selectedSemester === $semester->id)>{{ $semester->nama }}</option>
                    @endforeach
                </select>
                @unless(auth()->user()->hasRole('kaprodi'))
                    <select name="prodi_id" class="form-select ubp-control w-auto">
                        <option value="">Semua Prodi</option>
                        @foreach($prodis as $prodi)
                            <option value="{{ $prodi->id }}" @selected($selectedProdi === $prodi->id)>{{ $prodi->nama }}</option>
                        @endforeach
                    </select>
                @endunless
                <button class="ubp-table-action ubp-table-action-primary" type="submit">Filter Data</button>
                @if($selectedSemester || $selectedProdi)
                    <a href="{{ route('dashboard') }}" class="ubp-table-action">Reset</a>
                @endif
            </form>
        </div>
    </div>

    <div class="ubp-stat-grid mb-5" style="grid-template-columns: repeat(4, minmax(0, 1fr));">
        @foreach($cards as $label => $value)
            @php($meta = $cardMeta[$label] ?? ['icon' => 'grid', 'tone' => 'blue', 'caption' => 'Data kemahasiswaan', 'href' => '#'])
            <a href="{{ $meta['href'] }}" class="ubp-stat-card tone-{{ $meta['tone'] }}" style="text-decoration: none;">
                <div>
                    <small>{{ $label }}</small>
                    <strong>{{ number_format($value) }}</strong>
                    <em style="background: rgba(255,255,255,0.4); color: inherit;">{{ $meta['caption'] }}</em>
                </div>
                <span class="ubp-stat-icon"><x-ui.app-icon :name="$meta['icon']" /></span>
            </a>
        @endforeach
    </div>



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
