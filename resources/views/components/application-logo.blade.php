@props(['size' => 28])
<img src="{{ asset('images/logo.png') }}" alt="Logo" class="size-{{ $size }}" {{ $attributes }} />