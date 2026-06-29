@props(['name', 'label', 'type' => 'text', 'value' => null, 'required' => false, 'help' => null, 'placeholder' => null])

<div {{ $attributes->merge(['class' => 'form-block']) }}>
    <label class="form-label" for="{{ $name }}">{{ $label }}</label>
    <input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}" class="form-control ubp-control @error($name) is-invalid @enderror" value="{{ old($name, $value) }}" @required($required) @if($placeholder) placeholder="{{ $placeholder }}" @endif>
    @if($help)
        <div class="form-text">{{ $help }}</div>
    @endif
    @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
