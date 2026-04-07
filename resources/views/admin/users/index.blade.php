@extends('layouts.app')

@section('title', 'Usuarios')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Gestión de Usuarios</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Cuentas de acceso al sistema</p>
        </div>
        <a href="{{ route('admin.users.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white
                  text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nuevo Usuario
        </a>
    </div>

    {{-- Tarjetas de resumen --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        @foreach([
            ['label' => 'Total',    'value' => $users->count(),                           'color' => 'text-gray-900 dark:text-white'],
            ['label' => 'Activos',  'value' => $users->where('is_active', true)->count(),  'color' => 'text-emerald-600 dark:text-emerald-400'],
            ['label' => 'Inactivos','value' => $users->where('is_active', false)->count(), 'color' => 'text-red-600 dark:text-red-400'],
            ['label' => 'Sin Rol',  'value' => $users->whereNull('role_id')->count(),      'color' => 'text-gray-500 dark:text-gray-400'],
        ] as $card)
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 px-4 py-3">
            <p class="text-[10px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">{{ $card['label'] }}</p>
            <p class="mt-1 text-2xl font-bold {{ $card['color'] }}">{{ $card['value'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- Aviso --}}
    <div class="flex items-start gap-3 p-4 bg-blue-50 dark:bg-blue-900/20
                border border-blue-200 dark:border-blue-800 rounded-lg">
        <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="text-sm text-blue-700 dark:text-blue-300">
            <span class="font-semibold text-blue-900 dark:text-blue-200">Requisito previo:</span>
            Para crear un usuario, primero registra a la persona en
            <a href="{{ route('admin.personas.index') }}"
               class="underline font-semibold hover:text-blue-900 dark:hover:text-blue-100">Personas</a>.
        </p>
    </div>

    <x-data-table id="users-table" search-placeholder="Buscar usuario…">

        {{-- Columnas --}}
        <x-slot:thead>
            <th data-col="0" class="tbl-sort px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer select-none group">
                <div class="flex items-center gap-1.5">Usuario <span class="sort-icon text-gray-300 dark:text-gray-600 group-hover:text-gray-400"></span></div>
            </th>
            <th data-col="1" class="tbl-sort px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer select-none group">
                <div class="flex items-center gap-1.5">Email <span class="sort-icon text-gray-300 dark:text-gray-600 group-hover:text-gray-400"></span></div>
            </th>
            <th data-col="2" class="tbl-sort px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer select-none group">
                <div class="flex items-center gap-1.5">Rol <span class="sort-icon text-gray-300 dark:text-gray-600 group-hover:text-gray-400"></span></div>
            </th>
            <th data-col="3" class="tbl-sort px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer select-none group">
                <div class="flex items-center gap-1.5">Área <span class="sort-icon text-gray-300 dark:text-gray-600 group-hover:text-gray-400"></span></div>
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
            @forelse($users as $user)
            <tr class="tbl-row hover:bg-gray-50 dark:hover:bg-gray-700/40 transition-colors">
                <td class="px-5 py-3.5">
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0 w-9 h-9 rounded-lg
                                    bg-gradient-to-br from-blue-500 to-indigo-600
                                    flex items-center justify-center text-white font-bold text-xs">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ $user->name }}</p>
                            @if($user->persona)
                            <p class="text-xs text-gray-400 dark:text-gray-500 truncate">{{ $user->persona->nombre_corto }}</p>
                            @endif
                        </div>
                    </div>
                </td>
                <td class="px-5 py-3.5">
                    <span class="text-sm text-gray-600 dark:text-gray-300">{{ $user->email }}</span>
                </td>
                <td class="px-5 py-3.5">
                    @if($user->role)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium
                                 bg-violet-100 dark:bg-violet-900/30 text-violet-700 dark:text-violet-300">
                        {{ $user->role->label }}
                    </span>
                    @else
                    <span class="text-gray-300 dark:text-gray-600 text-xs">—</span>
                    @endif
                </td>
                <td class="px-5 py-3.5">
                    <span class="text-sm text-gray-600 dark:text-gray-300">{{ $user->area->name ?? '—' }}</span>
                </td>
                <td class="px-5 py-3.5 text-center">
                    @if($user->is_active)
                    <span class="inline-flex items-center gap-1.5 text-xs font-medium text-emerald-700 dark:text-emerald-400">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>Activo
                    </span>
                    @else
                    <span class="inline-flex items-center gap-1.5 text-xs font-medium text-gray-400 dark:text-gray-500">
                        <span class="w-1.5 h-1.5 rounded-full bg-gray-300 dark:bg-gray-600"></span>Inactivo
                    </span>
                    @endif
                </td>
                <td class="px-5 py-3.5 text-right">
                    <div class="inline-flex items-center gap-1">
                        <a href="{{ route('admin.users.edit', $user) }}" title="Editar"
                           class="inline-flex items-center justify-center w-8 h-8 rounded-lg
                                  text-gray-400 dark:text-gray-500
                                  hover:text-blue-600 dark:hover:text-blue-400
                                  hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-all">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                            </svg>
                        </a>
                        @if($user->id !== auth()->id())
                        <form id="del-user-{{ $user->id }}"
                              action="{{ route('admin.users.destroy', $user) }}"
                              method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button type="button" title="Eliminar"
                                    onclick="dtConfirmDelete('del-user-{{ $user->id }}', '{{ addslashes($user->name) }}')"
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
        @if($users->isEmpty())
        <x-slot:empty>
            <div class="text-center py-16">
                <div class="mx-auto w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                    <svg class="w-8 h-8 text-gray-300 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                <h3 class="mt-4 text-sm font-semibold text-gray-900 dark:text-white">Sin usuarios registrados</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Primero registra personas, luego crea sus cuentas.</p>
                <a href="{{ route('admin.personas.create') }}"
                   class="mt-6 inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white
                          text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Registrar Persona
                </a>
            </div>
        </x-slot:empty>
        @endif

    </x-data-table>

</div>
@endsection
