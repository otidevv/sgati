@extends('layouts.app')
@section('title', 'Sistemas')

@section('content')
<div class="space-y-5">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Sistemas de Información</h1>
            <p class="mt-1 text-sm text-gray-500">Inventario de sistemas registrados en UNAMAD</p>
        </div>
        @can('systems.create')
        <a href="{{ route('systems.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nuevo Sistema
        </a>
        @endcan
    </div>

    {{-- Filtros --}}
    <div class="flex flex-col sm:flex-row sm:items-center gap-3">
        {{-- Pills de estado --}}
        <div class="flex items-center gap-1.5 flex-wrap">
            <a href="{{ route('systems.index', request()->except(['status','page'])) }}"
               class="px-3 py-1 text-xs font-medium rounded-full border transition-colors
                      {{ !request('status') ? 'bg-gray-900 text-white border-gray-900' : 'bg-white text-gray-500 border-gray-200 hover:border-gray-300 hover:text-gray-700' }}">
                Todos
            </a>
            @foreach($statuses as $s)
            @php
            $active = request('status') === $s->value;
            $activeCls = match($s->value) {
                'active'      => 'bg-green-600 text-white border-green-600',
                'development' => 'bg-blue-600 text-white border-blue-600',
                'maintenance' => 'bg-yellow-500 text-white border-yellow-500',
                'inactive'    => 'bg-red-500 text-white border-red-500',
                default       => 'bg-gray-700 text-white border-gray-700',
            };
            @endphp
            <a href="{{ route('systems.index', array_merge(request()->except(['status','page']), ['status' => $s->value])) }}"
               class="px-3 py-1 text-xs font-medium rounded-full border transition-colors
                      {{ $active ? $activeCls : 'bg-white text-gray-500 border-gray-200 hover:border-gray-300 hover:text-gray-700' }}">
                {{ $s->label() }}
            </a>
            @endforeach
        </div>

        {{-- Área + búsqueda --}}
        <form method="GET" action="{{ route('systems.index') }}" class="flex gap-2 sm:ml-auto">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            <select name="area_id" onchange="this.form.submit()"
                    class="text-sm rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 py-1.5 pr-8">
                <option value="">Todas las áreas</option>
                @foreach($areas as $area)
                <option value="{{ $area->id }}" {{ request('area_id') == $area->id ? 'selected' : '' }}>
                    {{ $area->acronym ?? $area->name }}
                </option>
                @endforeach
            </select>
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Buscar sistema…"
                       class="pl-9 text-sm rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 py-1.5 w-44">
            </div>
        </form>
    </div>

    {{-- Contador --}}
    @if($systems->total() > 0)
    <p class="text-xs text-gray-400">{{ $systems->total() }} {{ $systems->total() === 1 ? 'sistema encontrado' : 'sistemas encontrados' }}</p>
    @endif

    {{-- Grid de cards --}}
    @if($systems->isEmpty())
    <div class="text-center py-20 bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="mx-auto w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center">
            <svg class="w-8 h-8 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
        </div>
        <h3 class="mt-4 text-sm font-semibold text-gray-900">No se encontraron sistemas</h3>
        <p class="mt-1 text-sm text-gray-500">
            {{ request()->hasAny(['status','area_id','search']) ? 'Prueba ajustando los filtros.' : 'Registra el primer sistema de información.' }}
        </p>
        @can('systems.create')
        <div class="mt-6">
            <a href="{{ route('systems.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Registrar Sistema
            </a>
        </div>
        @endcan
    </div>
    @else
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @foreach($systems as $system)
        @php
            $sv = $system->status->value;
            $borderColor = match($sv) {
                'active'      => 'border-l-green-500',
                'development' => 'border-l-blue-500',
                'maintenance' => 'border-l-yellow-400',
                'inactive'    => 'border-l-red-400',
                default       => 'border-l-gray-300',
            };
            $avatarBg = match($sv) {
                'active'      => 'from-green-500 to-emerald-600',
                'development' => 'from-blue-500 to-indigo-600',
                'maintenance' => 'from-yellow-400 to-orange-500',
                'inactive'    => 'from-gray-400 to-gray-500',
                default       => 'from-gray-400 to-gray-500',
            };
        @endphp
        <div class="bg-white rounded-xl border border-gray-200 border-l-4 {{ $borderColor }} shadow-sm hover:shadow-md transition-all duration-200 flex flex-col group">

            {{-- Card header --}}
            <div class="p-4 flex items-start gap-3">
                <div class="flex-shrink-0 w-11 h-11 rounded-lg bg-gradient-to-br {{ $avatarBg }} flex items-center justify-center text-white font-bold text-sm tracking-wide shadow-sm">
                    {{ strtoupper(substr($system->acronym ?? $system->name, 0, 3)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-sm font-semibold text-gray-900 truncate leading-snug">{{ $system->name }}</h3>
                    @if($system->acronym)
                    <p class="text-xs text-gray-400 mt-0.5 font-mono">{{ $system->acronym }}</p>
                    @endif
                </div>
                <x-status-badge :status="$system->status" class="flex-shrink-0 mt-0.5" />
            </div>

            {{-- Card body --}}
            <div class="px-4 pb-3 flex-1 space-y-2.5">
                @if($system->description)
                <p class="text-xs text-gray-500 line-clamp-2 leading-relaxed">{{ $system->description }}</p>
                @endif

                <div class="space-y-1.5">
                    @if($system->area)
                    <div class="flex items-center gap-1.5 text-xs text-gray-500">
                        <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <span class="truncate">{{ $system->area->name }}</span>
                    </div>
                    @endif
                    @if($system->responsible)
                    <div class="flex items-center gap-1.5 text-xs text-gray-500">
                        <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <span class="truncate">{{ $system->responsible->name }}</span>
                    </div>
                    @endif
                    @if($system->infrastructure?->system_url)
                    <div class="flex items-center gap-1.5 text-xs">
                        <svg class="w-3.5 h-3.5 flex-shrink-0 {{ $system->infrastructure->ssl_enabled ? 'text-green-500' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        <a href="{{ $system->infrastructure->system_url }}" target="_blank"
                           class="truncate {{ $system->infrastructure->ssl_enabled ? 'text-green-600' : 'text-gray-500' }} hover:underline">
                            {{ $system->infrastructure->system_url }}
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Card footer --}}
            <div class="px-4 py-2.5 border-t border-gray-100 flex items-center justify-between">
                <span class="text-xs text-gray-400">{{ $system->updated_at->diffForHumans() }}</span>
                <div class="flex items-center gap-0.5">
                    <a href="{{ route('systems.show', $system) }}"
                       class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-blue-600 hover:bg-blue-50 rounded-md transition-colors">
                        Ver detalle
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                    @can('systems.edit')
                    <a href="{{ route('systems.edit', $system) }}"
                       class="inline-flex items-center justify-center w-7 h-7 rounded text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition-all"
                       title="Editar">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                    </a>
                    @endcan
                    @can('systems.delete')
                    <form action="{{ route('systems.destroy', $system) }}" method="POST" class="inline">
                        @csrf @method('DELETE')
                        <button type="button" x-data
                                @click.prevent="if(confirm('¿Eliminar el sistema \'{{ addslashes($system->name) }}\'? Esta acción no se puede deshacer.')) $el.closest('form').submit()"
                                class="inline-flex items-center justify-center w-7 h-7 rounded text-gray-400 hover:text-red-600 hover:bg-red-50 transition-all"
                                title="Eliminar">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </form>
                    @endcan
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Paginación --}}
    @if($systems->hasPages())
    <div>{{ $systems->links() }}</div>
    @endif
    @endif

</div>
@endsection
