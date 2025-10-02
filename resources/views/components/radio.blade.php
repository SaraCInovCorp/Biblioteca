@props([
    'name',
    'value',
    'id' => null,
    'checked' => false,
    'label' => null,
])

@php
    $id = $id ?? $name . '-' . $value;
@endphp

<div class="flex items-center gap-2">
    <input type="radio" 
           id="{{ $id }}" 
           name="{{ $name }}" 
           value="{{ $value }}"
           {{ $checked ? 'checked' : '' }}
           {{ $attributes->merge(['class' => 'radio radio-xl']) }} />
    @if ($label)
        <label for="{{ $id }}" class="cursor-pointer select-none">{{ $label }}</label>
    @endif
</div>
