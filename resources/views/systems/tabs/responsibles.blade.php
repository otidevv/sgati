@php
    $levelColors = [
        'lider_proyecto' => 'bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300',
        'desarrollador'  => 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300',
        'mantenimiento'  => 'bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300',
        'administrador'  => 'bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300',
        'analista'       => 'bg-cyan-100 dark:bg-cyan-900/40 text-cyan-700 dark:text-cyan-300',
        'soporte'        => 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300',
        'supervision'    => 'bg-violet-100 dark:bg-violet-900/40 text-violet-700 dark:text-violet-300',
    ];
    $avatarColors = [
        'lider_proyecto' => 'bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300',
        'desarrollador'  => 'bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-300',
        'mantenimiento'  => 'bg-amber-100 dark:bg-amber-900/50 text-amber-700 dark:text-amber-300',
        'administrador'  => 'bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300',
        'analista'       => 'bg-cyan-100 dark:bg-cyan-900/50 text-cyan-700 dark:text-cyan-300',
        'soporte'        => 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300',
        'supervision'    => 'bg-violet-100 dark:bg-violet-900/50 text-violet-700 dark:text-violet-300',
    ];
    $leftBorder = [
        'lider_proyecto' => 'border-blue-400 dark:border-blue-500',
        'desarrollador'  => 'border-emerald-400 dark:border-emerald-500',
        'mantenimiento'  => 'border-amber-400 dark:border-amber-500',
        'administrador'  => 'border-indigo-400 dark:border-indigo-500',
        'analista'       => 'border-cyan-400 dark:border-cyan-500',
        'soporte'        => 'border-slate-300 dark:border-slate-500',
        'supervision'    => 'border-violet-400 dark:border-violet-500',
    ];
    $levelLabels = [
        'lider_proyecto' => 'Líder de Proyecto',
        'desarrollador'  => 'Desarrollador',
        'mantenimiento'  => 'Mantenimiento',
        'administrador'  => 'Administrador',
        'analista'       => 'Analista',
        'soporte'        => 'Soporte Técnico',
        'supervision'    => 'Supervisión',
    ];
    $docLabels   = ['resolucion_directoral'=>'R.D.','resolucion_jefatural'=>'R.J.','memorando'=>'Memo.','oficio'=>'Oficio','contrato'=>'Contrato','acta'=>'Acta','otro'=>'Doc.'];

    $activeResponsibles      = $system->responsibles->where('is_active', true);
    $historicalResponsibles  = $system->responsibles->where('is_active', false)->sortByDesc('unassigned_at');
@endphp

