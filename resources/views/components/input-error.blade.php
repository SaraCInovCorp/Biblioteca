@props(['for'])

@error($for)
    <p {{ $attributes->merge(['class' => 'input input-error']) }}>{{ $message }}</p>
@enderror
