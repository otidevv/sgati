@extends('layouts.app')

@section('title', 'Personas')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Gestión de Personas</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Datos civiles del personal</p>
        </div>
        <a href="{{ route('admin.personas.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white
                  text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nueva Persona
        </a>
    </div>

    {{-- Tarjetas de resumen --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        @foreach([
            ['label' => 'Total',        'value' => $personas->count(),                                                       'color' => 'text-gray-900 dark:text-white'],
            ['label' => 'Con Usuario',  'value' => $personas->where('user', '!=', null)->count(),                            'color' => 'text-emerald-600 dark:text-emerald-400'],
            ['label' => 'Sin Usuario',  'value' => $personas->where('user', null)->count(),                                  'color' => 'text-amber-600 dark:text-amber-400'],
            ['label' => 'Sin Contacto','value' => $personas->whereNull('email_personal')->whereNull('telefono')->count(),    'color' => 'text-gray-500 dark:text-gray-400'],
        ] as $card)
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 px-4 py-3">
            <p class="text-[10px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">{{ $card['label'] }}</p>
            <p class="mt-1 text-2xl font-bold {{ $card['color'] }}">{{ $card['value'] }}</p>
        </div>
        @endforeach
    </div>

    <x-data-table id="personas-table" search-placeholder="Buscar persona…">

        {{-- Columnas --}}
        <x-slot:thead>
            <th data-col="0" class="tbl-sort px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer select-none group">
                <div class="flex items-center gap-1.5">Persona <span class="sort-icon text-gray-300 dark:text-gray-600 group-hover:text-gray-400"></span></div>
            </th>
            <th data-col="1" class="tbl-sort px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer select-none group">
                <div class="flex items-center gap-1.5">DNI <span class="sort-icon text-gray-300 dark:text-gray-600 group-hover:text-gray-400"></span></div>
            </th>
            <th data-col="2" class="tbl-sort px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer select-none group">
                <div class="flex items-center gap-1.5">Contacto <span class="sort-icon text-gray-300 dark:text-gray-600 group-hover:text-gray-400"></span></div>
            </th>
            <th data-col="3" class="tbl-sort px-5 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer select-none group">
                <div class="flex items-center justify-center gap-1.5">Sexo <span class="sort-icon text-gray-300 dark:text-gray-600 group-hover:text-gray-400"></span></div>
            </th>
            <th data-col="4" class="tbl-sort px-5 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer select-none group">
                <div class="flex items-center justify-center gap-1.5">Estado <span class="sort-icon text-gray-300 dark:text-gray-600 group-hover:text-gray-400"></span></div>
            </th>
            <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                Acciones
            </th>
        </x-slot:thead>

        {{-- Filas --}}
        <x-slot:tbody>
            @forelse($personas as $persona)
            <tr class="tbl-row hover:bg-gray-50 dark:hover:bg-gray-700/40 transition-colors">
                <td class="px-5 py-3.5">
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0 w-9 h-9 rounded-lg
                                    bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500
                                    flex items-center justify-center text-white font-bold text-xs">
                            {{ strtoupper(substr($persona->nombres, 0, 1) . substr($persona->apellido_paterno, 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                                {{ $persona->nombre_completo }}
                            </p>
                            @if($persona->fecha_nacimiento)
                            <p class="text-xs text-gray-400 dark:text-gray-500">
                                {{ $persona->fecha_nacimiento->format('d/m/Y') }}
                                ({{ $persona->fecha_nacimiento->age }} años)
                            </p>
                            @endif
                        </div>
                    </div>
                </td>
                <td class="px-5 py-3.5">
                    <span class="text-sm font-mono text-gray-700 dark:text-gray-300">{{ $persona->dni }}</span>
                </td>
                <td class="px-5 py-3.5">
                    <div class="text-sm">
                        @if($persona->email_personal)
                        <p class="text-gray-700 dark:text-gray-300 truncate max-w-[180px]">{{ $persona->email_personal }}</p>
                        @endif
                        @if($persona->telefono)
                        <p class="text-gray-400 dark:text-gray-500 text-xs">{{ $persona->telefono }}</p>
                        @endif
                        @if(!$persona->email_personal && !$persona->telefono)
                        <span class="text-gray-300 dark:text-gray-600 text-xs">—</span>
                        @endif
                    </div>
                </td>
                <td class="px-5 py-3.5 text-center">
                    @if($persona->sexo)
                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-xs font-semibold
                                 {{ $persona->sexo === 'M'
                                    ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400'
                                    : 'bg-pink-100 dark:bg-pink-900/30 text-pink-700 dark:text-pink-400' }}">
                        {{ $persona->sexo }}
                    </span>
                    @else
                    <span class="text-gray-300 dark:text-gray-600">—</span>
                    @endif
                </td>
                <td class="px-5 py-3.5 text-center">
                    @if($persona->user)
                    <span class="inline-flex items-center gap-1.5 text-xs font-medium text-emerald-700 dark:text-emerald-400">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        Vinculado
                    </span>
                    @else
                    <span class="inline-flex items-center gap-1.5 text-xs font-medium text-amber-600 dark:text-amber-400">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                        Sin cuenta
                    </span>
                    @endif
                </td>
                <td class="px-5 py-3.5 text-right">
                    <div class="inline-flex items-center gap-1">
                        <a href="{{ route('admin.personas.edit', $persona) }}" title="Editar"
                           class="inline-flex items-center justify-center w-8 h-8 rounded-lg
                                  text-gray-400 dark:text-gray-500
                                  hover:text-blue-600 dark:hover:text-blue-400
                                  hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-all">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                            </svg>
                        </a>
                        @if(!$persona->user)
                        <form id="del-persona-{{ $persona->id }}"
                              action="{{ route('admin.personas.destroy', $persona) }}"
                              method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button type="button" title="Eliminar"
                                    onclick="dtConfirmDelete('del-persona-{{ $persona->id }}', '{{ addslashes($persona->nombre_completo) }}')"
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg
                                           text-gray-400 dark:text-gray-500
                                           hover:text-red-600 dark:hover:text-red-400
                                           hover:bg-red-50 dark:hover:bg-red-900/30 transition-all">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7
                                             m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            @endforelse
        </x-slot:tbody>

        {{-- Estado vacío --}}
        @if($personas->isEmpty())
        <x-slot:empty>
            <div class="text-center py-16">
                <div class="mx-auto w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                    <svg class="w-8 h-8 text-gray-300 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <h3 class="mt-4 text-sm font-semibold text-gray-900 dark:text-white">No hay personas registradas</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Comienza registrando los datos civiles del personal.</p>
                <a href="{{ route('admin.personas.create') }}"
                   class="mt-6 inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white
                          text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Registrar primera persona
                </a>
            </div>
        </x-slot:empty>
        @endif

    </x-data-table>

</div>
@endsection
