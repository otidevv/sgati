@extends('layouts.app')
@section('title', $database->db_name . ' — ' . $system->name)

@section('content')
@php
    $activeResponsibles     = $database->responsibles->where('is_active', true);
    $historicalResponsibles = $database->responsibles->where('is_active', false)->sortByDesc('unassigned_at');
    $docLabels = ['resolucion_directoral'=>'R.D.','resolucion_jefatural'=>'R.J.','memorando'=>'Memo.','oficio'=>'Oficio','contrato'=>'Contrato','acta'=>'Acta','otro'=>'Doc.'];

    $engineColors = match($database->engine->value ?? $database->engine) {
        'postgresql' => 'bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300',
        'mysql'      => 'bg-orange-100 dark:bg-orange-900/40 text-orange-700 dark:text-orange-300',
        'mariadb'    => 'bg-orange-100 dark:bg-orange-900/40 text-orange-700 dark:text-orange-300',
        'oracle'     => 'bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-300',
        'sqlserver'  => 'bg-slate-100 dark:bg-slate-700/40 text-slate-700 dark:text-slate-300',
        'mongodb'    => 'bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-300',
        'sqlite'     => 'bg-purple-100 dark:bg-purple-900/40 text-purple-700 dark:text-purple-300',
        default      => 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300',
    };

    $envColors = match($database->environment->value ?? $database->environment) {
        'production'  => 'bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-300 ring-red-200 dark:ring-red-700',
        'staging'     => 'bg-yellow-50 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300 ring-yellow-200 dark:ring-yellow-700',
        'development' => 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 ring-emerald-200 dark:ring-emerald-700',
        default       => 'bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300 ring-gray-200 dark:ring-gray-600',
    };
@endphp

