@props([
    'as' => 'button',
    'href' => null,
])

@if ($as === 'a' && $href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => 'btn btn-ghost dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 transition ease-in-out duration-300']) }}>
        {{ $slot }}
    </a>
@else
    <button type="button" {{ $attributes->merge(['class' => 'btn btn-ghost dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 transition ease-in-out duration-300']) }}>
        {{ $slot }}
    </button>
@endif