<div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
    {{-- Header --}}
    <div class="px-5 py-3.5 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Responsables del Sistema</h3>
            @if($system->responsibles->count())
                <span class="text-xs text-gray-400 dark:text-gray-500">({{ $system->responsibles->count() }})</span>
            @endif
        </div>
        <button onclick="sysRespOpenModal()"
                class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium
                       text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/30
                       rounded-lg hover:bg-emerald-100 dark:hover:bg-emerald-900/50 transition-colors">
            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Agregar
        </button>
    </div>

    {{-- Sin ningún responsable --}}
    @if($activeResponsibles->isEmpty() && $historicalResponsibles->isEmpty())
    <div class="flex flex-col items-center justify-center py-10 text-center">
        <div class="w-12 h-12 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mb-3">
            <svg class="w-6 h-6 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </div>
        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Sin responsables asignados</p>
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Usa el botón "Agregar" para asignar uno</p>
    </div>
    @else

    {{-- Responsables activos --}}
    @if($activeResponsibles->isEmpty())
    <div class="px-5 py-4 flex items-center gap-2 text-sm text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
        </svg>
        Sin responsables activos actualmente
    </div>
    @else
    <div class="divide-y divide-gray-100 dark:divide-gray-700/60">
        @foreach($activeResponsibles as $resp)
        @php
            $initials     = strtoupper(substr($resp->persona->apellido_paterno, 0, 1) . substr($resp->persona->apellido_materno ?? '', 0, 1));
            $levels       = (array) $resp->level;
            $primaryLevel = $levels[0] ?? 'soporte';
            $borderClass  = $leftBorder[$primaryLevel] ?? 'border-gray-300';
            $avatarClass  = $avatarColors[$primaryLevel] ?? 'bg-gray-100 text-gray-600';
        @endphp
        <div class="px-4 py-3 border-l-[3px] {{ $borderClass }} hover:bg-gray-50/60 dark:hover:bg-gray-700/30 transition-colors group/row">
            <div class="flex items-start gap-3">
                <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0 text-xs font-bold mt-0.5 {{ $avatarClass }}">
                    {{ $initials }}
                </div>
                <div class="flex-1 min-w-0">
                    {{-- Nombre + acciones --}}
                    <div class="flex items-center gap-1.5 min-w-0">
                        <span class="text-sm font-semibold text-gray-800 dark:text-gray-100 leading-snug truncate flex-1 min-w-0">
                            {{ $resp->persona->apellido_paterno }} {{ $resp->persona->apellido_materno }},
                            <span class="font-normal text-gray-600 dark:text-gray-300">{{ $resp->persona->nombres }}</span>
                        </span>
                        <div class="flex items-center gap-0.5 flex-shrink-0 opacity-0 group-hover/row:opacity-100 transition-opacity">
                            <button onclick="sysRespOpenDocUpload({{ $resp->id }})"
                                    title="Adjuntar documento"
                                    class="w-6 h-6 flex items-center justify-center rounded-md text-gray-400 dark:text-gray-500
                                           hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                </svg>
                            </button>
                            <button onclick="sysRespEdit({{ $resp->id }}, {{ $resp->toJson() }})"
                                    title="Editar"
                                    class="w-6 h-6 flex items-center justify-center rounded-md text-gray-400 dark:text-gray-500
                                           hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                </svg>
                            </button>
                            <button onclick="sysRespOpenDeactivate({{ $resp->id }}, '{{ addslashes($resp->persona->apellido_paterno . ' ' . $resp->persona->nombres) }}')"
                                    title="Dar de baja"
                                    class="w-6 h-6 flex items-center justify-center rounded-md text-gray-400 dark:text-gray-500
                                           hover:text-orange-600 dark:hover:text-orange-400 hover:bg-orange-50 dark:hover:bg-orange-900/20 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    {{-- Badges --}}
                    <div class="flex items-center gap-2 mt-1 flex-wrap">
                        @foreach($levels as $lvl)
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold uppercase tracking-wide {{ $levelColors[$lvl] ?? '' }}">
                            {{ $levelLabels[$lvl] ?? $lvl }}
                        </span>
                        @endforeach
                        <span class="inline-flex items-center gap-1 text-[10px] text-emerald-600 dark:text-emerald-400">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 dark:bg-emerald-400"></span>
                            Activo
                        </span>
                        <span class="text-[11px] text-gray-400 dark:text-gray-500 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Desde {{ $resp->assigned_at->format('d/m/Y') }}
                        </span>
                        @php $firstDoc = $resp->documents->first(); @endphp
                        @if($firstDoc?->document_type)
                        <div class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md
                                    bg-indigo-50 dark:bg-indigo-900/30 border border-indigo-200 dark:border-indigo-700/50
                                    text-[11px] text-indigo-600 dark:text-indigo-400">
                            <svg class="w-3 h-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span class="font-medium">{{ $docLabels[$firstDoc->document_type] ?? $firstDoc->document_type }}</span>
                            @if($firstDoc->document_number)<span class="text-indigo-300 dark:text-indigo-600">·</span> {{ $firstDoc->document_number }}@endif
                            @if($firstDoc->document_date)<span class="text-indigo-300 dark:text-indigo-600">·</span> {{ $firstDoc->document_date->format('d/m/Y') }}@endif
                        </div>
                        @endif
                    </div>
                    {{-- Documentos adjuntos --}}
                    @if($resp->documents->count())
                    <div class="mt-2 flex flex-wrap gap-1.5">
                        @foreach($resp->documents as $doc)
                        @php
                            $docExt    = strtolower(pathinfo($doc->original_name, PATHINFO_EXTENSION));
                            $docMeta   = collect([$docLabels[$doc->document_type] ?? null, $doc->document_number, $doc->document_date?->format('d/m/Y')])->filter()->implode(' · ');
                            $chipLabel = $doc->description ?: $doc->original_name;
                        @endphp
                        <div class="inline-flex items-center gap-1 pl-2 pr-1 py-0.5 rounded-full
                                    bg-indigo-50 dark:bg-indigo-900/30 border border-indigo-200 dark:border-indigo-700
                                    text-indigo-700 dark:text-indigo-300 text-[11px] font-medium max-w-xs">
                            <svg class="w-3 h-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                            <button type="button"
                                    onclick='openDocPreview({
                                        name:        {{ json_encode($chipLabel) }},
                                        description: {{ json_encode($docMeta ?: ($doc->description ? $doc->original_name : null)) }},
                                        previewUrl:  "{{ route('systems.responsibles.documents.preview', [$system, $resp, $doc]) }}",
                                        downloadUrl: "{{ route('systems.responsibles.documents.download', [$system, $resp, $doc]) }}",
                                        ext:         {{ json_encode($docExt) }}
                                    })'
                                    title="{{ $docMeta ? $docMeta . ' — ' . $doc->original_name : $doc->original_name }}"
                                    class="truncate max-w-[160px] hover:underline cursor-pointer">
                                {{ $chipLabel }}
                            </button>
                            <form action="{{ route('systems.responsibles.documents.destroy', [$system, $resp, $doc]) }}"
                                  method="POST" class="inline" onsubmit="sgDeleteForm(this,'¿Eliminar este documento?');return false">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="ml-0.5 w-4 h-4 flex items-center justify-center rounded-full
                                               text-indigo-400 hover:text-red-500 hover:bg-red-100 dark:hover:bg-red-900/40 transition-colors">
                                    <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- Historial de responsables --}}
    @if($historicalResponsibles->isNotEmpty())
    <div class="border-t border-gray-100 dark:border-gray-700/60">
        <button type="button" onclick="sysRespToggleHistory()"
                class="w-full flex items-center justify-between px-5 py-2.5 text-xs font-semibold
                       text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/30
                       uppercase tracking-wider transition-colors">
            <span class="flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Historial de responsables
                <span class="px-1.5 py-0.5 rounded-full bg-gray-200 dark:bg-gray-600 text-gray-600 dark:text-gray-300 normal-case tracking-normal font-semibold">
                    {{ $historicalResponsibles->count() }}
                </span>
            </span>
            <svg id="sys-history-chevron" class="w-3.5 h-3.5 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>

        <div id="sys-resp-history" class="hidden divide-y divide-gray-100 dark:divide-gray-700/60 bg-gray-50/50 dark:bg-gray-800/30">
            @foreach($historicalResponsibles as $resp)
            @php
                $initials    = strtoupper(substr($resp->persona->apellido_paterno, 0, 1) . substr($resp->persona->apellido_materno ?? '', 0, 1));
                $avatarClass = 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400';
                $histLevels  = (array) $resp->level;
            @endphp
            <div class="px-4 py-3 border-l-[3px] border-gray-300 dark:border-gray-600 opacity-75 hover:opacity-100 hover:bg-gray-100/60 dark:hover:bg-gray-700/30 transition-all group/hist">
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0 text-xs font-bold mt-0.5 {{ $avatarClass }}">
                        {{ $initials }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-1.5 min-w-0">
                            <span class="text-sm font-semibold text-gray-600 dark:text-gray-400 leading-snug truncate flex-1 min-w-0">
                                {{ $resp->persona->apellido_paterno }} {{ $resp->persona->apellido_materno }},
                                <span class="font-normal">{{ $resp->persona->nombres }}</span>
                            </span>
                            <div class="flex items-center gap-0.5 flex-shrink-0 opacity-0 group-hover/hist:opacity-100 transition-opacity">
                                <button onclick="sysRespOpenReactivate({{ $resp->id }}, '{{ addslashes($resp->persona->apellido_paterno . ' ' . $resp->persona->nombres) }}')"
                                        title="Reactivar"
                                        class="w-6 h-6 flex items-center justify-center rounded-md text-gray-400 dark:text-gray-500
                                               hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                </button>
                                <form action="{{ route('systems.responsibles.destroy', [$system, $resp]) }}"
                                      method="POST" id="sys-del-hist-{{ $resp->id }}" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="button"
                                            title="Eliminar del historial"
                                            onclick="dtConfirmDelete('sys-del-hist-{{ $resp->id }}', '{{ addslashes($resp->persona->apellido_paterno . ' ' . $resp->persona->nombres) }}')"
                                            class="w-6 h-6 flex items-center justify-center rounded-md text-gray-400 dark:text-gray-500
                                                   hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 mt-1 flex-wrap">
                            @foreach($histLevels as $lvl)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold uppercase tracking-wide {{ $levelColors[$lvl] ?? '' }} opacity-60">
                                {{ $levelLabels[$lvl] ?? $lvl }}
                            </span>
                            @endforeach
                            <span class="inline-flex items-center gap-1 text-[11px] text-gray-400 dark:text-gray-500 font-medium">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                {{ $resp->assigned_at->format('d/m/Y') }}
                                <svg class="w-3 h-3 text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                </svg>
                                {{ $resp->unassigned_at?->format('d/m/Y') ?? 'N/A' }}
                                @php $dias = $resp->assigned_at->diffInDays($resp->unassigned_at ?? now()); @endphp
                                <span class="text-gray-300 dark:text-gray-600">({{ $dias }} {{ $dias === 1 ? 'día' : 'días' }})</span>
                            </span>
                            @php $histDoc = $resp->documents->first(); @endphp
                            @if($histDoc?->document_type)
                            <div class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md
                                        bg-gray-100 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600
                                        text-[11px] text-gray-500 dark:text-gray-400">
                                <span class="font-medium">{{ $docLabels[$histDoc->document_type] ?? $histDoc->document_type }}</span>
                                @if($histDoc->document_number)<span class="text-gray-300 dark:text-gray-600">·</span> {{ $histDoc->document_number }}@endif
                            </div>
                            @endif
                            @if($histDoc?->document_notes)
                            <span class="text-[11px] text-gray-400 dark:text-gray-500 italic">{{ $histDoc->document_notes }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    @endif
</div>

{{-- ════════════════════════════════════════════════
     MODAL: Asignar / Editar Responsable
════════════════════════════════════════════════ --}}
<div id="sys-modal-responsible"
     class="hidden fixed inset-0 z-50 items-center justify-center p-4"
     role="dialog" aria-modal="true">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeSysModal('sys-modal-responsible')"></div>
    <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 id="sys-resp-modal-title" class="text-base font-semibold text-gray-900 dark:text-white">
                Asignar Responsable
            </h3>
            <button onclick="closeSysModal('sys-modal-responsible')"
                    class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400
                           hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form id="sys-resp-form"
              action="{{ route('systems.responsibles.store', $system) }}"
              method="POST">
            @csrf
            <span id="sys-resp-method"></span>

            <div class="p-6 space-y-4">
                {{-- Persona --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Persona <span class="text-red-500">*</span>
                    </label>
                    <div class="relative" id="sys-resp-search-wrap">
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input type="text" id="sys-resp-search-input" autocomplete="off"
                                   placeholder="Buscar por DNI o apellido/nombre..."
                                   class="w-full pl-9 pr-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600
                                          dark:bg-gray-700 dark:text-white text-sm
                                          focus:ring-emerald-500 focus:border-emerald-500">
                        </div>
                        <input type="hidden" name="persona_id" id="sys-resp-persona_id" required>
                        <div id="sys-resp-dropdown"
                             class="hidden absolute z-50 w-full mt-1 bg-white dark:bg-gray-800
                                    border border-gray-200 dark:border-gray-600 rounded-lg shadow-xl
                                    max-h-48 overflow-y-auto text-sm">
                        </div>
                        <div id="sys-resp-selected"
                             class="hidden mt-2 items-center gap-2 px-3 py-2 rounded-lg
                                    bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-700/40">
                            <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <span id="sys-resp-selected-name" class="flex-1 text-sm font-medium text-emerald-700 dark:text-emerald-300 truncate"></span>
                            <button type="button" onclick="clearPersonaSearch('sys-resp')"
                                    class="text-emerald-400 hover:text-red-500 transition-colors flex-shrink-0">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">Escribe al menos 4 caracteres para buscar</p>
                    </div>
                </div>

                {{-- Niveles (multi) --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Nivel(es) <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-2" id="sys-resp-levels-grid">
                        @foreach([
                            'lider_proyecto' => ['label'=>'Líder de Proyecto','color'=>'blue'],
                            'desarrollador'  => ['label'=>'Desarrollador','color'=>'emerald'],
                            'mantenimiento'  => ['label'=>'Mantenimiento','color'=>'amber'],
                            'administrador'  => ['label'=>'Administrador','color'=>'indigo'],
                            'analista'       => ['label'=>'Analista','color'=>'cyan'],
                            'soporte'        => ['label'=>'Soporte Técnico','color'=>'slate'],
                            'supervision'    => ['label'=>'Supervisión','color'=>'violet'],
                        ] as $val => $item)
                        <label class="sys-level-card flex items-center gap-2.5 px-3 py-2 rounded-lg border
                                      border-gray-200 dark:border-gray-600 cursor-pointer transition-all
                                      hover:border-emerald-400 dark:hover:border-emerald-500">
                            <input type="checkbox" name="level[]" value="{{ $val }}"
                                   class="sys-level-cb rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                            <span class="text-xs font-medium text-gray-700 dark:text-gray-300 leading-tight">{{ $item['label'] }}</span>
                        </label>
                        @endforeach
                    </div>
                    <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">Selecciona al menos un nivel</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Fecha de asignación <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="assigned_at" id="sys-resp-assigned_at" required
                           value="{{ now()->format('Y-m-d') }}"
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                  dark:text-white focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                </div>

                <div class="flex items-center gap-3">
                    <input type="checkbox" name="is_active" id="sys-resp-is_active" value="1"
                           checked class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                    <label for="sys-resp-is_active" class="text-sm text-gray-700 dark:text-gray-300 cursor-pointer">
                        Asignación activa
                    </label>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                <button type="button" onclick="closeSysModal('sys-modal-responsible')"
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700
                               border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                    Cancelar
                </button>
                <button id="sys-resp-submit-btn" type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2 text-sm font-medium text-white
                               bg-emerald-600 rounded-lg hover:bg-emerald-700 transition-colors shadow-sm">
                    <svg id="sys-resp-submit-icon" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <svg id="sys-resp-submit-spinner" class="hidden w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/>
                    </svg>
                    <span id="sys-resp-submit-label">Asignar</span>
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL: Dar de Baja --}}
<div id="sys-modal-deactivate"
     class="hidden fixed inset-0 z-50 items-center justify-center p-4"
     role="dialog" aria-modal="true">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeSysModal('sys-modal-deactivate')"></div>
    <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                </svg>
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Dar de Baja Responsable</h3>
            </div>
            <button onclick="closeSysModal('sys-modal-deactivate')"
                    class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400
                           hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form id="sys-deactivate-form" method="POST">
            @csrf
            <input type="hidden" name="_method" value="PATCH">
            <div class="p-6 space-y-4">
                <div class="flex items-start gap-3 p-3 rounded-lg bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-700/40">
                    <svg class="w-4 h-4 text-orange-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-sm text-orange-700 dark:text-orange-300">
                        El responsable <strong id="sys-deactivate-name" class="font-semibold"></strong> pasará al historial conservando su período de gestión.
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Fecha de baja <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="unassigned_at" id="sys-deactivate-date" required
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                  dark:text-white focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Motivo / Observaciones <span class="text-gray-400 font-normal">(opcional)</span>
                    </label>
                    <textarea name="deactivate_notes" id="sys-deactivate-notes" rows="2"
                              placeholder="Ej: Renuncia voluntaria, Fin de contrato, Reasignación..."
                              class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                     dark:text-white focus:ring-orange-500 focus:border-orange-500 sm:text-sm resize-none"></textarea>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                <button type="button" onclick="closeSysModal('sys-modal-deactivate')"
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700
                               border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                    Cancelar
                </button>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2 text-sm font-medium text-white
                               bg-orange-600 rounded-lg hover:bg-orange-700 transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                    </svg>
                    Dar de Baja
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL: Reactivar --}}
<div id="sys-modal-reactivate"
     class="hidden fixed inset-0 z-50 items-center justify-center p-4"
     role="dialog" aria-modal="true">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeSysModal('sys-modal-reactivate')"></div>
    <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-sm">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Reactivar Responsable</h3>
            </div>
            <button onclick="closeSysModal('sys-modal-reactivate')"
                    class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400
                           hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form id="sys-reactivate-form" method="POST">
            @csrf
            <input type="hidden" name="_method" value="PATCH">
            <div class="p-6 space-y-4">
                <p class="text-sm text-gray-600 dark:text-gray-300">
                    Reactivar a <strong id="sys-reactivate-name" class="font-semibold text-gray-800 dark:text-gray-100"></strong> como responsable activo.
                </p>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Nueva fecha de asignación <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="assigned_at" id="sys-reactivate-date" required
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                  dark:text-white focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                <button type="button" onclick="closeSysModal('sys-modal-reactivate')"
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700
                               border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                    Cancelar
                </button>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2 text-sm font-medium text-white
                               bg-emerald-600 rounded-lg hover:bg-emerald-700 transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Reactivar
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL: Adjuntar Documento --}}
<div id="sys-modal-doc-upload"
     class="hidden fixed inset-0 z-50 items-center justify-center p-4"
     role="dialog" aria-modal="true">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeSysModal('sys-modal-doc-upload')"></div>
    <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                </svg>
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Adjuntar Documento</h3>
            </div>
            <button onclick="closeSysModal('sys-modal-doc-upload')"
                    class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400
                           hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form id="sys-doc-upload-form" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="p-6 space-y-4">
                <div x-data="{ dragging: false }"
                     @dragover.prevent="dragging = true"
                     @dragleave.prevent="dragging = false"
                     @drop.prevent="dragging = false; $refs.sysFileInput.files = $event.dataTransfer.files; sysUpdateFileName($event.dataTransfer.files[0]?.name)"
                     :class="dragging ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20' : 'border-gray-300 dark:border-gray-600 hover:border-indigo-400 dark:hover:border-indigo-500'"
                     class="border-2 border-dashed rounded-xl p-6 text-center transition-colors cursor-pointer"
                     onclick="document.getElementById('sys-doc-file-input').click()">
                    <svg class="w-10 h-10 mx-auto text-gray-300 dark:text-gray-600 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    <p id="sys-doc-file-label" class="text-sm text-gray-500 dark:text-gray-400">
                        Arrastra el archivo aquí o <span class="text-indigo-600 dark:text-indigo-400 font-medium">haz clic para seleccionar</span>
                    </p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">PDF, Word, Excel, imagen · Máx. 10 MB</p>
                    <input id="sys-doc-file-input" x-ref="sysFileInput" type="file" name="file"
                           accept=".pdf,.doc,.docx,.xls,.xlsx,.png,.jpg,.jpeg"
                           class="hidden" required
                           onchange="sysUpdateFileName(this.files[0]?.name)">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Descripción <span class="text-gray-400 font-normal">(opcional)</span>
                    </label>
                    <input type="text" name="description" id="sys-doc-description"
                           placeholder="Ej: Resolución de nombramiento 2024"
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                  dark:text-white focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
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
                            <select name="document_type" id="sys-doc-document_type"
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
                            <input type="text" name="document_number" id="sys-doc-document_number"
                                   placeholder="R.D. N°042-2024-OTI"
                                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                          dark:text-white focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Fecha del documento</label>
                            <input type="date" name="document_date" id="sys-doc-document_date"
                                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                          dark:text-white focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Observaciones</label>
                            <input type="text" name="document_notes" id="sys-doc-document_notes"
                                   placeholder="Notas adicionales..."
                                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                          dark:text-white focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 rounded-b-2xl">
                <button type="button" onclick="closeSysModal('sys-modal-doc-upload')"
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700
                               border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
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

