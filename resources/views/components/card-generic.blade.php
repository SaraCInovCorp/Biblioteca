@props([
    'imageUrl' => '',
    'title' => '',
    'description' => '',
    'buttonText' => '',
    'buttonUrl' => '',
])

@php
    use Illuminate\Support\Str;

    if ($imageUrl && !Str::startsWith($imageUrl, ['http://', 'https://'])) {
        $imageUrl = asset('storage/' . ltrim($imageUrl, '/'));
    }
@endphp

<div {{ $attributes->merge(['class' => 'card card-side bg-base-100 shadow-sm']) }}>
    @if($imageUrl)
        <figure class="w-48">
            <img src="{{ $imageUrl }}" alt="{{ $title }}" />
        </figure>
    @endif

    <div class="card-body">
        @if($title)
            <h2 class="card-title">{{ $title }}</h2>
        @endif

        @if($description)
            <p>{{ $description }}</p>
        @endif

        <div>
            {{ $slot }}
        </div>

        @if($buttonText && $buttonUrl)
            <div class="card-actions justify-end">
                <a href="{{ $buttonUrl }}" class="btn btn-wide bg-gray-800 dark:bg-gray-200 text-white dark:text-gray-800 hover:bg-gray-500 dark:hover:bg-white transition ease-in-out duration-300">
                    {{ $buttonText }}
                </a>
            </div>
        @endif
    </div>
</div>