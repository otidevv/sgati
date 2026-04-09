<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h3 class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">
            Bases de Datos ({{ $system->databases->count() }})
        </h3>
        @can('databases.create/edit/delete')
        <a href="{{ route('systems.databases.create', $system) }}"
           class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/30 hover:bg-blue-100 dark:hover:bg-blue-900/50 rounded-lg transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nueva Base de Datos
        </a>
        @endcan
    </div>

    @forelse($system->databases as $db)
    @php
    $engineColors = match($db->engine) {
        'postgresql'  => 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 ring-blue-200 dark:ring-blue-800',
        'mysql'       => 'bg-orange-50 dark:bg-orange-900/30 text-orange-700 dark:text-orange-300 ring-orange-200 dark:ring-orange-800',
        'oracle'      => 'bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-300 ring-red-200 dark:ring-red-800',
        'sqlserver'   => 'bg-slate-50 dark:bg-slate-700/30 text-slate-700 dark:text-slate-300 ring-slate-200 dark:ring-slate-600',
        'mongodb'     => 'bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-300 ring-green-200 dark:ring-green-800',
        'sqlite'      => 'bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 ring-purple-200 dark:ring-purple-800',
        default       => 'bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300 ring-gray-200 dark:ring-gray-600',
    };
    @endphp
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 shadow-sm hover:shadow-md dark:hover:shadow-gray-700/50 transition-shadow">
        <div class="flex items-start justify-between gap-3">
            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('systems.databases.show', [$system, $db]) }}"
                   class="text-sm font-semibold text-gray-900 dark:text-white font-mono hover:text-blue-600 dark:hover:text-blue-400 hover:underline">
                    {{ $db->db_name }}
                </a>
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium ring-1 {{ $engineColors }}">
                    {{ strtoupper($db->engine) }}
                </span>
                @if($db->environment)
                <span class="text-xs text-gray-400 dark:text-gray-500">{{ $db->environment->label() }}</span>
                @endif
                @php $activeCount = $db->responsibles->where('is_active', true)->count(); @endphp
                @if($db->responsibles->count())
                <a href="{{ route('systems.databases.show', [$system, $db]) }}"
                   class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-xs font-medium
                          {{ $activeCount ? 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400' : 'bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400' }}">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    {{ $activeCount }}
                </a>
                @endif
            </div>
            @can('databases.create/edit/delete')
            <div class="flex items-center gap-1 flex-shrink-0">
                <a href="{{ route('systems.databases.show', [$system, $db]) }}"
                   class="inline-flex items-center justify-center w-7 h-7 rounded text-gray-400 dark:text-gray-500 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 transition-all"
                   title="Ver / gestionar responsables">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </a>
                <a href="{{ route('systems.databases.edit', [$system, $db]) }}"
                   class="inline-flex items-center justify-center w-7 h-7 rounded text-gray-400 dark:text-gray-500 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-all">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                </a>
                <form action="{{ route('systems.databases.destroy', [$system, $db]) }}" method="POST" class="inline">
                    @csrf @method('DELETE')
                    <button type="button" x-data @click.prevent="sgDeleteForm($el.closest('form'), '¿Eliminar la base de datos {{ addslashes($db->db_name) }}?')"
                            class="inline-flex items-center justify-center w-7 h-7 rounded text-gray-400 dark:text-gray-500 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 transition-all">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </form>
            </div>
            @endcan
        </div>
        <div class="mt-2.5 grid grid-cols-2 sm:grid-cols-4 gap-2 text-xs">
            @if($db->databaseServer)
            <div>
                <span class="text-gray-400 dark:text-gray-500">Motor BD</span>
                <p class="font-mono text-gray-700 dark:text-gray-300 mt-0.5">{{ $db->databaseServer->engine_label }}</p>
            </div>
            <div>
                <span class="text-gray-400 dark:text-gray-500">Host</span>
                <p class="font-mono text-gray-700 dark:text-gray-300 mt-0.5">{{ $db->databaseServer->connection_string }}</p>
            </div>
            @endif
            @if($db->schema_name)
            <div><span class="text-gray-400 dark:text-gray-500">Schema</span><p class="font-mono text-gray-700 dark:text-gray-300 mt-0.5">{{ $db->schema_name }}</p></div>
            @endif
        </div>
        @if($db->notes)
        <p class="mt-2 text-xs text-gray-400 dark:text-gray-500 italic">{{ $db->notes }}</p>
        @endif
    </div>
    @empty
    <div class="text-center py-12 text-gray-400 dark:text-gray-500">
        <svg class="mx-auto w-10 h-10 text-gray-300 dark:text-gray-600 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
        </svg>
        <p class="text-sm">No hay bases de datos registradas.</p>
        @can('databases.create/edit/delete')
        <a href="{{ route('systems.databases.create', $system) }}" class="mt-2 inline-block text-sm text-blue-600 dark:text-blue-400 hover:underline">Registrar base de datos →</a>
        @endcan
    </div>
    @endforelse
</div>