<div class="max-w-3xl mx-auto space-y-6">

    {{-- Encabezado --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('systems.show', $system) }}?tab=databases"
           class="inline-flex items-center justify-center w-10 h-10 rounded-lg
                  bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300
                  hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 flex-wrap">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white font-mono truncate">{{ $database->db_name }}</h1>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-semibold {{ $engineColors }}">
                    {{ strtoupper($database->engine->value ?? $database->engine) }}
                </span>
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium ring-1 {{ $envColors }}">
                    {{ $database->environment->label() }}
                </span>
            </div>
            <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">{{ $system->name }}</p>
        </div>
        @can('databases.create/edit/delete')
        <a href="{{ route('systems.databases.edit', [$system, $database]) }}"
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
            <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Información técnica</h3>
        </div>
        <div class="p-6 grid grid-cols-2 sm:grid-cols-3 gap-5 text-sm">
            @if($database->databaseServer)
            <div>
                <p class="text-xs text-gray-400 dark:text-gray-500 mb-0.5">Gestor / Motor</p>
                <p class="font-medium text-gray-800 dark:text-gray-200">{{ $database->databaseServer->engine_label }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 dark:text-gray-500 mb-0.5">Host</p>
                <p class="font-mono text-gray-800 dark:text-gray-200">{{ $database->databaseServer->connection_string }}</p>
            </div>
            @endif
            @if($database->schema_name)
            <div>
                <p class="text-xs text-gray-400 dark:text-gray-500 mb-0.5">Schema</p>
                <p class="font-mono text-gray-800 dark:text-gray-200">{{ $database->schema_name }}</p>
            </div>
            @endif
            @if($database->db_user)
            <div>
                <p class="text-xs text-gray-400 dark:text-gray-500 mb-0.5">Usuario BD</p>
                <p class="font-mono text-gray-800 dark:text-gray-200">{{ $database->db_user }}</p>
            </div>
            @endif
            @if($database->notes)
            <div class="col-span-2 sm:col-span-3">
                <p class="text-xs text-gray-400 dark:text-gray-500 mb-0.5">Notas</p>
                <p class="text-gray-700 dark:text-gray-300 italic">{{ $database->notes }}</p>
            </div>
            @endif
        </div>
    </div>

    {{-- ── Responsables ── --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">

        {{-- Cabecera --}}
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-700">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Responsables</h3>
                @if($database->responsibles->count())
                    <span class="text-xs text-gray-400 dark:text-gray-500">({{ $activeResponsibles->count() }} activos)</span>
                @endif
            </div>
            @can('databases.create/edit/delete')
            <button onclick="openModal('modal-resp')"
                    class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium
                           text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/30
                           rounded-lg hover:bg-emerald-100 dark:hover:bg-emerald-900/50 transition-colors">
                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Agregar
            </button>
            @endcan
        </div>

        @if($activeResponsibles->isEmpty() && $historicalResponsibles->isEmpty())
            {{-- Sin ningún responsable --}}
            <div class="flex flex-col items-center justify-center py-10 text-center">
                <div class="w-12 h-12 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mb-3">
                    <svg class="w-6 h-6 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Sin responsables asignados</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Usa el botón "Agregar" para asignar uno</p>
            </div>
        @else
            {{-- Activos --}}
            @if($activeResponsibles->isEmpty())
                <div class="px-5 py-4 flex items-center gap-2 text-sm text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    </svg>
                    Sin responsables activos actualmente
                </div>
            @else
                <div class="divide-y divide-gray-100 dark:divide-gray-700/60">
                    @foreach($activeResponsibles as $resp)
                    @php
                        $initials = strtoupper(
                            substr($resp->persona->apellido_paterno, 0, 1) .
                            substr($resp->persona->apellido_materno ?? '', 0, 1)
                        );
                        $fullName = $resp->persona->apellido_paterno . ' ' .
                                    ($resp->persona->apellido_materno ? $resp->persona->apellido_materno . ', ' : ', ') .
                                    $resp->persona->nombres;
                    @endphp
                    <div class="px-5 py-3.5">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center
                                        text-xs font-bold text-emerald-700 dark:text-emerald-300 flex-shrink-0">
                                {{ $initials }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-800 dark:text-gray-200 truncate">{{ $fullName }}</p>
                                <div class="flex items-center gap-2 mt-0.5 flex-wrap">
                                    <span class="text-xs text-emerald-600 dark:text-emerald-400 font-medium">
                                        {{ \App\Models\SystemDatabaseResponsible::levelLabel($resp->level) }}
                                    </span>
                                    <span class="text-xs text-gray-400 dark:text-gray-500">
                                        Desde {{ $resp->assigned_at->format('d/m/Y') }}
                                    </span>
                                    @php $firstDoc = $resp->documents->first(); @endphp
                                    @if($firstDoc?->document_type)
                                    <span class="inline-flex items-center gap-1 text-xs text-indigo-600 dark:text-indigo-400">
                                        · <span class="font-medium">{{ $docLabels[$firstDoc->document_type] ?? $firstDoc->document_type }}</span>
                                        @if($firstDoc->document_number) {{ $firstDoc->document_number }}@endif
                                        @if($firstDoc->document_date) · {{ $firstDoc->document_date->format('d/m/Y') }}@endif
                                    </span>
                                    @endif
                                </div>
                            </div>
                            @can('databases.create/edit/delete')
                            <div class="flex items-center gap-1 flex-shrink-0">
                                <button type="button"
                                        onclick="downloadResponsibleActa('{{ route('systems.databases.responsibles.pdf-data', [$system, $database, $resp]) }}', this)"
                                        title="Generar acta de asignación PDF"
                                        class="w-6 h-6 flex items-center justify-center rounded-md text-gray-400 dark:text-gray-500
                                               hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors disabled:opacity-50">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </button>
                                <button onclick="openDocUpload({{ $resp->id }})"
                                        title="Adjuntar documento"
                                        class="w-6 h-6 flex items-center justify-center rounded-md text-gray-400 dark:text-gray-500
                                               hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                    </svg>
                                </button>
                                <button onclick="openDeactivate({{ $resp->id }}, '{{ addslashes($fullName) }}')"
                                        title="Dar de baja"
                                        class="w-6 h-6 flex items-center justify-center rounded-md text-gray-400 dark:text-gray-500
                                               hover:text-orange-600 dark:hover:text-orange-400 hover:bg-orange-50 dark:hover:bg-orange-900/20 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                    </svg>
                                </button>
                                <button onclick="editResponsible({{ $resp->id }}, {{ $resp->load('persona')->toJson() }})"
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

                        {{-- Documentos adjuntos --}}
                        @if($resp->documents->isNotEmpty())
                        <div class="mt-2.5 ml-11 flex flex-wrap gap-1.5">
                            @foreach($resp->documents as $doc)
                            @php
                                $docExt    = strtolower(pathinfo($doc->original_name, PATHINFO_EXTENSION));
                                $docMeta   = collect([$docLabels[$doc->document_type] ?? null, $doc->document_number, $doc->document_date?->format('d/m/Y')])->filter()->implode(' · ');
                                $chipLabel = $doc->description ?: $doc->original_name;
                            @endphp
                            <div class="inline-flex items-center gap-1 pl-2 pr-1 py-0.5 rounded-full
                                        bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-700/40 text-xs">
                                <svg class="w-3 h-3 text-indigo-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                </svg>
                                <button type="button"
                                        onclick='openDocPreview({
                                            name:        {{ json_encode($chipLabel) }},
                                            description: {{ json_encode($docMeta ?: ($doc->description ? $doc->original_name : null)) }},
                                            previewUrl:  "{{ route('systems.databases.responsibles.documents.preview', [$system, $database, $resp, $doc]) }}",
                                            downloadUrl: "{{ route('systems.databases.responsibles.documents.download', [$system, $database, $resp, $doc]) }}",
                                            ext:         {{ json_encode($docExt) }}
                                        })'
                                        title="{{ $docMeta ? $docMeta . ' — ' . $doc->original_name : $doc->original_name }}"
                                        class="truncate max-w-[160px] text-indigo-700 dark:text-indigo-300 hover:underline cursor-pointer">
                                    {{ $chipLabel }}
                                </button>
                                <form action="{{ route('systems.databases.responsibles.documents.destroy', [$system, $database, $resp, $doc]) }}"
                                      method="POST" class="inline"
                                      onsubmit="sgDeleteForm(this,'¿Eliminar este documento?');return false">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="ml-0.5 w-4 h-4 flex items-center justify-center rounded-full
                                                   text-indigo-300 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                        <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            @endif

            {{-- Historial --}}
            @if($historicalResponsibles->isNotEmpty())
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
                        Historial de responsables
                        <span class="px-1.5 py-0.5 rounded-full bg-gray-200 dark:bg-gray-600 text-gray-600 dark:text-gray-300 normal-case tracking-normal font-semibold">
                            {{ $historicalResponsibles->count() }}
                        </span>
                    </span>
                    <svg id="history-chevron" class="w-3.5 h-3.5 transition-transform duration-200"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div id="resp-history" class="hidden divide-y divide-gray-100 dark:divide-gray-700/60 bg-gray-50/50 dark:bg-gray-800/30">
                    @foreach($historicalResponsibles as $resp)
                    @php
                        $initials = strtoupper(
                            substr($resp->persona->apellido_paterno, 0, 1) .
                            substr($resp->persona->apellido_materno ?? '', 0, 1)
                        );
                        $fullName = $resp->persona->apellido_paterno . ' ' .
                                    ($resp->persona->apellido_materno ? $resp->persona->apellido_materno . ', ' : ', ') .
                                    $resp->persona->nombres;
                    @endphp
                    <div class="px-5 py-3 flex items-center gap-3 opacity-70">
                        <div class="w-7 h-7 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center
                                    text-xs font-bold text-gray-500 dark:text-gray-400 flex-shrink-0">
                            {{ $initials }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300 truncate">{{ $fullName }}</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                                {{ \App\Models\SystemDatabaseResponsible::levelLabel($resp->level) }}
                                · {{ $resp->assigned_at->format('d/m/Y') }} – {{ $resp->unassigned_at?->format('d/m/Y') ?? '?' }}
                            </p>
                        </div>
                        @can('databases.create/edit/delete')
                        <div class="flex items-center gap-1 flex-shrink-0">
                            <button onclick="openReactivate({{ $resp->id }}, '{{ addslashes($fullName) }}')"
                                    title="Reactivar"
                                    class="w-6 h-6 flex items-center justify-center rounded-md text-gray-400 dark:text-gray-500
                                           hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                            </button>
                            <form action="{{ route('systems.databases.responsibles.destroy', [$system, $database, $resp]) }}"
                                  method="POST" id="del-hist-{{ $resp->id }}" class="inline">
                                @csrf @method('DELETE')
                                <button type="button" title="Eliminar del historial"
                                        onclick="dtConfirmDelete('del-hist-{{ $resp->id }}', '{{ addslashes($fullName) }}')"
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

{{-- ════════════ MODAL: Asignar / Editar Responsable ════════════ --}}
<div id="modal-resp" class="hidden fixed inset-0 z-50 items-center justify-center p-4" role="dialog" aria-modal="true">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeModal('modal-resp')"></div>
    <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 id="resp-modal-title" class="text-base font-semibold text-gray-900 dark:text-white">Asignar Responsable</h3>
            <button onclick="closeModal('modal-resp')"
                    class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400
                           hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form id="resp-form"
              action="{{ route('systems.databases.responsibles.store', [$system, $database]) }}"
              method="POST">
            @csrf
            <span id="resp-method"></span>
            <div class="p-6 space-y-4">

                {{-- Búsqueda de persona --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Persona <span class="text-red-500">*</span>
                    </label>
                    <div class="relative" id="resp-search-wrap">
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input type="text" id="resp-search-input" autocomplete="off"
                                   placeholder="Buscar por DNI o apellido/nombre..."
                                   class="w-full pl-9 pr-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600
                                          dark:bg-gray-700 dark:text-white text-sm
                                          focus:ring-emerald-500 focus:border-emerald-500">
                        </div>
                        <input type="hidden" name="persona_id" id="resp-persona_id" required>
                        <div id="resp-dropdown"
                             class="hidden absolute z-50 w-full mt-1 bg-white dark:bg-gray-800
                                    border border-gray-200 dark:border-gray-600 rounded-lg shadow-xl
                                    max-h-48 overflow-y-auto text-sm"></div>
                        <div id="resp-selected"
                             class="hidden mt-2 items-center gap-2 px-3 py-2 rounded-lg
                                    bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-700/40">
                            <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <span id="resp-selected-name" class="flex-1 text-sm font-medium text-emerald-700 dark:text-emerald-300 truncate"></span>
                            <button type="button" onclick="clearPersonaSearch('resp')"
                                    class="text-emerald-400 hover:text-red-500 transition-colors flex-shrink-0">
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
                            Nivel <span class="text-red-500">*</span>
                        </label>
                        <select name="level" id="resp-level" required
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                       dark:text-white focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                            <option value="principal">Responsable Principal</option>
                            <option value="soporte">Soporte Técnico</option>
                            <option value="supervision">Supervisión</option>
                            <option value="operador">Operador</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Fecha de asignación <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="assigned_at" id="resp-assigned_at" required
                               value="{{ now()->format('Y-m-d') }}"
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                      dark:text-white focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                    </div>
                </div>

            </div>
            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                <button type="button" onclick="closeModal('modal-resp')"
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700
                               border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancelar
                </button>
                <button id="sysdb-resp-submit-btn" type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2 text-sm font-medium text-white
                               bg-emerald-600 rounded-lg hover:bg-emerald-700 transition-colors shadow-sm">
                    <svg id="sysdb-resp-submit-icon" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <svg id="sysdb-resp-submit-spinner" class="hidden w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/>
                    </svg>
                    <span id="resp-submit-label">Asignar</span>
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
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Dar de Baja Responsable</h3>
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
                    dejará de figurar como responsable activo.
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
                <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Reactivar Responsable</h3>
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
                    volverá a figurar como responsable activo.
                </p>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Fecha de reactivación <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="assigned_at" id="reactivate-date" required
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                  dark:text-white focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
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
                               bg-emerald-600 rounded-lg hover:bg-emerald-700 transition-colors shadow-sm">
                    Reactivar
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ════════════ MODAL: Adjuntar Documento a Responsable ════════════ --}}
<div id="modal-doc-upload"
     class="hidden fixed inset-0 z-50 items-center justify-center p-4"
     role="dialog" aria-modal="true">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeModal('modal-doc-upload')"></div>
    <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                </svg>
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Adjuntar Documento</h3>
            </div>
            <button onclick="closeModal('modal-doc-upload')"
                    class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400
                           hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form id="doc-upload-form" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="p-6 space-y-4">
                <div x-data="{ dragging: false }"
                     @dragover.prevent="dragging = true"
                     @dragleave.prevent="dragging = false"
                     @drop.prevent="dragging = false; $refs.fileInput.files = $event.dataTransfer.files; updateFileName($event.dataTransfer.files[0]?.name)"
                     :class="dragging ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20' : 'border-gray-300 dark:border-gray-600 hover:border-indigo-400 dark:hover:border-indigo-500'"
                     class="border-2 border-dashed rounded-xl p-6 text-center transition-colors cursor-pointer"
                     onclick="document.getElementById('doc-file-input').click()">
                    <svg class="w-10 h-10 mx-auto text-gray-300 dark:text-gray-600 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    <p id="doc-file-label" class="text-sm text-gray-500 dark:text-gray-400">
                        Arrastra el archivo aquí o <span class="text-indigo-600 dark:text-indigo-400 font-medium">haz clic para seleccionar</span>
                    </p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">PDF, Word, Excel, imagen · Máx. 10 MB</p>
                    <input id="doc-file-input" x-ref="fileInput" type="file" name="file"
                           accept=".pdf,.doc,.docx,.xls,.xlsx,.png,.jpg,.jpeg"
                           class="hidden" required
                           onchange="updateFileName(this.files[0]?.name)">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Descripción <span class="text-gray-400 font-normal">(opcional)</span>
                    </label>
                    <input type="text" name="description" id="doc-description"
                           placeholder="Ej: Resolución de nombramiento 2024"
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                  dark:text-white focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>

                {{-- Datos del documento de respaldo --}}
                <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                    <p class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider mb-3 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Datos del documento <span class="font-normal text-gray-400 normal-case">(opcional)</span>
                    </p>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Tipo</label>
                            <select name="document_type" id="doc-document_type"
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                           dark:text-white focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="">Sin tipo</option>
                                <option value="resolucion_directoral">Resolución Directoral</option>
                                <option value="resolucion_jefatural">Resolución Jefatural</option>
                                <option value="memorando">Memorando</option>
                                <option value="oficio">Oficio</option>
                                <option value="contrato">Contrato</option>
                                <option value="acta">Acta</option>
                                <option value="otro">Otro</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">N° de documento</label>
                            <input type="text" name="document_number" id="doc-document_number"
                                   placeholder="R.D. N°042-2024-OTI"
                                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                          dark:text-white focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Fecha del documento</label>
                            <input type="date" name="document_date" id="doc-document_date"
                                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                          dark:text-white focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Observaciones</label>
                            <input type="text" name="document_notes" id="doc-document_notes"
                                   placeholder="Notas adicionales..."
                                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                          dark:text-white focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 rounded-b-2xl">
                <button type="button" onclick="closeModal('modal-doc-upload')"
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700
                               border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancelar
                </button>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2 text-sm font-medium text-white
                               bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    Subir documento
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Componente de previsualización --}}
<x-doc-preview-modal />

@push('scripts')
<script>
(function () {
    const storeUrl   = "{{ route('systems.databases.responsibles.store', [$system, $database]) }}";
    const updateBase = "{{ url('systems/' . $system->id . '/databases/' . $database->id . '/responsibles') }}/";
    const personaSearchUrl = "{{ route('admin.personas.search') }}";
    let searchTimer;

    // ── Modal helpers ────────────────────────────────────────────────────────
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
    const searchInput = document.getElementById('resp-search-input');
    const hiddenInput = document.getElementById('resp-persona_id');
    const dropdown    = document.getElementById('resp-dropdown');
    const selected    = document.getElementById('resp-selected');
    const selName     = document.getElementById('resp-selected-name');

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
                                    'hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition-colors';
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
        if (!document.getElementById('resp-search-wrap').contains(e.target)) {
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

    function resetPersonaSearch() {
        window.clearPersonaSearch();
        dropdown.classList.add('hidden');
        dropdown.innerHTML = '';
    }

    // ── Protección doble envío ───────────────────────────────────────────────
    let sysdbRespSubmitting = false;
    const sysdbRespBtn     = document.getElementById('sysdb-resp-submit-btn');
    const sysdbRespIcon    = document.getElementById('sysdb-resp-submit-icon');
    const sysdbRespSpinner = document.getElementById('sysdb-resp-submit-spinner');
    const sysdbRespLabel   = document.getElementById('resp-submit-label');

    document.getElementById('resp-form').addEventListener('submit', function (e) {
        if (sysdbRespSubmitting) { e.preventDefault(); return; }
        if (!this.checkValidity()) return;
        sysdbRespSubmitting = true;
        sysdbRespBtn.classList.add('pointer-events-none', 'opacity-75');
        sysdbRespIcon.classList.add('hidden');
        sysdbRespSpinner.classList.remove('hidden');
        sysdbRespLabel.textContent = 'Guardando…';
    });

    function resetSysdbRespBtn() {
        sysdbRespSubmitting = false;
        sysdbRespBtn.classList.remove('pointer-events-none', 'opacity-75');
        sysdbRespIcon.classList.remove('hidden');
        sysdbRespSpinner.classList.add('hidden');
    }

    // ── Botón Agregar → reset modal ──────────────────────────────────────────
    document.querySelector('[onclick="openModal(\'modal-resp\')"]')?.addEventListener('click', function () {
        document.getElementById('resp-modal-title').textContent  = 'Asignar Responsable';
        sysdbRespLabel.textContent = 'Asignar';
        document.getElementById('resp-form').action = storeUrl;
        document.getElementById('resp-method').innerHTML = '';
        document.getElementById('resp-form').reset();
        resetPersonaSearch();
        resetSysdbRespBtn();
        document.getElementById('resp-assigned_at').value = new Date().toISOString().slice(0, 10);
    });

    // ── Editar responsable ───────────────────────────────────────────────────
    window.editResponsible = function (id, data) {
        document.getElementById('resp-modal-title').textContent  = 'Editar Responsable';
        sysdbRespLabel.textContent = 'Guardar';
        resetSysdbRespBtn();
        document.getElementById('resp-form').action = updateBase + id;
        document.getElementById('resp-method').innerHTML = '<input type="hidden" name="_method" value="PUT">';

        hiddenInput.value = data.persona_id ?? '';
        if (data.persona_id && data.persona) {
            const nombre = (data.persona.apellido_paterno ?? '') + ' ' + (data.persona.apellido_materno ?? '') +
                           ', ' + (data.persona.nombres ?? '');
            selName.textContent = nombre.trim();
            selected.classList.remove('hidden');
            selected.classList.add('flex');
        }

        document.getElementById('resp-level').value       = data.level       ?? 'principal';
        document.getElementById('resp-assigned_at').value = data.assigned_at ?? '';
        openModal('modal-resp');
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
        const panel   = document.getElementById('resp-history');
        const chevron = document.getElementById('history-chevron');
        const hidden  = panel.classList.toggle('hidden');
        chevron.style.transform = hidden ? '' : 'rotate(180deg)';
    };

    // ── Documentos adjuntos ──────────────────────────────────────────────────
    const docUploadBase = "{{ url('systems/' . $system->id . '/databases/' . $database->id . '/responsibles') }}/";

    window.openDocUpload = function (responsibleId) {
        document.getElementById('doc-upload-form').action       = docUploadBase + responsibleId + '/documents';
        document.getElementById('doc-file-input').value         = '';
        document.getElementById('doc-description').value        = '';
        document.getElementById('doc-document_type').value      = '';
        document.getElementById('doc-document_number').value    = '';
        document.getElementById('doc-document_date').value      = '';
        document.getElementById('doc-document_notes').value     = '';
        document.getElementById('doc-file-label').innerHTML =
            'Arrastra el archivo aquí o <span class="text-indigo-600 dark:text-indigo-400 font-medium">haz clic para seleccionar</span>';
        openModal('modal-doc-upload');
    };

    window.updateFileName = function (name) {
        if (!name) return;
        document.getElementById('doc-file-label').innerHTML =
            '<span class="font-medium text-gray-700 dark:text-gray-300">' + name + '</span>';
    };

    // ── dtConfirmDelete helper (si no está global) ───────────────────────────
    if (typeof window.dtConfirmDelete === 'undefined') {
        window.dtConfirmDelete = function (formId, name) {
            if (confirm('¿Eliminar a ' + name + ' del historial?')) {
                document.getElementById(formId).submit();
            }
        };
    }
})();
</script>
@endpush
@endsection
