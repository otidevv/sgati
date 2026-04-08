@extends('layouts.app')
@section('title', 'Sistemas')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="relative bg-gradient-to-r from-blue-600 to-blue-700 rounded-2xl shadow-lg overflow-hidden">
        <div class="absolute inset-0 opacity-10 pointer-events-none">
            <svg class="h-full w-full" viewBox="0 0 800 200" preserveAspectRatio="none">
                <path d="M0,0 C200,50 400,150 600,100 C700,75 750,125 800,100 L800,0 L0,0 Z" fill="white"/>
            </svg>
        </div>
        <div class="relative z-10 px-6 py-8 sm:px-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-white/10 backdrop-blur-sm rounded-xl">
                        <svg class="w-8 h-8 text-blue-100" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-white">Sistemas de Información</h1>
                        <p class="mt-1 text-sm text-blue-100">Inventario de sistemas registrados en UNAMAD</p>
                    </div>
                </div>
                @can('systems.create')
                <a href="{{ route('systems.create') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-white text-blue-600 text-sm font-semibold rounded-lg hover:bg-blue-50 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Nuevo Sistema
                </a>
                @endcan
            </div>
        </div>
    </div>

    {{-- Filters Bar --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-5">
        <div class="flex flex-col xl:flex-row xl:items-center gap-4">
            {{-- Pills de estado --}}
            <div class="flex-1">
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="text-sm font-semibold text-gray-700 dark:text-gray-300 flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Estado:
                    </span>
                    <div class="w-px h-5 bg-gray-300 dark:bg-gray-600 hidden sm:block"></div>
                    <div class="flex items-center gap-1.5 flex-wrap">
                        @php $currentStatus = request('status'); @endphp
                        <a href="{{ route('systems.index', request()->except(['status','page'])) }}"
                           style="display: inline-flex; padding: 0.5rem 1rem; font-size: 0.875rem; font-weight: 500; border-radius: 0.5rem; transition: all 0.2s; border: 2px solid;
                                  {{ !$currentStatus ? 'background: rgb(17 24 39); color: white; border-color: rgb(17 24 39);' : 'background: rgb(243 244 246); color: rgb(55 65 81); border-color: rgb(229 231 235);' }}">
                            Todos
                        </a>
                        @foreach($statuses as $s)
                        @php
                        $isActive = $currentStatus === $s->value;
                        $colors = match($s->value) {
                            'active'      => ['bg' => 'rgb(22, 163, 74)', 'border' => 'rgb(22, 163, 74)', 'text' => 'white'],
                            'development' => ['bg' => 'rgb(37, 99, 235)', 'border' => 'rgb(37, 99, 235)', 'text' => 'white'],
                            'maintenance' => ['bg' => 'rgb(245, 158, 11)', 'border' => 'rgb(245, 158, 11)', 'text' => 'white'],
                            'inactive'    => ['bg' => 'rgb(220, 38, 38)', 'border' => 'rgb(220, 38, 38)', 'text' => 'white'],
                            default       => ['bg' => 'rgb(75, 85, 99)', 'border' => 'rgb(75, 85, 99)', 'text' => 'white'],
                        };
                        $inactiveBg = 'rgb(243, 244, 246)';
                        $inactiveBorder = 'rgb(229, 231, 235)';
                        $inactiveText = 'rgb(55, 65, 81)';
                        @endphp
                        <a href="{{ route('systems.index', array_merge(request()->except(['status','page']), ['status' => $s->value])) }}"
                           style="display: inline-flex; padding: 0.5rem 1rem; font-size: 0.875rem; font-weight: 500; border-radius: 0.5rem; transition: all 0.2s; border: 2px solid;
                                  background: {{ $isActive ? $colors['bg'] : $inactiveBg }};
                                  color: {{ $isActive ? $colors['text'] : $inactiveText }};
                                  border-color: {{ $isActive ? $colors['border'] : $inactiveBorder }};
                                  box-shadow: {{ $isActive ? '0 10px 15px -3px rgb(0 0 0 / 0.1)' : 'none' }};">
                            {{ $s->label() }}
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Área + búsqueda --}}
            <form method="GET" action="{{ route('systems.index') }}" class="flex flex-col sm:flex-row gap-3 xl:ml-auto">
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <select name="area_id" onchange="this.form.submit()"
                            class="pl-10 pr-10 py-2.5 text-sm font-medium rounded-lg border-2 border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-300 transition-all cursor-pointer">
                        <option value="">Todas las áreas</option>
                        @foreach($areas as $area)
                        <option value="{{ $area->id }}" {{ request('area_id') == $area->id ? 'selected' : '' }}>
                            {{ $area->acronym ?? $area->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="relative flex-1 min-w-[240px]">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Buscar sistema…"
                           class="pl-10 pr-4 py-2.5 text-sm rounded-lg border-2 border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-300 transition-all w-full">
                    @if(request('search'))
                    <a href="{{ route('systems.index', array_merge(request()->except(['search','page']), request()->only('status', 'area_id'))) }}"
                       class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Results Count --}}
    @if($systems->total() > 0)
    <div class="flex items-center justify-between">
        <p class="text-sm text-gray-600 dark:text-gray-400">
            Mostrando <span class="font-semibold text-gray-900 dark:text-white">{{ $systems->firstItem() }}</span> a
            <span class="font-semibold text-gray-900 dark:text-white">{{ $systems->lastItem() }}</span> de
            <span class="font-semibold text-gray-900 dark:text-white">{{ $systems->total() }}</span> sistemas
        </p>
    </div>
    @endif

    {{-- Grid de cards --}}
    @if($systems->isEmpty())
    <div class="text-center py-20 bg-white dark:bg-gray-800 rounded-xl border-2 border-dashed border-gray-200 dark:border-gray-700">
        <div class="mx-auto w-20 h-20 rounded-full bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600 flex items-center justify-center">
            <svg class="w-10 h-10 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
        </div>
        <h3 class="mt-6 text-lg font-semibold text-gray-900 dark:text-white">No se encontraron sistemas</h3>
        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 max-w-sm mx-auto">
            {{ request()->hasAny(['status','area_id','search']) ? 'Prueba ajustando los filtros de búsqueda para encontrar lo que necesitas.' : 'Comienza registrando el primer sistema de información en UNAMAD.' }}
        </p>
        @can('systems.create')
        <div class="mt-8">
            <a href="{{ route('systems.create') }}"
               class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Registrar Sistema
            </a>
        </div>
        @endcan
    </div>
    @else
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
        @foreach($systems as $system)
        @php
            $sv = $system->status->value;
            $gradientColors = match($sv) {
                'active'      => 'linear-gradient(135deg, #16a34a, #059669)',
                'development' => 'linear-gradient(135deg, #2563eb, #4f46e5)',
                'maintenance' => 'linear-gradient(135deg, #f59e0b, #ea580c)',
                'inactive'    => 'linear-gradient(135deg, #9ca3af, #4b5563)',
                default       => 'linear-gradient(135deg, #9ca3af, #4b5563)',
            };
            $statusColors = match($sv) {
                'active'      => ['bg' => 'rgb(220, 252, 231)', 'text' => 'rgb(22, 101, 52)', 'border' => 'rgb(187, 247, 208)'],
                'development' => ['bg' => 'rgb(219, 234, 254)', 'text' => 'rgb(30, 64, 175)', 'border' => 'rgb(191, 219, 254)'],
                'maintenance' => ['bg' => 'rgb(254, 243, 199)', 'text' => 'rgb(146, 64, 14)', 'border' => 'rgb(253, 230, 138)'],
                'inactive'    => ['bg' => 'rgb(254, 226, 226)', 'text' => 'rgb(153, 27, 27)', 'border' => 'rgb(254, 202, 202)'],
                default       => ['bg' => 'rgb(243, 244, 246)', 'text' => 'rgb(55, 65, 81)', 'border' => 'rgb(229, 231, 235)'],
            };
        @endphp
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-xl dark:hover:shadow-gray-700/50 transition-all duration-300 flex flex-col group overflow-hidden">

            {{-- Top accent bar --}}
            <div style="height: 0.375rem; background: {{ $gradientColors }};"></div>

            {{-- Card header --}}
            <div class="p-5 pb-4">
                <div class="flex items-start gap-4">
                    <div style="flex-shrink: 0; width: 3.5rem; height: 3.5rem; border-radius: 0.75rem; background: {{ $gradientColors }}; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 1.125rem; box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1); transition: transform 0.3s;" class="group-hover:scale-105">
                        {{ strtoupper(substr($system->acronym ?? $system->name, 0, 3)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex-1 min-w-0">
                                <h3 class="text-base font-bold text-gray-900 dark:text-white leading-tight group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors truncate">{{ $system->name }}</h3>
                                @if($system->acronym)
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 font-mono">{{ $system->acronym }}</p>
                                @endif
                            </div>
                            <span style="display: inline-flex; align-items: center; padding: 0.25rem 0.625rem; font-size: 0.75rem; font-weight: 600; border-radius: 0.5rem; background: {{ $statusColors['bg'] }}; color: {{ $statusColors['text'] }}; border: 1px solid {{ $statusColors['border'] }};">
                                {{ $system->status->label() }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card body --}}
            @if($system->description || $system->area || $system->responsible || $system->infrastructure?->system_url)
            <div class="px-5 pb-4 flex-1 space-y-3">
                @if($system->description)
                <p class="text-sm text-gray-600 dark:text-gray-300 line-clamp-2 leading-relaxed">{{ $system->description }}</p>
                @endif

                <div class="space-y-2.5 pt-1">
                    @if($system->area)
                    <div class="flex items-center gap-2.5 text-sm text-gray-600 dark:text-gray-300">
                        <div class="flex-shrink-0 w-7 h-7 rounded-md bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center">
                            <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <span class="truncate font-medium">{{ $system->area->name }}</span>
                    </div>
                    @endif
                    @if($system->responsible)
                    <div class="flex items-center gap-2.5 text-sm text-gray-600 dark:text-gray-300">
                        <div class="flex-shrink-0 w-7 h-7 rounded-md bg-purple-50 dark:bg-purple-900/30 flex items-center justify-center">
                            <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <span class="truncate font-medium">{{ $system->responsible->name }}</span>
                    </div>
                    @endif
                    @if($system->infrastructure?->system_url)
                    <div class="flex items-center gap-2.5 text-sm">
                        <div class="flex-shrink-0 w-7 h-7 rounded-md flex items-center justify-center {{ $system->infrastructure->ssl_enabled ? 'bg-green-50 dark:bg-green-900/30' : 'bg-gray-50 dark:bg-gray-700' }}">
                            <svg class="w-4 h-4 {{ $system->infrastructure->ssl_enabled ? 'text-green-600 dark:text-green-400' : 'text-gray-500 dark:text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                            </svg>
                        </div>
                        <a href="{{ $system->infrastructure->system_url }}" target="_blank"
                           class="truncate font-mono text-xs {{ $system->infrastructure->ssl_enabled ? 'text-green-700 dark:text-green-400' : 'text-gray-600 dark:text-gray-400' }} hover:underline">
                            {{ $system->infrastructure->system_url }}
                        </a>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- Card footer --}}
            <div class="mt-auto border-t border-gray-100 dark:border-gray-700 px-5 py-3 flex items-center justify-between bg-gray-50/50 dark:bg-gray-700/30">
                <div class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Actualizado {{ $system->updated_at->diffForHumans() }}</span>
                </div>
                <div class="flex items-center gap-1">
                    <a href="{{ route('systems.show', $system) }}"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-lg transition-colors">
                        Ver
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                    @can('systems.edit')
                    <a href="{{ route('systems.edit', $system) }}"
                       class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-all"
                       title="Editar">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                    </a>
                    @endcan
                    @can('systems.delete')
                    <form action="{{ route('systems.destroy', $system) }}" method="POST" class="inline">
                        @csrf @method('DELETE')
                        <button type="button" x-data
                                @click.prevent="sgDeleteForm($el.closest('form'), '¿Eliminar el sistema \'{{ addslashes($system->name) }}\'?')"
                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-500 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 transition-all"
                                title="Eliminar">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
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
    <div class="flex justify-center">{{ $systems->links() }}</div>
    @endif
    @endif

</div>
@endsection
