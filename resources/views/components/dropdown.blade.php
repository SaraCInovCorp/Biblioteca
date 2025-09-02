@props(['align' => 'right', 'width' => '48', 'contentClasses' => 'dropdown-content menu bg-base-100 rounded-box z-1 w-52 p-2 shadow-sm', 'dropdownClasses' => 'dropdown'])

@php
$alignmentClasses = match ($align) {
    'left' => 'ltr:origin-top-left rtl:origin-top-right start-0',
    'top' => 'origin-top',
    'none', 'false' => '',
    default => 'ltr:origin-top-right rtl:origin-top-left end-0',
};

$width = match ($width) {
    '48' => 'w-48',
    '60' => 'w-60',
    default => 'w-48',
};
@endphp

<div class="relative dropdown" x-data="{ open: false }" @click.away="open = false" @close.stop="open = false">
    <div @click="open = !open" tabindex="0" role="button">
        {{ $trigger }}
    </div>

    
        <ul tabindex="0" class="rounded-md ring-1 ring-black ring-opacity-5 {{ $alignmentClasses }} {{ $contentClasses }}">
            {{ $content }}
        </ul>
    
</div>
