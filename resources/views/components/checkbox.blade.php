@props(['checked' => false])

<input
    type="checkbox"
    {!! $attributes->merge(['class' => 'checkbox checkbox-xl']) !!}
    @if($checked) checked @endif
/>