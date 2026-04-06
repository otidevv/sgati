<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">
            Integraciones ({{ $system->integrationsFrom->count() + $system->integrationsTo->count() }})
        </h3>
        @can('integrations.create/edit/delete')
        <a href="{{ route('systems.integrations.create', $system) }}"
           class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nueva Integración
        </a>
        @endcan
    </div>

    @if($system->integrationsFrom->isEmpty() && $system->integrationsTo->isEmpty())
    <div class="text-center py-12 text-gray-400">
        <svg class="mx-auto w-10 h-10 text-gray-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
        </svg>
        <p class="text-sm">No hay integraciones registradas.</p>
        @can('integrations.create/edit/delete')
        <a href="{{ route('systems.integrations.create', $system) }}" class="mt-2 inline-block text-sm text-blue-600 hover:underline">Registrar integración →</a>
        @endcan
    </div>
    @else

    {{-- Salientes (este sistema → otro) --}}
    @if($system->integrationsFrom->isNotEmpty())
    <div>
        <p class="text-xs font-medium text-gray-500 mb-2 flex items-center gap-1.5">
            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
            Este sistema se integra con...
        </p>
        <div class="space-y-2">
            @foreach($system->integrationsFrom as $integration)
            @php
            $typeLabel = match($integration->connection_type) {
                'api'       => 'API',
                'direct_db' => 'BD directa',
                'file'      => 'Archivo',
                'sftp'      => 'SFTP',
                default     => ucfirst($integration->connection_type),
            };
            @endphp
            <div class="bg-white rounded-lg border border-gray-200 p-3 flex items-center gap-3 shadow-sm">
                <div class="flex-shrink-0 w-9 h-9 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-xs font-bold">
                    {{ strtoupper(substr($integration->targetSystem->acronym ?? $integration->targetSystem->name, 0, 2)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 truncate">{{ $integration->targetSystem->name }}</p>
                    <div class="flex items-center gap-2 mt-0.5">
                        <span class="text-xs text-gray-400">{{ $typeLabel }}</span>
                        <span class="inline-flex items-center gap-1 text-xs {{ $integration->is_active ? 'text-green-600' : 'text-gray-400' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $integration->is_active ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                            {{ $integration->is_active ? 'Activa' : 'Inactiva' }}
                        </span>
                    </div>
                </div>
                @can('integrations.create/edit/delete')
                <div class="flex items-center gap-1">
                    <a href="{{ route('systems.integrations.edit', [$system, $integration]) }}"
                       class="inline-flex items-center justify-center w-7 h-7 rounded text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition-all">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                    </a>
                    <form action="{{ route('systems.integrations.destroy', [$system, $integration]) }}" method="POST" class="inline">
                        @csrf @method('DELETE')
                        <button type="button" x-data @click.prevent="if(confirm('¿Eliminar esta integración?')) $el.closest('form').submit()"
                                class="inline-flex items-center justify-center w-7 h-7 rounded text-gray-400 hover:text-red-600 hover:bg-red-50 transition-all">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </form>
                </div>
                @endcan
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Entrantes (otro → este sistema) --}}
    @if($system->integrationsTo->isNotEmpty())
    <div>
        <p class="text-xs font-medium text-gray-500 mb-2 flex items-center gap-1.5">
            <span class="w-2 h-2 rounded-full bg-green-500"></span>
            Sistemas que se integran con este...
        </p>
        <div class="space-y-2">
            @foreach($system->integrationsTo as $integration)
            <div class="bg-gray-50 rounded-lg border border-gray-200 p-3 flex items-center gap-3">
                <div class="flex-shrink-0 w-9 h-9 rounded-lg bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center text-white text-xs font-bold">
                    {{ strtoupper(substr($integration->sourceSystem->acronym ?? $integration->sourceSystem->name, 0, 2)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 truncate">{{ $integration->sourceSystem->name }}</p>
                    <span class="text-xs text-gray-400">
                        {{ match($integration->connection_type) { 'api' => 'API', 'direct_db' => 'BD directa', 'file' => 'Archivo', 'sftp' => 'SFTP', default => ucfirst($integration->connection_type) } }}
                    </span>
                </div>
                <span class="inline-flex items-center gap-1 text-xs {{ $integration->is_active ? 'text-green-600' : 'text-gray-400' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $integration->is_active ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                    {{ $integration->is_active ? 'Activa' : 'Inactiva' }}
                </span>
            </div>
            @endforeach
        </div>
    </div>
    @endif
    @endif
</div>
