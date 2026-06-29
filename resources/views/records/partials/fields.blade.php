@php
    $record = $record ?? null;
    $prefix = $prefix ?? ($record ? 'edit-'.$record->id : 'create');
@endphp

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label" for="{{ $prefix }}-semester_id">Semester dan Tahun Akademik</label>
        <select id="{{ $prefix }}-semester_id" name="semester_id" class="form-select ubp-control" required>
            @foreach($semesters as $semester)
                <option value="{{ $semester->id }}" @selected(old('semester_id', $record?->semester_id) == $semester->id)>{{ $semester->nama }} - {{ $semester->tahun_akademik }}</option>
            @endforeach
        </select>
    </div>
    @unless(auth()->user()->hasRole('kaprodi'))
        <div class="col-md-6">
            <label class="form-label" for="{{ $prefix }}-prodi_id">Prodi</label>
            <select id="{{ $prefix }}-prodi_id" name="prodi_id" class="form-select ubp-control" required>
                @foreach($prodis as $prodi)
                    <option value="{{ $prodi->id }}" @selected(old('prodi_id', $record?->prodi_id) == $prodi->id)>{{ $prodi->nama }}</option>
                @endforeach
            </select>
        </div>
    @endunless

    @foreach($config['fields'] as $name => $field)
        <?php $fieldId = $prefix.'-'.$name; ?>
        <div class="{{ $field['type'] === 'textarea' ? 'col-12' : 'col-md-6' }}">
            <label class="form-label" for="{{ $fieldId }}">{{ $field['label'] }}</label>
            @if($field['type'] === 'textarea')
                <textarea id="{{ $fieldId }}" name="{{ $name }}" class="form-control ubp-control @error($name) is-invalid @enderror" rows="4" @required($field['required'] ?? false)>{{ old($name, $record?->{$name}) }}</textarea>
            @elseif($field['type'] === 'select')
                <select id="{{ $fieldId }}" name="{{ $name }}" class="form-select ubp-control @error($name) is-invalid @enderror" @required($field['required'] ?? false)>
                    @unless($field['required'] ?? false)
                        <option value="">-</option>
                    @endunless
                    @foreach($field['options'] as $value => $label)
                        @php
                            $optionValue = is_string($value) && ! is_numeric($value) ? $label : $value;
                        @endphp
                        <option value="{{ $optionValue }}" @selected((string) old($name, $record?->{$name}) === (string) $optionValue)>{{ $label }}</option>
                    @endforeach
                </select>
            @elseif($field['type'] === 'file')
                <input id="{{ $fieldId }}" type="file" name="{{ $name }}" class="form-control ubp-control @error($name) is-invalid @enderror" accept=".pdf,image/*">
                @if($record?->{$name})
                    <a class="small d-inline-block mt-2" href="{{ asset('storage/'.$record->{$name}) }}" target="_blank">Lihat file saat ini</a>
                @endif
            @else
                @php
                    $val = old($name, $record?->{$name});
                    if ($val && $field['type'] === 'date') $val = date('Y-m-d', strtotime((string) $val));
                @endphp
                <input id="{{ $fieldId }}" name="{{ $name }}" type="{{ $field['type'] }}" class="form-control ubp-control @error($name) is-invalid @enderror" value="{{ $val }}" @required($field['required'] ?? false)>
            @endif
            @error($name)
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    @endforeach
</div>
