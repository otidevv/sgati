<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">
            Servicios / APIs ({{ $system->services->count() }})
        </h3>
        @can('services.create/edit/delete')
        <a href="{{ route('systems.services.create', $system) }}"
           class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors">
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
        'rest_api'  => 'bg-green-50 text-green-700 ring-green-200',
        'soap'      => 'bg-blue-50 text-blue-700 ring-blue-200',
        'sftp'      => 'bg-purple-50 text-purple-700 ring-purple-200',
        'smtp'      => 'bg-orange-50 text-orange-700 ring-orange-200',
        'ldap'      => 'bg-yellow-50 text-yellow-700 ring-yellow-200',
        'database'  => 'bg-slate-50 text-slate-700 ring-slate-200',
        default     => 'bg-gray-50 text-gray-700 ring-gray-200',
    };
    $directionLabel = $service->direction === 'consumed' ? 'Consumido' : 'Expuesto';
    $directionColors = $service->direction === 'consumed' ? 'text-blue-600 bg-blue-50' : 'text-green-600 bg-green-50';
    @endphp
    <div class="bg-white rounded-lg border border-gray-200 p-4 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-start justify-between gap-3">
            <div class="flex items-center gap-2 flex-wrap">
                <span class="text-sm font-semibold text-gray-900">{{ $service->service_name }}</span>
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium ring-1 {{ $typeColors }}">
                    {{ strtoupper(str_replace('_', ' ', $service->service_type)) }}
                </span>
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $directionColors }}">
                    {{ $directionLabel }}
                </span>
                <span class="inline-flex items-center gap-1 text-xs {{ $service->is_active ? 'text-green-600' : 'text-gray-400' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $service->is_active ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                    {{ $service->is_active ? 'Activo' : 'Inactivo' }}
                </span>
            </div>
            @can('services.create/edit/delete')
            <div class="flex items-center gap-1 flex-shrink-0">
                <a href="{{ route('systems.services.edit', [$system, $service]) }}"
                   class="inline-flex items-center justify-center w-7 h-7 rounded text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition-all">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                </a>
                <form action="{{ route('systems.services.destroy', [$system, $service]) }}" method="POST" class="inline">
                    @csrf @method('DELETE')
                    <button type="button" x-data @click.prevent="if(confirm('¿Eliminar el servicio {{ addslashes($service->service_name) }}?')) $el.closest('form').submit()"
                            class="inline-flex items-center justify-center w-7 h-7 rounded text-gray-400 hover:text-red-600 hover:bg-red-50 transition-all">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </form>
            </div>
            @endcan
        </div>
        @if($service->endpoint_url)
        <div class="mt-2">
            <a href="{{ $service->endpoint_url }}" target="_blank"
               class="text-xs font-mono text-blue-600 hover:underline break-all">{{ $service->endpoint_url }}</a>
        </div>
        @endif
        <div class="mt-2 flex flex-wrap gap-x-4 text-xs text-gray-400">
            @if($service->auth_type)
            <span>Auth: <span class="text-gray-600">{{ $service->auth_type }}</span></span>
            @endif
            @if($service->description)
            <span class="text-gray-500">{{ $service->description }}</span>
            @endif
        </div>
    </div>
    @empty
    <div class="text-center py-12 text-gray-400">
        <svg class="mx-auto w-10 h-10 text-gray-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        <p class="text-sm">No hay servicios o APIs registrados.</p>
        @can('services.create/edit/delete')
        <a href="{{ route('systems.services.create', $system) }}" class="mt-2 inline-block text-sm text-blue-600 hover:underline">Registrar servicio →</a>
        @endcan
    </div>
    @endforelse
</div>
