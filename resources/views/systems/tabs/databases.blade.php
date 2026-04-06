<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">
            Bases de Datos ({{ $system->databases->count() }})
        </h3>
        @can('databases.create/edit/delete')
        <a href="{{ route('systems.databases.create', $system) }}"
           class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors">
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
        'postgresql'  => 'bg-blue-50 text-blue-700 ring-blue-200',
        'mysql'       => 'bg-orange-50 text-orange-700 ring-orange-200',
        'oracle'      => 'bg-red-50 text-red-700 ring-red-200',
        'sqlserver'   => 'bg-slate-50 text-slate-700 ring-slate-200',
        'mongodb'     => 'bg-green-50 text-green-700 ring-green-200',
        'sqlite'      => 'bg-purple-50 text-purple-700 ring-purple-200',
        default       => 'bg-gray-50 text-gray-700 ring-gray-200',
    };
    @endphp
    <div class="bg-white rounded-lg border border-gray-200 p-4 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-start justify-between gap-3">
            <div class="flex items-center gap-2 flex-wrap">
                <span class="text-sm font-semibold text-gray-900 font-mono">{{ $db->db_name }}</span>
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium ring-1 {{ $engineColors }}">
                    {{ strtoupper($db->engine) }}
                </span>
                @if($db->environment)
                <span class="text-xs text-gray-400">{{ $db->environment->label() }}</span>
                @endif
            </div>
            @can('databases.create/edit/delete')
            <div class="flex items-center gap-1 flex-shrink-0">
                <a href="{{ route('systems.databases.edit', [$system, $db]) }}"
                   class="inline-flex items-center justify-center w-7 h-7 rounded text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition-all">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                </a>
                <form action="{{ route('systems.databases.destroy', [$system, $db]) }}" method="POST" class="inline">
                    @csrf @method('DELETE')
                    <button type="button" x-data @click.prevent="if(confirm('¿Eliminar la base de datos {{ addslashes($db->db_name) }}?')) $el.closest('form').submit()"
                            class="inline-flex items-center justify-center w-7 h-7 rounded text-gray-400 hover:text-red-600 hover:bg-red-50 transition-all">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </form>
            </div>
            @endcan
        </div>
        <div class="mt-2.5 grid grid-cols-2 sm:grid-cols-4 gap-2 text-xs">
            @if($db->server_host)
            <div><span class="text-gray-400">Host</span><p class="font-mono text-gray-700 mt-0.5">{{ $db->server_host }}{{ $db->port ? ':' . $db->port : '' }}</p></div>
            @endif
            @if($db->schema_name)
            <div><span class="text-gray-400">Schema</span><p class="font-mono text-gray-700 mt-0.5">{{ $db->schema_name }}</p></div>
            @endif
            @if($db->responsible)
            <div><span class="text-gray-400">Responsable</span><p class="text-gray-700 mt-0.5">{{ $db->responsible }}</p></div>
            @endif
        </div>
        @if($db->notes)
        <p class="mt-2 text-xs text-gray-400 italic">{{ $db->notes }}</p>
        @endif
    </div>
    @empty
    <div class="text-center py-12 text-gray-400">
        <svg class="mx-auto w-10 h-10 text-gray-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
        </svg>
        <p class="text-sm">No hay bases de datos registradas.</p>
        @can('databases.create/edit/delete')
        <a href="{{ route('systems.databases.create', $system) }}" class="mt-2 inline-block text-sm text-blue-600 hover:underline">Registrar base de datos →</a>
        @endcan
    </div>
    @endforelse
</div>
