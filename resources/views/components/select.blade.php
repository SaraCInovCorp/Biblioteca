@props(['label', 'options', 'selected'])
<select {!! $attributes->merge(['class' => 'select select-xl']) !!}>
    <option value="">Selecione {{ $label }}</option>
    @foreach ($options as $value => $name)
        <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }}>{{ $name }}</option>
    @endforeach
</select>