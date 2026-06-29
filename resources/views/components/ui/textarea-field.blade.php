@props(['name', 'label', 'value' => null, 'required' => false])

<div {{ $attributes->merge(['class' => 'form-block']) }}>
    <label class="form-label" for="{{ $name }}">{{ $label }}</label>
    <textarea id="{{ $name }}" name="{{ $name }}" class="form-control ubp-control @error($name) is-invalid @enderror" rows="4" @required($required)>{{ old($name, $value) }}</textarea>
    @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
