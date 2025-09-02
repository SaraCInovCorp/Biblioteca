<button {{ $attributes->merge(['type' => 'button', 'class' => 'btn btn-error transition ease-in-out duration-300']) }}>
    {{ $slot }}
</button>
