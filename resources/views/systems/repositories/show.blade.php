@extends('layouts.app')
@section('title', $repository->name . ' — ' . $system->name)

@section('content')
@php
    $providerColors = [
        'github'    => 'bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200',
        'gitlab'    => 'bg-orange-50 dark:bg-orange-900/30 text-orange-700 dark:text-orange-300',
        'bitbucket' => 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300',
        'gitea'     => 'bg-teal-50 dark:bg-teal-900/30 text-teal-700 dark:text-teal-300',
        'other'     => 'bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300',
    ];
    $providerColor = $providerColors[$repository->provider->value] ?? $providerColors['other'];
@endphp

<div class="max-w-3xl mx-auto space-y-6">

    {{-- Encabezado --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('systems.show', $system) }}?tab=repositories"
           class="inline-flex items-center justify-center w-10 h-10 rounded-lg
                  bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300
                  hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 flex-wrap">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white truncate">{{ $repository->name }}</h1>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-semibold {{ $providerColor }}">
                    {{ $repository->provider->label() }}
                </span>
                @if($repository->is_private)
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium
                             bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    Privado
                </span>
                @endif
                @if(!$repository->is_active)
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                             bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400">
                    Inactivo
                </span>
                @endif
            </div>
            <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">{{ $system->name }}</p>
        </div>
        @can('repositories.edit')
        <a href="{{ route('systems.repositories.edit', [$system, $repository]) }}"
           class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-600 dark:text-gray-300
                  bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600
                  rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
            </svg>
            Editar
        </a>
        @endcan
    </div>

    {{-- Info técnica --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
            <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Información del Repositorio</h3>
        </div>
        <div class="p-6 grid grid-cols-2 sm:grid-cols-3 gap-5 text-sm">
            @if($repository->clean_url)
            <div class="col-span-2 sm:col-span-3">
                <p class="text-xs text-gray-400 dark:text-gray-500 mb-0.5">URL</p>
                <a href="{{ $repository->clean_url }}" target="_blank"
                   class="font-mono text-blue-600 dark:text-blue-400 hover:underline break-all text-sm">
                    {{ $repository->clean_url }}
                </a>
            </div>
            @endif
            @if($repository->default_branch)
            <div>
                <p class="text-xs text-gray-400 dark:text-gray-500 mb-0.5">Rama principal</p>
                <p class="font-mono text-gray-800 dark:text-gray-200">{{ $repository->default_branch }}</p>
            </div>
            @endif
            @if($repository->repo_type)
            <div>
                <p class="text-xs text-gray-400 dark:text-gray-500 mb-0.5">Tipo</p>
                <p class="text-gray-800 dark:text-gray-200">{{ $repository->repo_type === 'organization' ? 'Organización' : 'Personal' }}</p>
            </div>
            @endif
            @if($repository->username)
            <div>
                <p class="text-xs text-gray-400 dark:text-gray-500 mb-0.5">Usuario</p>
                <p class="font-mono text-gray-800 dark:text-gray-200">{{ $repository->username }}</p>
            </div>
            @endif
            @if($repository->credential_type)
            <div>
                <p class="text-xs text-gray-400 dark:text-gray-500 mb-0.5">Credencial</p>
                <p class="text-gray-800 dark:text-gray-200">
                    {{ match($repository->credential_type) {
                        'token'      => 'Token (PAT)',
                        'password'   => 'Usuario + Contraseña',
                        'deploy_key' => 'Deploy Key (SSH)',
                        'oauth'      => 'OAuth App',
                        default      => $repository->credential_type,
                    } }}
                </p>
            </div>
            @endif
            @if($repository->notes)
            <div class="col-span-2 sm:col-span-3">
                <p class="text-xs text-gray-400 dark:text-gray-500 mb-0.5">Notas</p>
                <p class="text-gray-700 dark:text-gray-300 italic">{{ $repository->notes }}</p>
            </div>
            @endif
        </div>
    </div>

    {{-- ── Colaboradores ── --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">

        {{-- Cabecera --}}
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-700">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-violet-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Colaboradores</h3>
                @if($activeCollaborators->count() || $historicalCollaborators->count())
                <span class="text-xs text-gray-400 dark:text-gray-500">({{ $activeCollaborators->count() }} activos)</span>
                @endif
            </div>
            @can('repositories.edit')
            <button onclick="openModal('modal-collab')"
                    class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium
                           text-violet-600 dark:text-violet-400 bg-violet-50 dark:bg-violet-900/30
                           rounded-lg hover:bg-violet-100 dark:hover:bg-violet-900/50 transition-colors">
                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Agregar
            </button>
            @endcan
        </div>

        @if($activeCollaborators->isEmpty() && $historicalCollaborators->isEmpty())
            <div class="flex flex-col items-center justify-center py-10 text-center">
                <div class="w-12 h-12 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mb-3">
                    <svg class="w-6 h-6 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Sin colaboradores asignados</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Usa el botón "Agregar" para registrar uno</p>
            </div>
        @else
            {{-- Activos --}}
            @if($activeCollaborators->isEmpty())
                <div class="px-5 py-4 flex items-center gap-2 text-sm text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    </svg>
                    Sin colaboradores activos actualmente
                </div>
            @else
                <div class="divide-y divide-gray-100 dark:divide-gray-700/60">
                    @foreach($activeCollaborators as $collab)
                    @php
                        $initials = strtoupper(
                            substr($collab->persona->apellido_paterno ?? '', 0, 1) .
                            substr($collab->persona->apellido_materno ?? '', 0, 1)
                        );
                        $fullName = trim(
                            ($collab->persona->apellido_paterno ?? '') . ' ' .
                            ($collab->persona->apellido_materno ? $collab->persona->apellido_materno . ', ' : ', ') .
                            ($collab->persona->nombres ?? '')
                        );
                    @endphp
                    <div class="px-5 py-3.5">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-violet-100 dark:bg-violet-900/40 flex items-center justify-center
                                        text-xs font-bold text-violet-700 dark:text-violet-300 flex-shrink-0">
                                {{ $initials }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-800 dark:text-gray-200 truncate">{{ $fullName }}</p>
                                <div class="flex items-center gap-2 mt-0.5 flex-wrap">
                                    <span class="text-xs text-violet-600 dark:text-violet-400 font-medium">
                                        {{ \App\Models\RepositoryCollaborator::roleLabel($collab->role) }}
                                    </span>
                                    <span class="text-xs text-gray-400 dark:text-gray-500">
                                        Desde {{ $collab->assigned_at->format('d/m/Y') }}
                                    </span>
                                </div>
                            </div>
                            @can('repositories.edit')
                            <div class="flex items-center gap-1 flex-shrink-0">
                                <button onclick="openDeactivate({{ $collab->id }}, '{{ addslashes($fullName) }}')"
                                        title="Dar de baja"
                                        class="w-6 h-6 flex items-center justify-center rounded-md text-gray-400 dark:text-gray-500
                                               hover:text-orange-600 dark:hover:text-orange-400 hover:bg-orange-50 dark:hover:bg-orange-900/20 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                    </svg>
                                </button>
                                <button onclick="editCollab({{ $collab->id }}, {{ $collab->load('persona')->toJson() }})"
                                        title="Editar"
                                        class="w-6 h-6 flex items-center justify-center rounded-md text-gray-400 dark:text-gray-500
                                               hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                    </svg>
                                </button>
                            </div>
                            @endcan
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif

            {{-- Historial --}}
            @if($historicalCollaborators->isNotEmpty())
            <div class="border-t border-gray-100 dark:border-gray-700/60">
                <button type="button" onclick="toggleHistory()"
                        class="w-full flex items-center justify-between px-5 py-2.5 text-xs font-semibold
                               text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/30
                               uppercase tracking-wider transition-colors">
                    <span class="flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Historial de colaboradores
                        <span class="px-1.5 py-0.5 rounded-full bg-gray-200 dark:bg-gray-600 text-gray-600 dark:text-gray-300 normal-case tracking-normal font-semibold">
                            {{ $historicalCollaborators->count() }}
                        </span>
                    </span>
                    <svg id="history-chevron" class="w-3.5 h-3.5 transition-transform duration-200"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div id="collab-history" class="hidden divide-y divide-gray-100 dark:divide-gray-700/60 bg-gray-50/50 dark:bg-gray-800/30">
                    @foreach($historicalCollaborators as $collab)
                    @php
                        $initials = strtoupper(
                            substr($collab->persona->apellido_paterno ?? '', 0, 1) .
                            substr($collab->persona->apellido_materno ?? '', 0, 1)
                        );
                        $fullName = trim(
                            ($collab->persona->apellido_paterno ?? '') . ' ' .
                            ($collab->persona->apellido_materno ? $collab->persona->apellido_materno . ', ' : ', ') .
                            ($collab->persona->nombres ?? '')
                        );
                    @endphp
                    <div class="px-5 py-3 flex items-center gap-3 opacity-70">
                        <div class="w-7 h-7 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center
                                    text-xs font-bold text-gray-500 dark:text-gray-400 flex-shrink-0">
                            {{ $initials }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300 truncate">{{ $fullName }}</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                                {{ \App\Models\RepositoryCollaborator::roleLabel($collab->role) }}
                                · {{ $collab->assigned_at->format('d/m/Y') }} – {{ $collab->unassigned_at?->format('d/m/Y') ?? '?' }}
                            </p>
                        </div>
                        @can('repositories.edit')
                        <div class="flex items-center gap-1 flex-shrink-0">
                            <button onclick="openReactivate({{ $collab->id }}, '{{ addslashes($fullName) }}')"
                                    title="Reactivar"
                                    class="w-6 h-6 flex items-center justify-center rounded-md text-gray-400 dark:text-gray-500
                                           hover:text-violet-600 dark:hover:text-violet-400 hover:bg-violet-50 dark:hover:bg-violet-900/20 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                            </button>
                            <form action="{{ route('systems.repositories.collaborators.destroy', [$system, $repository, $collab]) }}"
                                  method="POST" id="del-hist-{{ $collab->id }}" class="inline">
                                @csrf @method('DELETE')
                                <button type="button" title="Eliminar del historial"
                                        onclick="dtConfirmDelete('del-hist-{{ $collab->id }}', '{{ addslashes($fullName) }}')"
                                        class="w-6 h-6 flex items-center justify-center rounded-md text-gray-400 dark:text-gray-500
                                               hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                        @endcan
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        @endif
    </div>
</div>

{{-- ════════════ MODAL: Asignar / Editar Colaborador ════════════ --}}
<div id="modal-collab" class="hidden fixed inset-0 z-50 items-center justify-center p-4" role="dialog" aria-modal="true">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeModal('modal-collab')"></div>
    <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 id="collab-modal-title" class="text-base font-semibold text-gray-900 dark:text-white">Agregar Colaborador</h3>
            <button onclick="closeModal('modal-collab')"
                    class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400
                           hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form id="collab-form"
              action="{{ route('systems.repositories.collaborators.store', [$system, $repository]) }}"
              method="POST">
            @csrf
            <span id="collab-method"></span>
            <div class="p-6 space-y-4">

                {{-- Búsqueda de persona --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Persona <span class="text-red-500">*</span>
                    </label>
                    <div class="relative" id="collab-search-wrap">
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input type="text" id="collab-search-input" autocomplete="off"
                                   placeholder="Buscar por DNI o apellido/nombre..."
                                   class="w-full pl-9 pr-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600
                                          dark:bg-gray-700 dark:text-white text-sm
                                          focus:ring-violet-500 focus:border-violet-500">
                        </div>
                        <input type="hidden" name="persona_id" id="collab-persona_id" required>
                        <div id="collab-dropdown"
                             class="hidden absolute z-50 w-full mt-1 bg-white dark:bg-gray-800
                                    border border-gray-200 dark:border-gray-600 rounded-lg shadow-xl
                                    max-h-48 overflow-y-auto text-sm"></div>
                        <div id="collab-selected"
                             class="hidden mt-2 items-center gap-2 px-3 py-2 rounded-lg
                                    bg-violet-50 dark:bg-violet-900/20 border border-violet-200 dark:border-violet-700/40">
                            <svg class="w-4 h-4 text-violet-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <span id="collab-selected-name" class="flex-1 text-sm font-medium text-violet-700 dark:text-violet-300 truncate"></span>
                            <button type="button" onclick="clearPersonaSearch()"
                                    class="text-violet-400 hover:text-red-500 transition-colors flex-shrink-0">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">Escribe al menos 4 caracteres para buscar</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Rol <span class="text-red-500">*</span>
                        </label>
                        <select name="role" id="collab-role" required
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                       dark:text-white focus:ring-violet-500 focus:border-violet-500 sm:text-sm">
                            <option value="owner">Propietario</option>
                            <option value="maintainer">Mantenedor</option>
                            <option value="developer" selected>Desarrollador</option>
                            <option value="reader">Lector</option>
                            <option value="deployer">Despliegue (CI/CD)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Fecha de asignación <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="assigned_at" id="collab-assigned_at" required
                               value="{{ now()->format('Y-m-d') }}"
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                      dark:text-white focus:ring-violet-500 focus:border-violet-500 sm:text-sm">
                    </div>
                </div>

            </div>
            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                <button type="button" onclick="closeModal('modal-collab')"
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700
                               border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancelar
                </button>
                <button id="collab-submit-btn" type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2 text-sm font-medium text-white
                               bg-violet-600 rounded-lg hover:bg-violet-700 transition-colors shadow-sm">
                    <svg id="collab-submit-icon" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <svg id="collab-submit-spinner" class="hidden w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/>
                    </svg>
                    <span id="collab-submit-label">Asignar</span>
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ════════════ MODAL: Dar de baja ════════════ --}}
<div id="modal-deactivate" class="hidden fixed inset-0 z-50 items-center justify-center p-4" role="dialog" aria-modal="true">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeModal('modal-deactivate')"></div>
    <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                </svg>
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Dar de Baja Colaborador</h3>
            </div>
            <button onclick="closeModal('modal-deactivate')"
                    class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400
                           hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form id="deactivate-form" method="POST">
            @csrf
            <input type="hidden" name="_method" value="PATCH">
            <div class="p-6 space-y-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    <strong id="deactivate-name" class="text-gray-800 dark:text-gray-200 font-semibold"></strong>
                    dejará de figurar como colaborador activo.
                </p>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Fecha de baja <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="unassigned_at" id="deactivate-date" required
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                  dark:text-white focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Observaciones</label>
                    <input type="text" name="deactivate_notes" id="deactivate-notes"
                           placeholder="Motivo de la baja..."
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                  dark:text-white focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 rounded-b-2xl">
                <button type="button" onclick="closeModal('modal-deactivate')"
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700
                               border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancelar
                </button>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2 text-sm font-medium text-white
                               bg-orange-600 rounded-lg hover:bg-orange-700 transition-colors shadow-sm">
                    Dar de baja
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ════════════ MODAL: Reactivar ════════════ --}}
<div id="modal-reactivate" class="hidden fixed inset-0 z-50 items-center justify-center p-4" role="dialog" aria-modal="true">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeModal('modal-reactivate')"></div>
    <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-violet-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Reactivar Colaborador</h3>
            </div>
            <button onclick="closeModal('modal-reactivate')"
                    class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400
                           hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form id="reactivate-form" method="POST">
            @csrf
            <input type="hidden" name="_method" value="PATCH">
            <div class="p-6 space-y-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    <strong id="reactivate-name" class="text-gray-800 dark:text-gray-200 font-semibold"></strong>
                    volverá a figurar como colaborador activo.
                </p>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Fecha de reactivación <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="assigned_at" id="reactivate-date" required
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                  dark:text-white focus:ring-violet-500 focus:border-violet-500 sm:text-sm">
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 rounded-b-2xl">
                <button type="button" onclick="closeModal('modal-reactivate')"
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700
                               border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancelar
                </button>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2 text-sm font-medium text-white
                               bg-violet-600 rounded-lg hover:bg-violet-700 transition-colors shadow-sm">
                    Reactivar
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
(function () {
    const storeUrl   = "{{ route('systems.repositories.collaborators.store', [$system, $repository]) }}";
    const updateBase = "{{ url('systems/' . $system->id . '/repositories/' . $repository->id . '/collaborators') }}/";
    const personaSearchUrl = "{{ route('admin.personas.search') }}";
    let searchTimer;
    let collabSubmitting = false;

    const collabForm    = document.getElementById('collab-form');
    const collabBtn     = document.getElementById('collab-submit-btn');
    const collabIcon    = document.getElementById('collab-submit-icon');
    const collabSpinner = document.getElementById('collab-submit-spinner');
    const collabLabel   = document.getElementById('collab-submit-label');

    collabForm.addEventListener('submit', function (e) {
        if (collabSubmitting) { e.preventDefault(); return; }
        if (!collabForm.checkValidity()) return;
        collabSubmitting = true;
        collabBtn.classList.add('pointer-events-none', 'opacity-75');
        collabIcon.classList.add('hidden');
        collabSpinner.classList.remove('hidden');
        collabLabel.textContent = 'Guardando…';
    });

    function resetCollabBtn() {
        collabSubmitting = false;
        collabBtn.classList.remove('pointer-events-none', 'opacity-75');
        collabIcon.classList.remove('hidden');
        collabSpinner.classList.add('hidden');
    }

    function openModal(id) {
        const m = document.getElementById(id);
        m.classList.remove('hidden');
        m.classList.add('flex');
    }
    window.openModal = openModal;

    function closeModal(id) {
        const m = document.getElementById(id);
        m.classList.add('hidden');
        m.classList.remove('flex');
    }
    window.closeModal = closeModal;

    // ── Persona autocomplete ─────────────────────────────────────────────────
    const searchInput = document.getElementById('collab-search-input');
    const hiddenInput = document.getElementById('collab-persona_id');
    const dropdown    = document.getElementById('collab-dropdown');
    const selected    = document.getElementById('collab-selected');
    const selName     = document.getElementById('collab-selected-name');

    searchInput.addEventListener('input', function () {
        const q = this.value.trim();
        clearTimeout(searchTimer);
        dropdown.classList.add('hidden');
        dropdown.innerHTML = '';
        if (q.length < 4) return;

        dropdown.innerHTML = '<p class="px-4 py-3 text-xs text-gray-400">Buscando...</p>';
        dropdown.classList.remove('hidden');

        searchTimer = setTimeout(async () => {
            try {
                const res  = await fetch(personaSearchUrl + '?q=' + encodeURIComponent(q));
                const data = await res.json();
                dropdown.innerHTML = '';
                if (!data.length) {
                    dropdown.innerHTML = '<p class="px-4 py-3 text-xs text-gray-400">Sin resultados</p>';
                    return;
                }
                data.forEach(p => {
                    const btn = document.createElement('button');
                    btn.type  = 'button';
                    btn.className = 'w-full text-left px-4 py-2.5 text-sm text-gray-800 dark:text-gray-200 ' +
                                    'hover:bg-violet-50 dark:hover:bg-violet-900/20 transition-colors';
                    btn.innerHTML = `${p.apellido_paterno} ${p.apellido_materno ?? ''}, ${p.nombres} &mdash; <span class="font-mono">${p.dni}</span>`;
                    btn.addEventListener('click', () => {
                        hiddenInput.value = p.id;
                        selName.textContent = `${p.apellido_paterno} ${p.apellido_materno ?? ''}, ${p.nombres} (${p.dni})`;
                        searchInput.value = '';
                        dropdown.classList.add('hidden');
                        dropdown.innerHTML = '';
                        selected.classList.remove('hidden');
                        selected.classList.add('flex');
                    });
                    dropdown.appendChild(btn);
                });
            } catch {
                dropdown.innerHTML = '<p class="px-4 py-3 text-xs text-red-400">Error al buscar</p>';
            }
        }, 300);
    });

    document.addEventListener('click', e => {
        if (!document.getElementById('collab-search-wrap').contains(e.target)) {
            dropdown.classList.add('hidden');
        }
    });

    window.clearPersonaSearch = function () {
        hiddenInput.value = '';
        selName.textContent = '';
        selected.classList.add('hidden');
        selected.classList.remove('flex');
        searchInput.value = '';
    };

    function resetSearch() {
        window.clearPersonaSearch();
        dropdown.classList.add('hidden');
        dropdown.innerHTML = '';
    }

    // ── Botón Agregar → reset modal ──────────────────────────────────────────
    document.querySelector('[onclick="openModal(\'modal-collab\')"]')?.addEventListener('click', function () {
        document.getElementById('collab-modal-title').textContent  = 'Agregar Colaborador';
        collabLabel.textContent = 'Asignar';
        collabForm.action = storeUrl;
        document.getElementById('collab-method').innerHTML = '';
        collabForm.reset();
        resetSearch();
        resetCollabBtn();
        document.getElementById('collab-assigned_at').value = new Date().toISOString().slice(0, 10);
        document.getElementById('collab-role').value = 'developer';
    });

    // ── Editar colaborador ───────────────────────────────────────────────────
    window.editCollab = function (id, data) {
        document.getElementById('collab-modal-title').textContent = 'Editar Colaborador';
        collabLabel.textContent = 'Guardar';
        collabForm.action = updateBase + id;
        document.getElementById('collab-method').innerHTML = '<input type="hidden" name="_method" value="PUT">';
        resetCollabBtn();

        hiddenInput.value = data.persona_id ?? '';
        if (data.persona_id && data.persona) {
            const nombre = (data.persona.apellido_paterno ?? '') + ' ' + (data.persona.apellido_materno ?? '') +
                           ', ' + (data.persona.nombres ?? '');
            selName.textContent = nombre.trim();
            selected.classList.remove('hidden');
            selected.classList.add('flex');
        }

        document.getElementById('collab-role').value       = data.role        ?? 'developer';
        document.getElementById('collab-assigned_at').value = data.assigned_at ?? '';
        openModal('modal-collab');
    };

    // ── Baja ─────────────────────────────────────────────────────────────────
    window.openDeactivate = function (id, nombre) {
        document.getElementById('deactivate-name').textContent = nombre;
        document.getElementById('deactivate-form').action = updateBase + id + '/deactivate';
        document.getElementById('deactivate-date').value  = new Date().toISOString().slice(0, 10);
        document.getElementById('deactivate-notes').value = '';
        openModal('modal-deactivate');
    };

    // ── Reactivar ────────────────────────────────────────────────────────────
    window.openReactivate = function (id, nombre) {
        document.getElementById('reactivate-name').textContent = nombre;
        document.getElementById('reactivate-form').action = updateBase + id + '/reactivate';
        document.getElementById('reactivate-date').value  = new Date().toISOString().slice(0, 10);
        openModal('modal-reactivate');
    };

    // ── Historial toggle ─────────────────────────────────────────────────────
    window.toggleHistory = function () {
        const panel   = document.getElementById('collab-history');
        const chevron = document.getElementById('history-chevron');
        const hidden  = panel.classList.toggle('hidden');
        chevron.style.transform = hidden ? '' : 'rotate(180deg)';
    };

    window.dtConfirmDelete = function (formId, name) {
        if (confirm('¿Eliminar a ' + name + ' del historial?')) {
            document.getElementById(formId).submit();
        }
    };
})();
</script>
@endpush
@endsection
