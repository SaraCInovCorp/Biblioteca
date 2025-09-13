<header class="w-full lg:max-w-7xl mx-auto mb-6 px-4">
  <nav class="flex items-center justify-between pt-5">

    {{-- Logo à esquerda --}}
    <div class="flex items-center flex-shrink-0 space-x-2">
      <x-authentication-card-logo :size="10" />
      <span class="text-lg font-semibold">Nossa Biblioteca</span>
    </div>

    {{-- Menu central --}}
    <div class="hidden md:flex space-x-8 flex-grow justify-center text-sm font-medium text-gray-700 dark:text-gray-300">
      <x-secondary-button as="a"  href="/livros">Livro</x-secondary-button>
      <x-secondary-button as="a"  href="/autores">Autor</x-secondary-button>
      <x-secondary-button as="a"  href="/editoras">Editora</x-secondary-button>
      <x-secondary-button as="a"  href="/requisicoes">Requisições</x-secondary-button>
      <x-secondary-button as="a"  href="/users">Histórico</x-secondary-button>
    </div>

    {{-- Menu à direita --}}
    @if (Route::has('login'))
      <div class="flex items-center space-x-4">
        @if(auth()->check() && auth()->user()->isAdmin())
            <x-secondary-button as="a" href="{{ route('admin.register') }}">
                Registrar Admin
            </x-secondary-button>
        @endif
        @auth
          <x-secondary-button as="a"  href="{{ url('/dashboard') }}">
            Dashboard
          </x-secondary-button> 
          <form method="POST" action="{{ route('logout') }}" x-data>
            @csrf
            <x-secondary-button type="submit">
              Logout
            </x-secondary-button>
          </form>
        @else
          <x-secondary-button as="a" href="{{ route('login') }}">
            Log in
          </x-secondary-button>

          @if (Route::has('register'))
            <x-secondary-button as="a"  href="{{ route('register') }}">
              Register
            </x-secondary-button>
          @endif
          
        @endauth
      </div>
    @endif

  </nav>
</header>
