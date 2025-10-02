@props(['label', 'options', 'selected' => null])

@php
    $wireModel = null;
    foreach ($attributes->getIterator() as $key => $value) {
        if (str_starts_with($key, 'wire:model')) {
            $wireModel = $value;
            break;
        }
    }
@endphp

<select {!! $attributes->merge(['class' => 'select select-xl']) !!}>
    <option value="">{{ __('Selecione :label', ['label' => $label]) }}</option>

    @foreach ($options as $value => $name)
        @php
            $isSelected = $wireModel ? null : ($selected == $value ? 'selected' : null);
        @endphp
        <option value="{{ $value }}" {{ $isSelected }}>
            {{ $name }}
        </option>
    @endforeach
</select>
