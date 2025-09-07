<div class="flex flex-row items-center space-x-2 w-full max-w-md">
    <div class="flex-1">
    <input 
        type="password"
        id="{{ $attributes->get('id') }}"
        {!! $attributes->except('id')->merge(['class' => 'input input-xl flex-grow min-w-0']) !!}
    />
</div>
    <div class="min-w-[90px]">
    <x-secondary-button
        type="button"
        onclick="togglePassword('{{ $attributes->get('id') }}', this)"
        >
        Mostrar
    </x-secondary-button>
</div>
</div>

@once
<script>
    function togglePassword(inputId, btn) {
        const input = document.getElementById(inputId);
        if (!input) return;
        if (input.type === 'password') {
            input.type = 'text';
            btn.textContent = 'Ocultar';
        } else {
            input.type = 'password';
            btn.textContent = 'Mostrar';
        }
    }
</script>
@endonce
