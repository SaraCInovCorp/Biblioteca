@php
    $classes = 'card bg-base-100 w-96 shadow-sm border border-transparent hover:border-blue-800 group transition-colors duration-300';
@endphp

<div {{ $attributes(['class' => $classes]) }}>
    {{ $slot }}
</div>

