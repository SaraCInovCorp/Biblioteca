@props([
    'userName' => '',
    'reviewText' => '',
    'showButton' => false,
    'buttonText' => 'Action',
    'status' => null,
    'showStatus' => false,
    'class' => '', // permite sobrescrever largura/cor etc.
])

<div {{ $attributes->merge(['class' => 'card w-80 bg-base-100 card-xs shadow-sm m-3 ' . $class]) }}>
    <div class="card-body">
        @if($userName)
            <h3 class="font-semibold text-gray-500 text-ls">{{ $userName }}</h3>
        @endif

        @if($showStatus && $status)
            <p class="text-xs text-gray-500 capitalize mb-1">Status: {{ $status }}</p>
        @endif

        <p class="mt-2 font-semibold text-base italic">"{{ $reviewText }}"</p>

        @if($showButton)
            <div class="justify-end card-actions mt-4">
                <x-button>{{ $buttonText }}</x-button>
            </div>
        @endif
    </div>
</div>
