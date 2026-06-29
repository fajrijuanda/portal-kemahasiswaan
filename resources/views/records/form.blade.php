<x-app-layout>
    <x-slot name="header">
        <h1 class="ubp-title">{{ $record ? 'Edit' : 'Tambah' }} {{ $config['title'] }}</h1>
    </x-slot>

    <form method="POST" enctype="multipart/form-data" action="{{ $record ? route('records.update', [$module, $record]) : route('records.store', $module) }}">
        @csrf
        @if($record) @method('PUT') @endif
        <x-ui.card>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Semester dan Tahun Akademik</label>
                    <select name="semester_id" class="form-select ubp-control" required>
                        @foreach($semesters as $semester)
                            <option value="{{ $semester->id }}" @selected(old('semester_id', $record?->semester_id) == $semester->id)>{{ $semester->nama }} - {{ $semester->tahun_akademik }}</option>
                        @endforeach
                    </select>
                </div>
                @unless(auth()->user()->hasRole('kaprodi'))
                    <div class="col-md-6">
                        <label class="form-label">Prodi</label>
                        <select name="prodi_id" class="form-select ubp-control" required>
                            @foreach($prodis as $prodi)
                                <option value="{{ $prodi->id }}" @selected(old('prodi_id', $record?->prodi_id) == $prodi->id)>{{ $prodi->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                @endunless

                @foreach($config['fields'] as $name => $field)
                    <div class="{{ $field['type'] === 'textarea' ? 'col-12' : 'col-md-6' }}">
                        @if($field['type'] === 'textarea')
                            <x-ui.textarea-field :name="$name" :label="$field['label']" :value="$record?->{$name}" :required="$field['required'] ?? false" />
                        @elseif($field['type'] === 'select')
                            <x-ui.select-field :name="$name" :label="$field['label']" :options="$field['options']" :value="$record?->{$name}" :required="$field['required'] ?? false" />
                        @elseif($field['type'] === 'file')
                            <label class="form-label">{{ $field['label'] }}</label>
                            <input type="file" name="{{ $name }}" class="form-control ubp-control @error($name) is-invalid @enderror" accept="image/*">
                            @if($record?->{$name})
                                <a class="small d-inline-block mt-2" href="{{ asset('storage/'.$record->{$name}) }}" target="_blank">Lihat file saat ini</a>
                            @endif
                            @error($name)<div class="invalid-feedback">{{ $message }}</div>@enderror
                        @else
                            <x-ui.form-field :name="$name" :label="$field['label']" :type="$field['type']" :value="$record?->{$name}" :required="$field['required'] ?? false" />
                        @endif
                    </div>
                @endforeach
            </div>
            <div class="d-flex justify-content-between mt-4">
                <x-ui.button variant="outline" :href="route('records.index', $module)">Kembali</x-ui.button>
                <x-ui.button>Simpan</x-ui.button>
            </div>
        </x-ui.card>
    </form>
</x-app-layout>
