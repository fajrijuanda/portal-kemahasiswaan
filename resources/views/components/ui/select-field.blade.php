@props(['name', 'label', 'options' => [], 'value' => null, 'required' => false, 'placeholder' => null])

<div {{ $attributes->merge(['class' => 'form-block']) }}>
    <label class="form-label" for="{{ $name }}">{{ $label }}</label>
    <select id="{{ $name }}" name="{{ $name }}" class="form-select ubp-control @error($name) is-invalid @enderror" @required($required)>
        @if($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif
        @foreach($options as $optionValue => $optionLabel)
            @php($actualValue = is_int($optionValue) ? $optionLabel : $optionValue)
            <option value="{{ $actualValue }}" @selected((string) old($name, $value) === (string) $actualValue)>{{ $optionLabel }}</option>
        @endforeach
    </select>
    @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
