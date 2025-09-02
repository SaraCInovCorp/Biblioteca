<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn btn-wide bg-gray-800 dark:bg-gray-200 text-white dark:text-gray-800 hover:bg-gray-500 dark:hover:bg-white transition ease-in-out duration-300']) }}>
 {{ $slot }}
</button>