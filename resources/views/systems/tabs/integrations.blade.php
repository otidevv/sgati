<div class="space-y-4">

    {{-- ── Banner de ayuda ── --}}
    <div x-data="{ open: localStorage.getItem('help_integrations') !== '0' }" x-init="$watch('open', v => localStorage.setItem('help_integrations', v ? '1' : '0'))">
        <div x-show="open" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="rounded-xl border border-violet-200 dark:border-violet-700/50 bg-violet-50 dark:bg-violet-900/20 p-4 mb-1">
            <div class="flex items-start gap-3">
                <div class="flex-shrink-0 w-8 h-8 rounded-lg bg-violet-100 dark:bg-violet-900/50 flex items-center justify-center mt-0.5">
                    <svg class="w-4 h-4 text-violet-600 dark:text-violet-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-violet-800 dark:text-violet-200">¿Para qué sirven las Integraciones?</p>
                    <p class="mt-1 text-xs text-violet-700 dark:text-violet-300 leading-relaxed">
                        Documenta las <strong>conexiones entre sistemas</strong>. Permite saber qué otros sistemas dependen de este o de cuáles depende este sistema para funcionar.
                        Es clave para evaluar el impacto antes de hacer cambios o dar de baja un sistema.
                    </p>
                    <div class="mt-2.5 grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <div class="flex items-start gap-2 p-2 rounded-lg bg-white/60 dark:bg-gray-800/40 border border-violet-100 dark:border-violet-700/30">
                            <span class="w-2 h-2 rounded-full bg-blue-500 flex-shrink-0 mt-1"></span>
                            <div>
                                <p class="text-[11px] font-semibold text-violet-800 dark:text-violet-200">Salientes — este sistema se integra con...</p>
                                <p class="text-[11px] text-violet-600 dark:text-violet-400">
                                    Ej: el sistema de Matrícula <em>consume</em> datos del sistema de Pagos.
                                    Registra aquí los sistemas externos o internos que este usa.
                                </p>
                            </div>
                        </div>
                        <div class="flex items-start gap-2 p-2 rounded-lg bg-white/60 dark:bg-gray-800/40 border border-violet-100 dark:border-violet-700/30">
                            <span class="w-2 h-2 rounded-full bg-green-500 flex-shrink-0 mt-1"></span>
                            <div>
                                <p class="text-[11px] font-semibold text-violet-800 dark:text-violet-200">Entrantes — sistemas que dependen de este</p>
                                <p class="text-[11px] text-violet-600 dark:text-violet-400">
                                    Se registran automáticamente cuando otro sistema declara que se integra con este.
                                    No necesitas crearlas manualmente.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-2 flex flex-wrap gap-2">
                        @foreach(['API REST', 'Base de datos directa', 'Archivo / SFTP', 'SMTP'] as $tipo)
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-white/80 dark:bg-gray-800/60 border border-violet-200 dark:border-violet-700/40 text-violet-700 dark:text-violet-300">
                            {{ $tipo }}
                        </span>
                        @endforeach
                        <span class="text-[11px] text-violet-500 dark:text-violet-400 self-center">← tipos de conexión disponibles</span>
                    </div>
                </div>
                <button @click="open = false" title="Cerrar ayuda"
                        class="flex-shrink-0 w-6 h-6 flex items-center justify-center rounded text-violet-400 hover:text-violet-600 dark:hover:text-violet-300 hover:bg-violet-100 dark:hover:bg-violet-900/40 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <div x-show="!open" class="flex justify-end mb-1">
            <button @click="open = true"
                    class="inline-flex items-center gap-1 text-[11px] text-gray-400 dark:text-gray-500 hover:text-violet-500 dark:hover:text-violet-400 transition-colors">
                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                ¿Para qué sirve esto?
            </button>
        </div>
    </div>

    <div class="flex items-center justify-between">
        <h3 class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">
            Integraciones ({{ $system->integrationsFrom->count() + $system->integrationsTo->count() }})
        </h3>
        @can('integrations.create/edit/delete')
        <a href="{{ route('systems.integrations.create', $system) }}"
           class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/30 hover:bg-blue-100 dark:hover:bg-blue-900/50 rounded-lg transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nueva Integración
        </a>
        @endcan
    </div>

    @if($system->integrationsFrom->isEmpty() && $system->integrationsTo->isEmpty())
    <div class="text-center py-12 text-gray-400 dark:text-gray-500">
        <svg class="mx-auto w-10 h-10 text-gray-300 dark:text-gray-600 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
        </svg>
        <p class="text-sm">No hay integraciones registradas.</p>
        @can('integrations.create/edit/delete')
        <a href="{{ route('systems.integrations.create', $system) }}" class="mt-2 inline-block text-sm text-blue-600 dark:text-blue-400 hover:underline">Registrar integración →</a>
        @endcan
    </div>
    @else

    {{-- Salientes (este sistema → otro) --}}
    @if($system->integrationsFrom->isNotEmpty())
    <div>
        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-2 flex items-center gap-1.5">
            <span class="w-2 h-2 rounded-full bg-blue-500 dark:bg-blue-400"></span>
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
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-3 flex items-center gap-3 shadow-sm">
                <div class="flex-shrink-0 w-9 h-9 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-xs font-bold">
                    {{ strtoupper(substr($integration->targetSystem->acronym ?? $integration->targetSystem->name, 0, 2)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $integration->targetSystem->name }}</p>
                    <div class="flex items-center gap-2 mt-0.5">
                        <span class="text-xs text-gray-400 dark:text-gray-500">{{ $typeLabel }}</span>
                        <span class="inline-flex items-center gap-1 text-xs {{ $integration->is_active ? 'text-green-600 dark:text-green-400' : 'text-gray-400 dark:text-gray-500' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $integration->is_active ? 'bg-green-500' : 'bg-gray-400 dark:bg-gray-500' }}"></span>
                            {{ $integration->is_active ? 'Activa' : 'Inactiva' }}
                        </span>
                    </div>
                </div>
                @can('integrations.create/edit/delete')
                <div class="flex items-center gap-1">
                    <a href="{{ route('systems.integrations.edit', [$system, $integration]) }}"
                       class="inline-flex items-center justify-center w-7 h-7 rounded text-gray-400 dark:text-gray-500 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-all">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                    </a>
                    <form action="{{ route('systems.integrations.destroy', [$system, $integration]) }}" method="POST" class="inline">
                        @csrf @method('DELETE')
                        <button type="button" x-data @click.prevent="sgDeleteForm($el.closest('form'), '¿Eliminar esta integración?')"
                                class="inline-flex items-center justify-center w-7 h-7 rounded text-gray-400 dark:text-gray-500 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 transition-all">
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
        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-2 flex items-center gap-1.5">
            <span class="w-2 h-2 rounded-full bg-green-500 dark:bg-green-400"></span>
            Sistemas que se integran con este...
        </p>
        <div class="space-y-2">
            @foreach($system->integrationsTo as $integration)
            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-700 p-3 flex items-center gap-3">
                <div class="flex-shrink-0 w-9 h-9 rounded-lg bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center text-white text-xs font-bold">
                    {{ strtoupper(substr($integration->sourceSystem->acronym ?? $integration->sourceSystem->name, 0, 2)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $integration->sourceSystem->name }}</p>
                    <span class="text-xs text-gray-400 dark:text-gray-500">
                        {{ match($integration->connection_type) { 'api' => 'API', 'direct_db' => 'BD directa', 'file' => 'Archivo', 'sftp' => 'SFTP', default => ucfirst($integration->connection_type) } }}
                    </span>
                </div>
                <span class="inline-flex items-center gap-1 text-xs {{ $integration->is_active ? 'text-green-600 dark:text-green-400' : 'text-gray-400 dark:text-gray-500' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $integration->is_active ? 'bg-green-500' : 'bg-gray-400 dark:bg-gray-500' }}"></span>
                    {{ $integration->is_active ? 'Activa' : 'Inactiva' }}
                </span>
            </div>
            @endforeach
        </div>
    </div>
    @endif
    @endif
</div>
