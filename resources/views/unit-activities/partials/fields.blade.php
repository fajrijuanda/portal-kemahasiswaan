<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label" for="{{ $prefix }}-semester_id">Semester</label>
        <select id="{{ $prefix }}-semester_id" name="semester_id" class="form-select ubp-control" required>
            <option value="">Pilih semester</option>
            @foreach($semesters as $semester)
                <option value="{{ $semester->id }}" @selected(old('semester_id', $record?->semester_id) == $semester->id)>{{ $semester->nama }} - {{ $semester->tahun_akademik }}</option>
            @endforeach
        </select>
    </div>

    @unless(auth()->user()->hasRole('kaprodi'))
        <div class="col-md-6">
            <label class="form-label" for="{{ $prefix }}-prodi_id">Program Studi</label>
            <select id="{{ $prefix }}-prodi_id" name="prodi_id" class="form-select ubp-control" required>
                <option value="">Pilih prodi</option>
                @foreach($prodis as $prodi)
                    <option value="{{ $prodi->id }}" @selected(old('prodi_id', $record?->prodi_id) == $prodi->id)>{{ $prodi->nama }}</option>
                @endforeach
            </select>
        </div>
    @endunless

    @if(($unit ?? null) === 'pengembangan-ormawa' && ($canUseOrmawa ?? true))
        <div class="col-md-6">
            <label class="form-label" for="{{ $prefix }}-ormawa_id">Ormawa</label>
            <select id="{{ $prefix }}-ormawa_id" name="ormawa_id" class="form-select ubp-control">
                <option value="">Pilih Ormawa</option>
                @foreach($ormawas as $ormawa)
                    <option value="{{ $ormawa->id }}" @selected(old('ormawa_id', $record?->ormawa_id) == $ormawa->id)>{{ $ormawa->nama }}</option>
                @endforeach
            </select>
        </div>
    @endif

    <div class="col-md-8">
        <label class="form-label" for="{{ $prefix }}-judul">Nama Kegiatan</label>
        <input id="{{ $prefix }}-judul" name="judul" class="form-control ubp-control" value="{{ old('judul', $record?->judul) }}" required>
    </div>

    <div class="col-md-4">
        <label class="form-label" for="{{ $prefix }}-tanggal">Tanggal</label>
        @php
            $val = old('tanggal', $record?->tanggal);
            if ($val) $val = date('Y-m-d', strtotime((string) $val));
        @endphp
        <input id="{{ $prefix }}-tanggal" name="tanggal" type="date" class="form-control ubp-control" value="{{ $val }}">
    </div>

    <div class="col-md-6">
        <label class="form-label" for="{{ $prefix }}-penanggung_jawab">Penanggung Jawab</label>
        <input id="{{ $prefix }}-penanggung_jawab" name="penanggung_jawab" class="form-control ubp-control" value="{{ old('penanggung_jawab', $record?->penanggung_jawab) }}">
    </div>

    <div class="col-md-6">
        <label class="form-label" for="{{ $prefix }}-status">Status</label>
        <select id="{{ $prefix }}-status" name="status" class="form-select ubp-control" required>
            @foreach(['Draft', 'Berjalan', 'Selesai', 'Tertunda'] as $status)
                <option value="{{ $status }}" @selected(old('status', $record?->status ?? 'Draft') === $status)>{{ $status }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-12">
        <label class="form-label" for="{{ $prefix }}-catatan">Catatan</label>
        <textarea id="{{ $prefix }}-catatan" name="catatan" class="form-control ubp-control" rows="4">{{ old('catatan', $record?->catatan) }}</textarea>
    </div>
</div>
