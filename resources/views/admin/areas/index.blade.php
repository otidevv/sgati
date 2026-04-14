@extends('layouts.app')

@section('title', 'Áreas')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Gestión de Áreas</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Unidades organizativas de UNAMAD</p>
        </div>
        @can('areas.create')
        <a href="{{ route('admin.areas.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white
                  text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nueva Área
        </a>
        @endcan
    </div>

    @if(session('error'))
    <div class="flex items-start gap-3 p-4 bg-red-50 dark:bg-red-900/20
                border border-red-200 dark:border-red-800 rounded-lg">
        <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="text-sm text-red-700 dark:text-red-300">{{ session('error') }}</p>
    </div>
    @endif

    <x-data-table id="areas-table" search-placeholder="Buscar área…">

        {{-- Columnas --}}
        <x-slot:thead>
            <th data-col="0" class="tbl-sort px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer select-none group">
                <div class="flex items-center gap-1.5">Área <span class="sort-icon text-gray-300 dark:text-gray-600 group-hover:text-gray-400"></span></div>
            </th>
            <th data-col="1" class="tbl-sort px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer select-none group">
                <div class="flex items-center gap-1.5">Siglas <span class="sort-icon text-gray-300 dark:text-gray-600 group-hover:text-gray-400"></span></div>
            </th>
            <th data-col="2" class="tbl-sort px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer select-none group">
                <div class="flex items-center gap-1.5">Descripción <span class="sort-icon text-gray-300 dark:text-gray-600 group-hover:text-gray-400"></span></div>
            </th>
            <th data-col="3" class="tbl-sort px-5 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer select-none group">
                <div class="flex items-center justify-center gap-1.5">Sistemas <span class="sort-icon text-gray-300 dark:text-gray-600 group-hover:text-gray-400"></span></div>
            </th>
            <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                Acciones
            </th>
        </x-slot:thead>

        {{-- Filas --}}
        <x-slot:tbody>
            @forelse($areas as $area)
            <tr class="tbl-row hover:bg-gray-50 dark:hover:bg-gray-700/40 transition-colors">
                <td class="px-5 py-3.5">
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0 w-9 h-9 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600
                                    flex items-center justify-center text-white font-bold text-xs">
                            {{ strtoupper(substr($area->acronym ?? $area->name, 0, 2)) }}
                        </div>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $area->name }}</p>
                    </div>
                </td>
                <td class="px-5 py-3.5">
                    <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ $area->acronym ?? '—' }}</span>
                </td>
                <td class="px-5 py-3.5">
                    <span class="text-sm text-gray-500 dark:text-gray-400 truncate max-w-xs block">{{ $area->description ?? '—' }}</span>
                </td>
                <td class="px-5 py-3.5 text-center">
                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg
                                 bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400
                                 text-xs font-semibold">
                        {{ $area->systems_count }}
                    </span>
                </td>
                <td class="px-5 py-3.5 text-right">
                    <div class="inline-flex items-center gap-1">
                        @can('areas.edit')
                        <a href="{{ route('admin.areas.edit', $area) }}" title="Editar"
                           class="inline-flex items-center justify-center w-8 h-8 rounded-lg
                                  text-gray-400 dark:text-gray-500
                                  hover:text-blue-600 dark:hover:text-blue-400
                                  hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-all">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                            </svg>
                        </a>
                        @endcan
                        @can('areas.delete')
                        <form id="del-area-{{ $area->id }}"
                              action="{{ route('admin.areas.destroy', $area) }}"
                              method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button type="button" title="{{ $area->systems_count > 0 ? 'Tiene sistemas asociados' : 'Eliminar' }}"
                                    @if($area->systems_count > 0)
                                    disabled
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg
                                           text-gray-200 dark:text-gray-700 cursor-not-allowed"
                                    @else
                                    onclick="dtConfirmDelete('del-area-{{ $area->id }}', '{{ addslashes($area->name) }}')"
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg
                                           text-gray-400 dark:text-gray-500
                                           hover:text-red-600 dark:hover:text-red-400
                                           hover:bg-red-50 dark:hover:bg-red-900/30 transition-all"
                                    @endif>
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7
                                             m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </form>
                        @endcan
                    </div>
                </td>
            </tr>
            @empty
            @endforelse
        </x-slot:tbody>

        {{-- Estado vacío --}}
        @if($areas->isEmpty())
        <x-slot:empty>
            <div class="text-center py-16">
                <div class="mx-auto w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                    <svg class="w-8 h-8 text-gray-300 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <h3 class="mt-4 text-sm font-semibold text-gray-900 dark:text-white">No hay áreas registradas</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Comienza registrando las unidades organizativas.</p>
                @can('areas.create')
                <a href="{{ route('admin.areas.create') }}"
                   class="mt-6 inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white
                          text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Registrar primera área
                </a>
                @endcan
            </div>
        </x-slot:empty>
        @endif

    </x-data-table>

</div>
@endsection
