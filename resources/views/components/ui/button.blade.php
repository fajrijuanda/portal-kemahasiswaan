@props(['variant' => 'primary', 'href' => null, 'type' => 'submit'])

@php
    $class = 'ubp-btn ubp-btn-'.$variant;
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $class]) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $class]) }}>{{ $slot }}</button>
@endif