@push('scripts')
<script>
// ── Helpers de modal ──────────────────────────────────────────────────
function openSysModal(id)  { const m = document.getElementById(id); m.classList.remove('hidden'); m.classList.add('flex'); }
function closeSysModal(id) { const m = document.getElementById(id); m.classList.add('hidden'); m.classList.remove('flex'); }

// ── Persona autocomplete (reutiliza la fn global initPersonaSearch) ───
const sysPersonaSearchUrl = "{{ route('admin.personas.search') }}";

(function() {
    const prefix = 'sys-resp';
    const searchInput = document.getElementById(prefix + '-search-input');
    const hiddenInput = document.getElementById(prefix + '-persona_id');
    const dropdown    = document.getElementById(prefix + '-dropdown');
    const selected    = document.getElementById(prefix + '-selected');
    const selName     = document.getElementById(prefix + '-selected-name');
    let timer;

    searchInput.addEventListener('input', function () {
        const q = this.value.trim();
        clearTimeout(timer);
        dropdown.classList.add('hidden');
        dropdown.innerHTML = '';
        if (q.length < 4) return;

        dropdown.innerHTML = '<p class="px-4 py-3 text-xs text-gray-400">Buscando...</p>';
        dropdown.classList.remove('hidden');

        timer = setTimeout(async () => {
            try {
                const res  = await fetch(sysPersonaSearchUrl + '?q=' + encodeURIComponent(q));
                const data = await res.json();
                dropdown.innerHTML = '';
                if (!data.length) {
                    dropdown.innerHTML = '<p class="px-4 py-3 text-xs text-gray-400">Sin resultados</p>';
                    return;
                }
                data.forEach(p => {
                    const btn = document.createElement('button');
                    btn.type  = 'button';
                    btn.className = 'w-full text-left px-4 py-2.5 text-sm text-gray-800 dark:text-gray-200 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 hover:text-emerald-700 transition-colors';
                    btn.innerHTML = `${p.apellido_paterno} ${p.apellido_materno ?? ''}, ${p.nombres} &mdash; <span class="font-mono">${p.dni}</span>`;
                    btn.addEventListener('click', () => {
                        hiddenInput.value   = p.id;
                        selName.textContent = `${p.apellido_paterno} ${p.apellido_materno ?? ''}, ${p.nombres} (${p.dni})`;
                        searchInput.value   = '';
                        dropdown.classList.add('hidden');
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
        if (!document.getElementById(prefix + '-search-wrap').contains(e.target)) {
            dropdown.classList.add('hidden');
        }
    });
})();

// ── Responsables ──────────────────────────────────────────────────────
const sysRespStoreUrl  = "{{ route('systems.responsibles.store', $system) }}";
const sysRespBase      = "{{ url('systems/' . $system->id . '/responsibles') }}/";

function sysRespResetLevels() {
    document.querySelectorAll('#sys-resp-levels-grid .sys-level-cb').forEach(cb => {
        cb.checked = false;
        cb.closest('label').classList.remove('border-emerald-500','bg-emerald-50','dark:bg-emerald-900/20');
    });
}

function sysRespSetLevels(levels) {
    const arr = Array.isArray(levels) ? levels : (levels ? [levels] : []);
    document.querySelectorAll('#sys-resp-levels-grid .sys-level-cb').forEach(cb => {
        const active = arr.includes(cb.value);
        cb.checked = active;
        const card = cb.closest('label');
        card.classList.toggle('border-emerald-500', active);
        card.classList.toggle('bg-emerald-50', active);
        card.classList.toggle('dark:bg-emerald-900/20', active);
    });
}

// Toggle visual state on checkbox click
document.querySelectorAll('#sys-resp-levels-grid .sys-level-cb').forEach(cb => {
    cb.addEventListener('change', function () {
        const card = this.closest('label');
        card.classList.toggle('border-emerald-500', this.checked);
        card.classList.toggle('bg-emerald-50', this.checked);
        card.classList.toggle('dark:bg-emerald-900/20', this.checked);
    });
});

function sysRespOpenModal() {
    document.getElementById('sys-resp-modal-title').textContent = 'Asignar Responsable';
    sysRespLabel.textContent = 'Asignar';
    resetSysRespBtn();
    document.getElementById('sys-resp-form').action = sysRespStoreUrl;
    document.getElementById('sys-resp-method').innerHTML = '';
    document.getElementById('sys-resp-form').reset();
    // reset persona
    document.getElementById('sys-resp-persona_id').value = '';
    document.getElementById('sys-resp-selected-name').textContent = '';
    const sel = document.getElementById('sys-resp-selected');
    sel.classList.add('hidden'); sel.classList.remove('flex');
    document.getElementById('sys-resp-search-input').value = '';
    document.getElementById('sys-resp-dropdown').classList.add('hidden');
    document.getElementById('sys-resp-assigned_at').value = new Date().toISOString().slice(0, 10);
    document.getElementById('sys-resp-is_active').checked = true;
    sysRespResetLevels();
    openSysModal('sys-modal-responsible');
}

function sysRespEdit(id, data) {
    document.getElementById('sys-resp-modal-title').textContent = 'Editar Responsable';
    sysRespLabel.textContent = 'Guardar';
    resetSysRespBtn();
    document.getElementById('sys-resp-form').action = sysRespBase + id;
    document.getElementById('sys-resp-method').innerHTML = '<input type="hidden" name="_method" value="PUT">';
    document.getElementById('sys-resp-persona_id').value = data.persona_id ?? '';
    if (data.persona_id && data.persona) {
        const selName = document.getElementById('sys-resp-selected-name');
        const sel     = document.getElementById('sys-resp-selected');
        selName.textContent = `${data.persona.apellido_paterno} ${data.persona.apellido_materno ?? ''}, ${data.persona.nombres}`;
        sel.classList.remove('hidden');
        sel.classList.add('flex');
    }
    sysRespSetLevels(data.level ?? ['soporte']);
    document.getElementById('sys-resp-assigned_at').value = data.assigned_at ?? '';
    document.getElementById('sys-resp-is_active').checked = data.is_active == 1;
    openSysModal('sys-modal-responsible');
}

function sysRespOpenDeactivate(id, nombre) {
    document.getElementById('sys-deactivate-name').textContent = nombre;
    document.getElementById('sys-deactivate-form').action = sysRespBase + id + '/deactivate';
    document.getElementById('sys-deactivate-date').value  = new Date().toISOString().slice(0, 10);
    document.getElementById('sys-deactivate-notes').value = '';
    openSysModal('sys-modal-deactivate');
}

function sysRespOpenReactivate(id, nombre) {
    document.getElementById('sys-reactivate-name').textContent = nombre;
    document.getElementById('sys-reactivate-form').action = sysRespBase + id + '/reactivate';
    document.getElementById('sys-reactivate-date').value  = new Date().toISOString().slice(0, 10);
    openSysModal('sys-modal-reactivate');
}

function sysRespToggleHistory() {
    const panel   = document.getElementById('sys-resp-history');
    const chevron = document.getElementById('sys-history-chevron');
    const hidden  = panel.classList.toggle('hidden');
    chevron.style.transform = hidden ? '' : 'rotate(180deg)';
}

// ── Protección doble envío ──────────────────────────────────────────────
let sysRespSubmitting = false;
const sysRespBtn     = document.getElementById('sys-resp-submit-btn');
const sysRespIcon    = document.getElementById('sys-resp-submit-icon');
const sysRespSpinner = document.getElementById('sys-resp-submit-spinner');
const sysRespLabel   = document.getElementById('sys-resp-submit-label');

function resetSysRespBtn() {
    sysRespSubmitting = false;
    sysRespBtn.classList.remove('pointer-events-none', 'opacity-75');
    sysRespIcon.classList.remove('hidden');
    sysRespSpinner.classList.add('hidden');
}

// Validar al menos un nivel seleccionado antes de enviar
document.getElementById('sys-resp-form').addEventListener('submit', function (e) {
    const checked = document.querySelectorAll('#sys-resp-levels-grid .sys-level-cb:checked');
    if (!checked.length) {
        e.preventDefault();
        document.getElementById('sys-resp-levels-grid').classList.add('ring-2','ring-red-400','rounded-lg');
        setTimeout(() => document.getElementById('sys-resp-levels-grid').classList.remove('ring-2','ring-red-400','rounded-lg'), 2000);
        return;
    }
    if (sysRespSubmitting) { e.preventDefault(); return; }
    sysRespSubmitting = true;
    sysRespBtn.classList.add('pointer-events-none', 'opacity-75');
    sysRespIcon.classList.add('hidden');
    sysRespSpinner.classList.remove('hidden');
    sysRespLabel.textContent = 'Guardando…';
});

// ── Documentos ────────────────────────────────────────────────────────
const sysDocBase = "{{ url('systems/' . $system->id . '/responsibles') }}/";

function sysRespOpenDocUpload(responsibleId) {
    document.getElementById('sys-doc-upload-form').action = sysDocBase + responsibleId + '/documents';
    document.getElementById('sys-doc-file-input').value      = '';
    document.getElementById('sys-doc-description').value     = '';
    document.getElementById('sys-doc-document_type').value   = '';
    document.getElementById('sys-doc-document_number').value = '';
    document.getElementById('sys-doc-document_date').value   = '';
    document.getElementById('sys-doc-document_notes').value  = '';
    document.getElementById('sys-doc-file-label').innerHTML  =
        'Arrastra el archivo aquí o <span class="text-indigo-600 dark:text-indigo-400 font-medium">haz clic para seleccionar</span>';
    openSysModal('sys-modal-doc-upload');
}

function sysUpdateFileName(name) {
    if (!name) return;
    document.getElementById('sys-doc-file-label').innerHTML =
        '<span class="font-medium text-gray-700 dark:text-gray-300">' + name + '</span>';
}
</script>
@endpush
