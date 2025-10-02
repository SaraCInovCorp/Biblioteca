@props([
    'link' => route('carrinho.index'),
    'count' => 0, // quantidade total de itens no carrinho
    'label' => 'Ver carrinho',
    'color' => 'text-gray-800',
    'shadow' => 'shadow-md',
    'type' => 'button',
])

<a href="{{ $link }}" class="relative flex items-center" aria-label="{{ $label }}">
    <svg class="w-6 h-6 {{ $count == 0 ? 'opacity-50' : '' }}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" fill="currentColor" stroke="none">
        <path d="M24 48C10.7 48 0 58.7 0 72C0 85.3 10.7 96 24 96L69.3 96C73.2 96 76.5 98.8 77.2 102.6L129.3 388.9C135.5 423.1 165.3 448 200.1 448L456 448C469.3 448 480 437.3 480 424C480 410.7 469.3 400 456 400L200.1 400C188.5 400 178.6 391.7 176.5 380.3L171.4 352L475 352C505.8 352 532.2 330.1 537.9 299.8L568.9 133.9C572.6 114.2 557.5 96 537.4 96L124.7 96L124.3 94C119.5 67.4 96.3 48 69.2 48L24 48zM208 576C234.5 576 256 554.5 256 528C256 501.5 234.5 480 208 480C181.5 480 160 501.5 160 528C160 554.5 181.5 576 208 576zM432 576C458.5 576 480 554.5 480 528C480 501.5 458.5 480 432 480C405.5 480 384 501.5 384 528C384 554.5 405.5 576 432 576z"/>
    </svg>
    @if($count > 0)
        <span class="absolute top-0 right-0 -translate-y-1/2 translate-x-1/2 bg-red-600 text-white rounded-full text-xs font-bold w-5 h-5 flex items-center justify-center">
            {{ $count }}
        </span>
    @endif
</a>
