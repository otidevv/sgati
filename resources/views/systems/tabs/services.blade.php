<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h3 class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">
            Servicios / APIs ({{ $system->services->count() }})
        </h3>
        @can('services.create/edit/delete')
        <a href="{{ route('systems.services.create', $system) }}"
           class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/30 hover:bg-blue-100 dark:hover:bg-blue-900/50 rounded-lg transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nuevo Servicio
        </a>
        @endcan
    </div>

    @forelse($system->services as $service)
    @php
    $typeColors = match($service->service_type) {
        'rest_api'  => 'bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-300 ring-green-200 dark:ring-green-800',
        'soap'      => 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 ring-blue-200 dark:ring-blue-800',
        'sftp'      => 'bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 ring-purple-200 dark:ring-purple-800',
        'smtp'      => 'bg-orange-50 dark:bg-orange-900/30 text-orange-700 dark:text-orange-300 ring-orange-200 dark:ring-orange-800',
        'ldap'      => 'bg-yellow-50 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300 ring-yellow-200 dark:ring-yellow-800',
        'database'  => 'bg-slate-50 dark:bg-slate-700/30 text-slate-700 dark:text-slate-300 ring-slate-200 dark:ring-slate-600',
        default     => 'bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300 ring-gray-200 dark:ring-gray-600',
    };
    $directionLabel = $service->direction === 'consumed' ? 'Consumido' : 'Expuesto';
    $directionColors = $service->direction === 'consumed'
        ? 'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/30'
        : 'text-green-600 dark:text-green-400 bg-green-50 dark:bg-green-900/30';
    $fieldCount = $service->fields->count();
    $docCount   = $service->documents->count();
    @endphp
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 shadow-sm hover:shadow-md dark:hover:shadow-gray-700/50 transition-shadow">
        <div class="flex items-start justify-between gap-3">
            <div class="flex items-center gap-2 flex-wrap">
                {{-- Nombre clickeable --}}
                <a href="{{ route('systems.services.show', [$system, $service]) }}"
                   class="text-sm font-semibold text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                    {{ $service->service_name }}
                </a>
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium ring-1 {{ $typeColors }}">
                    {{ strtoupper(str_replace('_', ' ', $service->service_type)) }}
                </span>
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $directionColors }}">
                    {{ $directionLabel }}
                </span>
                <span class="inline-flex items-center gap-1 text-xs {{ $service->is_active ? 'text-green-600 dark:text-green-400' : 'text-gray-400 dark:text-gray-500' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $service->is_active ? 'bg-green-500' : 'bg-gray-400 dark:bg-gray-500' }}"></span>
                    {{ $service->is_active ? 'Activo' : 'Inactivo' }}
                </span>
            </div>
            <div class="flex items-center gap-1 flex-shrink-0">
                {{-- Ver detalle --}}
                <a href="{{ route('systems.services.show', [$system, $service]) }}"
                   title="Ver detalle"
                   class="inline-flex items-center justify-center w-7 h-7 rounded text-gray-400 dark:text-gray-500 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition-all">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </a>
                @can('services.create/edit/delete')
                <a href="{{ route('systems.services.edit', [$system, $service]) }}"
                   title="Editar"
                   class="inline-flex items-center justify-center w-7 h-7 rounded text-gray-400 dark:text-gray-500 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-all">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                </a>
                <form action="{{ route('systems.services.destroy', [$system, $service]) }}" method="POST" class="inline">
                    @csrf @method('DELETE')
                    <button type="button" x-data @click.prevent="sgDeleteForm($el.closest('form'), '¿Eliminar el servicio {{ addslashes($service->service_name) }}?')"
                            title="Eliminar"
                            class="inline-flex items-center justify-center w-7 h-7 rounded text-gray-400 dark:text-gray-500 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 transition-all">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </form>
                @endcan
            </div>
        </div>

        @if($service->endpoint_url)
        <div class="mt-2">
            <span class="text-xs font-mono text-blue-600 dark:text-blue-400 break-all">{{ $service->endpoint_url }}</span>
        </div>
        @endif

        {{-- Meta: env, auth, campos, documentos --}}
        <div class="mt-2 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-gray-400 dark:text-gray-500">
            @if($service->environment)
            <span class="inline-flex items-center gap-1">
                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M12 5l7 7-7 7"/></svg>
                {{ ucfirst($service->environment) }}
            </span>
            @endif
            @if($service->auth_type)
            <span>Auth: <span class="text-gray-600 dark:text-gray-300">{{ $service->auth_type }}</span></span>
            @endif
            @if($fieldCount > 0)
            <a href="{{ route('systems.services.show', [$system, $service]) }}"
               class="inline-flex items-center gap-1 text-indigo-500 dark:text-indigo-400 hover:underline">
                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h8"/></svg>
                {{ $fieldCount }} {{ $fieldCount === 1 ? 'campo' : 'campos' }}
            </a>
            @endif
            @if($docCount > 0)
            <a href="{{ route('systems.services.show', [$system, $service]) }}"
               class="inline-flex items-center gap-1 text-amber-500 dark:text-amber-400 hover:underline">
                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                {{ $docCount }} {{ $docCount === 1 ? 'doc' : 'docs' }}
            </a>
            @endif
            @if($service->description)
            <span class="text-gray-400 dark:text-gray-500 truncate max-w-xs">{{ $service->description }}</span>
            @endif
        </div>
    </div>
    @empty
    <div class="text-center py-12 text-gray-400 dark:text-gray-500">
        <svg class="mx-auto w-10 h-10 text-gray-300 dark:text-gray-600 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        <p class="text-sm">No hay servicios o APIs registrados.</p>
        @can('services.create/edit/delete')
        <a href="{{ route('systems.services.create', $system) }}" class="mt-2 inline-block text-sm text-blue-600 dark:text-blue-400 hover:underline">Registrar servicio →</a>
        @endcan
    </div>
    @endforelse
</div>
