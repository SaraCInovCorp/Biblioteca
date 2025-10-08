<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Logs de Atividades') }}
        </h2>
    </x-slot>

    <div class="flex-1">
        <!-- Formulário de filtro -->
        <form method="GET" action="{{ route('admin.activity-logs.index') }}" class="mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-4">
                <div class="flex flex-col">
                    <x-label value="ID Usuário" for="user_id" />
                    <x-input name="user_id" id="user_id" value="{{ request('user_id') }}" placeholder="ID Usuário" class="w-full"/>
                </div>
                <div class="flex flex-col">
                    <x-label value="Nome do Log" for="log_name" />
                    <x-input name="log_name" id="log_name" value="{{ request('log_name') }}" placeholder="Log" class="w-full"/>
                </div>
                <div class="flex flex-col">
                    <x-label value="Evento/Descrição" for="description" />
                    <x-input name="description" id="description" value="{{ request('description') }}" placeholder="Evento/Descrição" class="w-full"/>
                </div>
                <div class="flex flex-col">
                    <x-label value="Modelo" for="subject_type" />
                    <x-input name="subject_type" id="subject_type" value="{{ request('subject_type') }}" placeholder="Ex: User" class="w-full"/>
                </div>
                <div class="flex flex-col">
                    <x-label value="ID Modelo" for="subject_id" />
                    <x-input name="subject_id" id="subject_id" value="{{ request('subject_id') }}" placeholder="ID Modelo" class="w-full"/>
                </div>
                <div class="flex flex-col">
                    <x-label value="Data Início" for="date_from" />
                    <x-input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" class="w-full"/>
                </div>
                <div class="flex flex-col">
                    <x-label value="Data Fim" for="date_to" />
                    <x-input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" class="w-full"/>
                </div>
            </div>
                <div class="flex justify-end gap-2">
                    <x-secondary-button as="a" href="{{ route('admin.activity-logs.index') }}">Limpar</x-secondary-button>
                    <x-button type="submit">Filtrar</x-button>
                </div>
            
        
        <!-- Listagens -->
        @if($logs->isEmpty())
            <div class="p-4 text-center text-gray-600 italic">
                Nenhum log encontrado para os filtros fornecidos.
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-4">
                @foreach($logs as $log)
                    <div class="border rounded p-3 shadow bg-white break-words">
                        <p class="font-bold text-sm my-2 text-gray-700">
                            {{ $log->created_at->format('d/m/Y H:i:s') }}
                        </p>
                        <p class="mb-1"><span class="font-semibold">Usuário:</span> {{ $log->causer->name ?? $log->causer->email ?? 'N/D' }}</p>
                        <p class="mb-1"><span class="font-semibold">Log:</span> {{ $log->log_name }}</p>
                        <p class="mb-1"><span class="font-semibold">Evento:</span> {{ $log->description }}</p>
                        <p class="mb-1"><span class="font-semibold">Modelo:</span> {{ class_basename($log->subject_type) }}</p>
                        <p class="mb-1"><span class="font-semibold">ID Modelo:</span> {{ $log->subject_id }}</p>
                        <p class="mb-1"><span class="font-semibold">IP:</span> {{ $log->properties['ip'] ?? '-' }}</p>
                        <p class="mb-1"><span class="font-semibold">Browser:</span>
                            <span class="block break-all max-w-full text-xs" style="word-break: break-all;">
                                {{ $log->properties['user_agent'] ?? '-' }}
                            </span>
                        </p>
                        <div class="font-mono text-xs mt-2 bg-gray-50 p-2 rounded max-w-full overflow-x-auto">
                            <strong>Propriedades:</strong>
                            <pre class="whitespace-pre-line break-words" style="word-break: break-all;">{{ json_encode($log->properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-4">
                {{ $logs->withQueryString()->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
