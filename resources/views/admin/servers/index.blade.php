@extends('layouts.app')

@section('title', 'Servidores')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Servidores</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Infraestructura física y virtual de la OTI</p>
        </div>
        <a href="{{ route('admin.servers.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white
                  text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nuevo Servidor
        </a>
    </div>

    {{-- Tarjetas resumen --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        @foreach([
            ['label' => 'Total',      'value' => $servers->count(),                          'color' => 'text-gray-900 dark:text-white'],
            ['label' => 'Activos',    'value' => $servers->where('is_active', true)->count(),  'color' => 'text-emerald-600 dark:text-emerald-400'],
            ['label' => 'Inactivos',  'value' => $servers->where('is_active', false)->count(), 'color' => 'text-red-500 dark:text-red-400'],
            ['label' => 'Contenedores','value' => $servers->sum('active_containers_count'),    'color' => 'text-blue-600 dark:text-blue-400'],
        ] as $card)
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 px-4 py-3">
            <p class="text-[10px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">{{ $card['label'] }}</p>
            <p class="mt-1 text-2xl font-bold {{ $card['color'] }}">{{ $card['value'] }}</p>
        </div>
        @endforeach
    </div>

    <x-data-table id="servers-table" search-placeholder="Buscar servidor…">

        <x-slot:thead>
            <th data-col="0" class="tbl-sort px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer select-none group">
                <div class="flex items-center gap-1.5">Servidor <span class="sort-icon text-gray-300 dark:text-gray-600 group-hover:text-gray-400"></span></div>
            </th>
            <th data-col="1" class="tbl-sort px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer select-none group">
                <div class="flex items-center gap-1.5">IPs <span class="sort-icon text-gray-300 dark:text-gray-600 group-hover:text-gray-400"></span></div>
            </th>
            <th data-col="2" class="tbl-sort px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer select-none group">
                <div class="flex items-center gap-1.5">Sistema Operativo <span class="sort-icon text-gray-300 dark:text-gray-600 group-hover:text-gray-400"></span></div>
            </th>
            <th data-col="3" class="tbl-sort px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer select-none group">
                <div class="flex items-center gap-1.5">Función <span class="sort-icon text-gray-300 dark:text-gray-600 group-hover:text-gray-400"></span></div>
            </th>
            <th data-col="4" class="tbl-sort px-5 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer select-none group">
                <div class="flex items-center justify-center gap-1.5">Sistemas <span class="sort-icon text-gray-300 dark:text-gray-600 group-hover:text-gray-400"></span></div>
            </th>
            <th data-col="5" class="tbl-sort px-5 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer select-none group">
                <div class="flex items-center justify-center gap-1.5">Estado <span class="sort-icon text-gray-300 dark:text-gray-600 group-hover:text-gray-400"></span></div>
            </th>
            <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Acciones</th>
        </x-slot:thead>

        <x-slot:tbody>
            @forelse($servers as $server)
            @php $fn = $server->function; @endphp
            <tr class="tbl-row hover:bg-gray-50 dark:hover:bg-gray-700/40 transition-colors">

                {{-- Servidor --}}
                <td class="px-5 py-3.5">
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0 w-9 h-9 rounded-lg
                                    bg-gradient-to-br from-slate-600 to-slate-800
                                    flex items-center justify-center text-white font-bold text-xs">
                            {{ strtoupper(substr($server->name, 0, 2)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $server->name }}</p>
                            @if($server->installed_services)
                            <p class="text-xs text-gray-400 dark:text-gray-500 truncate max-w-[160px]">
                                {{ implode(', ', $server->installed_services) }}
                            </p>
                            @endif
                        </div>
                    </div>
                </td>

                {{-- IPs --}}
                <td class="px-5 py-3.5">
                    <div class="space-y-1">
                        @forelse($server->ips->where('type','private') as $ip)
                        <div class="flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400 flex-shrink-0"></span>
                            <span class="text-xs font-mono text-gray-600 dark:text-gray-300">{{ $ip->ip_address }}</span>
                        </div>
                        @empty @endforelse
                        @forelse($server->ips->where('type','public') as $ip)
                        <div class="flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-400 flex-shrink-0"></span>
                            <span class="text-xs font-mono text-gray-600 dark:text-gray-300">{{ $ip->ip_address }}</span>
                        </div>
                        @empty @endforelse
                        @if($server->ips->isEmpty())
                        <span class="text-gray-300 dark:text-gray-600 text-xs">—</span>
                        @endif
                    </div>
                </td>

                {{-- SO --}}
                <td class="px-5 py-3.5">
                    <span class="text-sm text-gray-600 dark:text-gray-300">{{ $server->operating_system ?? '—' }}</span>
                </td>

                {{-- Función --}}
                <td class="px-5 py-3.5">
                    @if($fn)
                    @php $color = $fn->color(); @endphp
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium
                                 bg-{{ $color }}-100 dark:bg-{{ $color }}-900/30
                                 text-{{ $color }}-700 dark:text-{{ $color }}-400">
                        {{ $fn->label() }}
                    </span>
                    @endif
                </td>

                {{-- Sistemas --}}
                <td class="px-5 py-3.5 text-center">
                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg
                                 bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400
                                 text-xs font-semibold">
                        {{ $server->deployments_count }}
                    </span>
                </td>

                {{-- Estado --}}
                <td class="px-5 py-3.5 text-center">
                    @if($server->is_active)
                    <span class="inline-flex items-center gap-1.5 text-xs font-medium text-emerald-700 dark:text-emerald-400">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>Activo
                    </span>
                    @else
                    <span class="inline-flex items-center gap-1.5 text-xs font-medium text-gray-400 dark:text-gray-500">
                        <span class="w-1.5 h-1.5 rounded-full bg-gray-300 dark:bg-gray-600"></span>Inactivo
                    </span>
                    @endif
                </td>

                {{-- Acciones --}}
                <td class="px-5 py-3.5 text-right">
                    <div class="inline-flex items-center gap-1">

                        {{-- Conectar via Guacamole --}}
                        @if($server->guacamole_connection_id)
                        <button type="button" title="Escritorio remoto (RDP)"
                                onclick="guacConnect(this, '{{ route('admin.servers.connect', $server) }}')"
                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg
                                       text-gray-400 dark:text-gray-500
                                       hover:text-green-600 dark:hover:text-green-400
                                       hover:bg-green-50 dark:hover:bg-green-900/30 transition-all">
                            {{-- monitor icon --}}
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </button>
                        @else
                        <span title="Sin conexión Guacamole"
                              class="inline-flex items-center justify-center w-8 h-8 rounded-lg
                                     text-gray-200 dark:text-gray-700 cursor-not-allowed">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </span>
                        @endif

                        <a href="{{ route('admin.servers.show', $server) }}" title="Ver detalle"
                           class="inline-flex items-center justify-center w-8 h-8 rounded-lg
                                  text-gray-400 dark:text-gray-500
                                  hover:text-indigo-600 dark:hover:text-indigo-400
                                  hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition-all">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </a>
                        <a href="{{ route('admin.servers.edit', $server) }}" title="Editar"
                           class="inline-flex items-center justify-center w-8 h-8 rounded-lg
                                  text-gray-400 dark:text-gray-500
                                  hover:text-blue-600 dark:hover:text-blue-400
                                  hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-all">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                            </svg>
                        </a>
                        <form id="del-server-{{ $server->id }}"
                              action="{{ route('admin.servers.destroy', $server) }}"
                              method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button type="button" title="Eliminar"
                                    onclick="dtConfirmDelete('del-server-{{ $server->id }}', '{{ addslashes($server->name) }}')"
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg
                                           text-gray-400 dark:text-gray-500
                                           hover:text-red-600 dark:hover:text-red-400
                                           hover:bg-red-50 dark:hover:bg-red-900/30 transition-all">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            @endforelse
        </x-slot:tbody>

        @if($servers->isEmpty())
        <x-slot:empty>
            <div class="text-center py-16">
                <div class="mx-auto w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                    <svg class="w-8 h-8 text-gray-300 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/>
                    </svg>
                </div>
                <h3 class="mt-4 text-sm font-semibold text-gray-900 dark:text-white">Sin servidores registrados</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Registra el primer servidor de la OTI.</p>
                <a href="{{ route('admin.servers.create') }}"
                   class="mt-6 inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white
                          text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Registrar servidor
                </a>
            </div>
        </x-slot:empty>
        @endif

    </x-data-table>

</div>
@endsection

@push('scripts')
<script>
async function guacConnect(btn, url) {
    // Deshabilitar botón y mostrar spinner
    btn.disabled = true;
    const original = btn.innerHTML;
    btn.innerHTML = `<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
    </svg>`;

    try {
        const res  = await fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
        const data = await res.json();

        if (data.url) {
            window.open(data.url, '_blank', 'noopener,noreferrer');
        } else {
            alert('Error al conectar: ' + (data.error ?? 'Respuesta inesperada'));
        }
    } catch (e) {
        alert('No se pudo contactar al servidor. Verifica la configuración de Guacamole.');
    } finally {
        btn.disabled = false;
        btn.innerHTML = original;
    }
}
</script>
@endpush
