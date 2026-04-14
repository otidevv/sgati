@extends('layouts.app')
@section('title', $service->service_name . ' — ' . $system->name)

@section('content')
@php
    $typeColors = match($service->service_type) {
        'rest_api' => 'bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-300',
        'soap'     => 'bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300',
        'sftp'     => 'bg-purple-100 dark:bg-purple-900/40 text-purple-700 dark:text-purple-300',
        'smtp'     => 'bg-orange-100 dark:bg-orange-900/40 text-orange-700 dark:text-orange-300',
        'ldap'     => 'bg-yellow-100 dark:bg-yellow-900/40 text-yellow-700 dark:text-yellow-300',
        'database' => 'bg-slate-100 dark:bg-slate-700/40 text-slate-700 dark:text-slate-300',
        default    => 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300',
    };
    $envColors = match($service->environment) {
        'production'  => 'bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-300 ring-red-200 dark:ring-red-700',
        'staging'     => 'bg-yellow-50 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300 ring-yellow-200 dark:ring-yellow-700',
        'development' => 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 ring-emerald-200 dark:ring-emerald-700',
        default       => 'bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300 ring-gray-200 dark:ring-gray-600',
    };
    $requestFields  = $service->fields->where('direction', 'request');
    $responseFields = $service->fields->where('direction', 'response');
    $sentDocs       = $service->documents->where('direction', 'sent');
    $receivedDocs   = $service->documents->where('direction', 'received');

    $tokenExpired = $service->token_expires_at && $service->token_expires_at->isPast();
    $tokenSoon    = !$tokenExpired && $service->token_expires_at && $service->token_expires_at->diffInDays(now()) <= 30;
@endphp

<div class="space-y-6">

    {{-- ── Encabezado (ancho completo) ── --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('systems.show', $system) }}?tab=services"
           class="inline-flex items-center justify-center w-10 h-10 rounded-lg
                  bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300
                  hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 flex-wrap">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white truncate">{{ $service->service_name }}</h1>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-semibold {{ $typeColors }}">
                    {{ strtoupper(str_replace('_', ' ', $service->service_type)) }}
                </span>
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium ring-1 {{ $envColors }}">
                    {{ match($service->environment) { 'production' => 'Producción', 'staging' => 'Staging', default => 'Desarrollo' } }}
                </span>
                @if($service->version)
                <span class="text-xs text-gray-400 dark:text-gray-500 font-mono">{{ $service->version }}</span>
                @endif
                <span class="inline-flex items-center gap-1 text-xs {{ $service->is_active ? 'text-green-600 dark:text-green-400' : 'text-gray-400 dark:text-gray-500' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $service->is_active ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                    {{ $service->is_active ? 'Activo' : 'Inactivo' }}
                </span>
            </div>
            <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">
                {{ $service->direction === 'consumed' ? 'Consumido por' : 'Expuesto por' }} {{ $system->name }}
            </p>
        </div>
        @can('services.create/edit/delete')
        <a href="{{ route('systems.services.edit', [$system, $service]) }}"
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

    {{-- ── Grid 2 columnas ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 items-start">

        {{-- ══════════════════════════════════════════════
             COLUMNA IZQUIERDA (3/5): Info · Campos · Docs
        ══════════════════════════════════════════════ --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- ── Info general ── --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Información general</h3>
                </div>
                <div class="p-6 grid grid-cols-2 sm:grid-cols-3 gap-5 text-sm">
                    @if($service->endpoint_url)
                    <div class="col-span-2 sm:col-span-3">
                        <p class="text-xs text-gray-400 dark:text-gray-500 mb-0.5">Endpoint</p>
                        <a href="{{ $service->endpoint_url }}" target="_blank"
                           class="font-mono text-blue-600 dark:text-blue-400 hover:underline break-all text-xs">{{ $service->endpoint_url }}</a>
                    </div>
                    @endif

                    @if($service->provider_type)
                    <div>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mb-0.5">Proveedor</p>
                        @if($service->provider_type === 'internal' && $service->providerSystem)
                            <p class="font-medium text-gray-800 dark:text-gray-200">
                                {{ $service->providerSystem->name }}
                                @if($service->providerSystem->acronym)
                                <span class="text-xs text-gray-400">({{ $service->providerSystem->acronym }})</span>
                                @endif
                            </p>
                        @elseif($service->provider_type === 'external')
                            <p class="font-medium text-gray-800 dark:text-gray-200">{{ $service->provider_name ?? '—' }}</p>
                        @endif
                    </div>
                    @endif

                    @if($service->requestedBy)
                    <div>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mb-0.5">Solicitado por</p>
                        <p class="font-medium text-gray-800 dark:text-gray-200">
                            {{ $service->requestedBy->apellido_paterno }} {{ $service->requestedBy->nombres }}
                        </p>
                    </div>
                    @endif

                    @if($service->valid_from || $service->valid_until)
                    <div>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mb-0.5">Vigencia</p>
                        <p class="text-gray-800 dark:text-gray-200">
                            {{ $service->valid_from?->format('d/m/Y') ?? '?' }}
                            — {{ $service->valid_until?->format('d/m/Y') ?? 'indefinido' }}
                        </p>
                    </div>
                    @endif

                    @if($service->auth_type)
                    <div>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mb-0.5">Autenticación</p>
                        <p class="text-gray-800 dark:text-gray-200">{{ $service->auth_type }}</p>
                    </div>
                    @endif

                    @if($service->description)
                    <div class="col-span-2 sm:col-span-3">
                        <p class="text-xs text-gray-400 dark:text-gray-500 mb-0.5">Descripción</p>
                        <p class="text-gray-700 dark:text-gray-300 italic">{{ $service->description }}</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- ── Campos de la API ── --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                        </svg>
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Campos / Contrato</h3>
                        @if($service->fields->count())
                        <span class="text-xs text-gray-400">({{ $requestFields->count() }} request · {{ $responseFields->count() }} response)</span>
                        @endif
                    </div>
                    @can('services.create/edit/delete')
                    <button onclick="openModal('modal-field')"
                            class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium
                                   text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/30
                                   rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-900/50 transition-colors">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Agregar campo
                    </button>
                    @endcan
                </div>

                @if($service->fields->isEmpty())
                <div class="py-10 text-center text-sm text-gray-400 dark:text-gray-500">
                    Sin campos registrados. Documenta el contrato de la API.
                </div>
                @else
                {{-- REQUEST --}}
                @if($requestFields->isNotEmpty())
                <div>
                    <div class="px-5 py-2 bg-blue-50 dark:bg-blue-900/20 border-b border-gray-100 dark:border-gray-700/60">
                        <span class="text-xs font-semibold text-blue-700 dark:text-blue-300 uppercase tracking-wider">↑ Request</span>
                    </div>
                    <div class="divide-y divide-gray-100 dark:divide-gray-700/60">
                        @foreach($requestFields as $field)
                        <div class="px-5 py-2.5 flex items-center gap-3 group">
                            <span class="font-mono text-sm font-medium text-gray-800 dark:text-gray-200 min-w-[160px]">{{ $field->field_name }}</span>
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-mono font-medium
                                         bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300">{{ $field->field_type }}</span>
                            @if($field->is_required)
                            <span class="text-[10px] text-red-500 font-semibold">req.</span>
                            @endif
                            @if($field->description)
                            <span class="flex-1 text-xs text-gray-500 dark:text-gray-400 truncate">{{ $field->description }}</span>
                            @endif
                            @if($field->example_value)
                            <span class="text-xs font-mono text-gray-400 dark:text-gray-500 truncate max-w-[100px]" title="{{ $field->example_value }}">{{ $field->example_value }}</span>
                            @endif
                            @can('services.create/edit/delete')
                            <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0">
                                <button onclick='editField({{ $field->id }}, {{ $field->toJson() }})'
                                        class="w-6 h-6 flex items-center justify-center rounded text-gray-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                </button>
                                <form action="{{ route('systems.services.fields.destroy', [$system, $service, $field]) }}" method="POST" class="inline"
                                      onsubmit="sgDeleteForm(this,'¿Eliminar campo {{ addslashes($field->field_name) }}?');return false">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="w-6 h-6 flex items-center justify-center rounded text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                            @endcan
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- RESPONSE --}}
                @if($responseFields->isNotEmpty())
                <div class="{{ $requestFields->isNotEmpty() ? 'border-t border-gray-100 dark:border-gray-700/60' : '' }}">
                    <div class="px-5 py-2 bg-emerald-50 dark:bg-emerald-900/20 border-b border-gray-100 dark:border-gray-700/60">
                        <span class="text-xs font-semibold text-emerald-700 dark:text-emerald-300 uppercase tracking-wider">↓ Response</span>
                    </div>
                    <div class="divide-y divide-gray-100 dark:divide-gray-700/60">
                        @foreach($responseFields as $field)
                        <div class="px-5 py-2.5 flex items-center gap-3 group">
                            <span class="font-mono text-sm font-medium text-gray-800 dark:text-gray-200 min-w-[160px]">{{ $field->field_name }}</span>
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-mono font-medium
                                         bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300">{{ $field->field_type }}</span>
                            @if($field->is_required)
                            <span class="text-[10px] text-red-500 font-semibold">req.</span>
                            @endif
                            @if($field->description)
                            <span class="flex-1 text-xs text-gray-500 dark:text-gray-400 truncate">{{ $field->description }}</span>
                            @endif
                            @if($field->example_value)
                            <span class="text-xs font-mono text-gray-400 dark:text-gray-500 truncate max-w-[100px]" title="{{ $field->example_value }}">{{ $field->example_value }}</span>
                            @endif
                            @can('services.create/edit/delete')
                            <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0">
                                <button onclick='editField({{ $field->id }}, {{ $field->toJson() }})'
                                        class="w-6 h-6 flex items-center justify-center rounded text-gray-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                </button>
                                <form action="{{ route('systems.services.fields.destroy', [$system, $service, $field]) }}" method="POST" class="inline"
                                      onsubmit="sgDeleteForm(this,'¿Eliminar campo {{ addslashes($field->field_name) }}?');return false">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="w-6 h-6 flex items-center justify-center rounded text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
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

            {{-- ── Documentos ── --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Documentos</h3>
                        @if($service->documents->count())
                        <span class="text-xs text-gray-400">({{ $service->documents->count() }})</span>
                        @endif
                    </div>
                    @can('services.create/edit/delete')
                    <button onclick="openModal('modal-doc')"
                            class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium
                                   text-orange-600 dark:text-orange-400 bg-orange-50 dark:bg-orange-900/30
                                   rounded-lg hover:bg-orange-100 dark:hover:bg-orange-900/50 transition-colors">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Adjuntar
                    </button>
                    @endcan
                </div>

                @if($service->documents->isEmpty())
                <div class="py-10 text-center text-sm text-gray-400 dark:text-gray-500">
                    Sin documentos adjuntos.
                </div>
                @else
                {{-- Enviados --}}
                @if($sentDocs->isNotEmpty())
                <div>
                    <div class="px-5 py-2 bg-blue-50 dark:bg-blue-900/20 border-b border-gray-100 dark:border-gray-700/60">
                        <span class="text-xs font-semibold text-blue-700 dark:text-blue-300 uppercase tracking-wider">↑ Enviados al proveedor</span>
                    </div>
                    <div class="divide-y divide-gray-100 dark:divide-gray-700/60">
                        @foreach($sentDocs as $doc)
                        @php $docExt = strtolower(pathinfo($doc->original_name, PATHINFO_EXTENSION)); @endphp
                        <div class="px-5 py-3 flex items-center gap-3">
                            <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <div class="flex-1 min-w-0">
                                <button type="button"
                                        onclick='openDocPreview({ name: {{ json_encode($doc->description ?: $doc->original_name) }}, description: {{ json_encode($doc->description ? $doc->original_name : null) }}, previewUrl: "{{ route('systems.services.documents.preview', [$system, $service, $doc]) }}", downloadUrl: "{{ route('systems.services.documents.download', [$system, $service, $doc]) }}", ext: {{ json_encode($docExt) }} })'
                                        class="text-sm font-medium text-gray-800 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 hover:underline truncate block text-left">
                                    {{ $doc->description ?: $doc->original_name }}
                                </button>
                                <p class="text-xs text-gray-400 mt-0.5">
                                    {{ \App\Models\SystemServiceDocument::typeLabel($doc->document_type) }}
                                    @if($doc->description) · <span class="font-mono">{{ $doc->original_name }}</span> @endif
                                </p>
                            </div>
                            @can('services.create/edit/delete')
                            <form action="{{ route('systems.services.documents.destroy', [$system, $service, $doc]) }}" method="POST" class="inline"
                                  onsubmit="sgDeleteForm(this,'¿Eliminar este documento?');return false">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-6 h-6 flex items-center justify-center rounded text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                            @endcan
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Recibidos --}}
                @if($receivedDocs->isNotEmpty())
                <div class="{{ $sentDocs->isNotEmpty() ? 'border-t border-gray-100 dark:border-gray-700/60' : '' }}">
                    <div class="px-5 py-2 bg-emerald-50 dark:bg-emerald-900/20 border-b border-gray-100 dark:border-gray-700/60">
                        <span class="text-xs font-semibold text-emerald-700 dark:text-emerald-300 uppercase tracking-wider">↓ Recibidos del proveedor</span>
                    </div>
                    <div class="divide-y divide-gray-100 dark:divide-gray-700/60">
                        @foreach($receivedDocs as $doc)
                        @php $docExt = strtolower(pathinfo($doc->original_name, PATHINFO_EXTENSION)); @endphp
                        <div class="px-5 py-3 flex items-center gap-3">
                            <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <div class="flex-1 min-w-0">
                                <button type="button"
                                        onclick='openDocPreview({ name: {{ json_encode($doc->description ?: $doc->original_name) }}, description: {{ json_encode($doc->description ? $doc->original_name : null) }}, previewUrl: "{{ route('systems.services.documents.preview', [$system, $service, $doc]) }}", downloadUrl: "{{ route('systems.services.documents.download', [$system, $service, $doc]) }}", ext: {{ json_encode($docExt) }} })'
                                        class="text-sm font-medium text-gray-800 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 hover:underline truncate block text-left">
                                    {{ $doc->description ?: $doc->original_name }}
                                </button>
                                <p class="text-xs text-gray-400 mt-0.5">
                                    {{ \App\Models\SystemServiceDocument::typeLabel($doc->document_type) }}
                                    @if($doc->description) · <span class="font-mono">{{ $doc->original_name }}</span> @endif
                                </p>
                            </div>
                            @can('services.create/edit/delete')
                            <form action="{{ route('systems.services.documents.destroy', [$system, $service, $doc]) }}" method="POST" class="inline"
                                  onsubmit="sgDeleteForm(this,'¿Eliminar este documento?');return false">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-6 h-6 flex items-center justify-center rounded text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                            @endcan
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
                @endif
            </div>

        </div>{{-- /col izquierda --}}

        {{-- ══════════════════════════════════════════════
             COLUMNA DERECHA (2/5): Credenciales · Gateway
        ══════════════════════════════════════════════ --}}
        <div class="lg:col-span-3 space-y-6">

            {{-- ── Credenciales ── --}}
            @if($service->api_key || $service->api_secret || $service->token)
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                        </svg>
                        <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Credenciales</h3>
                    </div>
                    @if($service->token_expires_at)
                    <span class="text-xs px-2 py-0.5 rounded-full font-medium
                        {{ $tokenExpired ? 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400' : ($tokenSoon ? 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400' : 'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400') }}">
                        {{ $tokenExpired ? 'Token vencido' : 'Vence ' . $service->token_expires_at->format('d/m/Y') }}
                    </span>
                    @endif
                </div>
                <div class="p-6 space-y-4 text-sm">
                    @foreach(['api_key' => 'API Key', 'api_secret' => 'API Secret', 'token' => 'Token / Bearer'] as $field => $label)
                    @if($service->$field)
                    <div>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mb-1">{{ $label }}</p>
                        <div class="flex items-center gap-2">
                            <span id="cred-mask-{{ $field }}" class="font-mono text-gray-400 dark:text-gray-500 text-xs">••••••••••••••••</span>
                            <span id="cred-plain-{{ $field }}" class="hidden font-mono text-xs text-gray-800 dark:text-gray-200 break-all">{{ $service->$field }}</span>
                            <button type="button" onclick="toggleCred('{{ $field }}')"
                                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors flex-shrink-0">
                                <svg id="cred-eye-{{ $field }}" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                            <button type="button" onclick="copyToClipboard('{{ $field }}')" title="Copiar"
                                    class="text-gray-400 hover:text-blue-500 transition-colors flex-shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    @endif
                    @endforeach
                </div>
            </div>
            @endif

            {{-- ═══════════════════════════════════════════════════════
                 GATEWAY (solo servicios "expuestos")
            ═══════════════════════════════════════════════════════ --}}
            @if($service->direction === 'exposed')

            {{-- Banner key recién generada --}}
            @if(session('new_raw_key'))
            <div id="new-key-banner"
                 class="bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-300 dark:border-emerald-600 rounded-xl p-4 space-y-3">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-emerald-800 dark:text-emerald-200">Solicitante registrado — copia los datos ahora</p>
                        <p class="text-xs text-emerald-600 dark:text-emerald-400 mt-0.5">La API key no se mostrará de nuevo. Guárdala en un lugar seguro.</p>
                        <p class="text-xs font-medium text-emerald-700 dark:text-emerald-300 mt-2 mb-1">API Key</p>
                        <div class="flex items-center gap-2">
                            <code id="raw-key-display" class="flex-1 block font-mono text-xs bg-white dark:bg-gray-800 border border-emerald-300 dark:border-emerald-600 rounded px-3 py-2 text-emerald-900 dark:text-emerald-100 break-all select-all">{{ session('new_raw_key') }}</code>
                            <button onclick="navigator.clipboard.writeText(document.getElementById('raw-key-display').textContent.trim())"
                                    class="flex-shrink-0 inline-flex items-center gap-1 px-3 py-2 text-xs font-medium bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                                Copiar
                            </button>
                        </div>
                        @if(session('new_gateway_url'))
                        <p class="text-xs font-medium text-emerald-700 dark:text-emerald-300 mt-3 mb-1">URL de gateway (exclusiva de este solicitante)</p>
                        <div class="flex items-center gap-2">
                            <code id="new-gw-url-display" class="flex-1 block font-mono text-xs bg-white dark:bg-gray-800 border border-emerald-300 dark:border-emerald-600 rounded px-3 py-2 text-indigo-700 dark:text-indigo-300 break-all select-all">{{ session('new_gateway_url') }}</code>
                            <button onclick="navigator.clipboard.writeText(document.getElementById('new-gw-url-display').textContent.trim())"
                                    class="flex-shrink-0 inline-flex items-center gap-1 px-3 py-2 text-xs font-medium bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                                Copiar
                            </button>
                        </div>
                        <p class="text-xs text-emerald-600 dark:text-emerald-400 mt-1">Envía las peticiones a esta URL con el header <code class="font-mono">X-API-Key: &lt;api_key&gt;</code></p>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            {{-- ── Solicitantes / Consumidores ── --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">

                {{-- Cabecera --}}
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between gap-3">
                    <div>
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Solicitantes / Consumidores
                                <span class="ml-1 px-1.5 py-0.5 text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-500 rounded">{{ $service->gatewayKeys->count() }}</span>
                            </h3>
                        </div>
                        {{-- Estado del gateway + botón de configuración --}}
                        <div class="flex items-center gap-2 mt-1.5">
                            @if($service->gateway_enabled)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-medium bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>Gateway activo
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-medium bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400">
                                <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>Gateway inactivo
                            </span>
                            @endif
                            @if($service->gateway_require_key)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-medium bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                Requiere API key
                            </span>
                            @endif
                            @can('services.create/edit/delete')
                            <button onclick="openModal('modal-gw-settings')" title="Configuración del gateway"
                                    class="inline-flex items-center gap-1 text-[11px] text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                Configuración
                            </button>
                            @endcan
                        </div>
                    </div>
                    @can('services.create/edit/delete')
                    <button onclick="openModal('modal-gateway-key')"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/30 hover:bg-amber-100 dark:hover:bg-amber-900/50 rounded-lg transition-colors flex-shrink-0">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Nuevo solicitante
                    </button>
                    @endcan
                </div>

                {{-- Lista de consumidores --}}
                @if($service->gatewayKeys->isEmpty())
                <div class="px-6 py-10 text-center">
                    <svg class="mx-auto w-10 h-10 text-gray-200 dark:text-gray-700 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <p class="text-sm text-gray-400 dark:text-gray-500">Ningún solicitante registrado aún.</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Al registrar el primero se activará el gateway automáticamente.</p>
                </div>
                @else
                <div class="divide-y divide-gray-100 dark:divide-gray-700/60">
                    @foreach($service->gatewayKeys as $gkey)
                    @php
                        $keyExpired   = $gkey->isExpired();
                        $consumerType = $gkey->consumer_type ?? 'external';
                        $isInternal   = $consumerType === 'internal';
                        $isPerson     = $consumerType === 'person';
                        $iconBg       = $isInternal ? 'bg-indigo-100 dark:bg-indigo-900/40'
                                      : ($isPerson ? 'bg-violet-100 dark:bg-violet-900/40'
                                      : 'bg-amber-50 dark:bg-amber-900/30');
                        $badgeClass   = $isInternal ? 'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400'
                                      : ($isPerson ? 'bg-violet-50 dark:bg-violet-900/30 text-violet-600 dark:text-violet-400'
                                      : 'bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400');
                        $badgeLabel   = $isInternal ? 'Sistema interno' : ($isPerson ? 'Persona' : 'Org. externa');
                        $ks           = $gatewayKeyStats[$gkey->id] ?? ['today' => 0, 'week' => 0, 'errors_today' => 0, 'avg_ms' => 0];
                    @endphp
                    <div x-data="{ open: {{ session('new_key_id') == $gkey->id ? 'true' : 'false' }}, editing: false }">

                        {{-- Fila compacta (siempre visible) --}}
                        <div class="px-5 py-3 flex items-center gap-3 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors select-none"
                             @click="open = !open">

                            {{-- Ícono tipo --}}
                            <div class="flex-shrink-0 w-9 h-9 rounded-lg flex items-center justify-center {{ $iconBg }}">
                                @if($isInternal && $gkey->requestingSystem)
                                <span class="text-xs font-bold text-indigo-700 dark:text-indigo-300">
                                    {{ strtoupper(substr($gkey->requestingSystem->acronym ?? $gkey->requestingSystem->name, 0, 2)) }}
                                </span>
                                @elseif($isInternal)
                                <svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2"/></svg>
                                @elseif($isPerson)
                                <svg class="w-4 h-4 text-violet-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                @else
                                <svg class="w-4 h-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                @endif
                            </div>

                            {{-- Info principal --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $gkey->name }}</span>
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium {{ $badgeClass }}">{{ $badgeLabel }}</span>
                                    @if($keyExpired)
                                    <span class="inline-flex items-center gap-1 text-xs text-red-500"><span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>Expirada</span>
                                    @elseif(!$gkey->is_active)
                                    <span class="inline-flex items-center gap-1 text-xs text-gray-400"><span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>Desactivada</span>
                                    @else
                                    <span class="inline-flex items-center gap-1 text-xs text-emerald-600 dark:text-emerald-400"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Activa</span>
                                    @endif
                                </div>
                                <div class="mt-0.5 flex items-center gap-3 text-xs text-gray-400 dark:text-gray-500">
                                    @if($isInternal && $gkey->requestingSystem)
                                    <span class="text-indigo-500 dark:text-indigo-400">{{ $gkey->requestingSystem->acronym ?? $gkey->requestingSystem->name }}</span>
                                    @elseif($isPerson && $gkey->persona)
                                    <span class="text-violet-500 dark:text-violet-400">{{ trim(($gkey->persona->apellido_paterno ?? '') . ' ' . ($gkey->persona->nombres ?? $gkey->persona->name ?? '')) }}</span>
                                    @elseif($gkey->consumer_organization)
                                    <span class="text-amber-600 dark:text-amber-400">{{ $gkey->consumer_organization }}</span>
                                    @endif
                                    <span>{{ number_format($gkey->total_requests) }} consultas totales</span>
                                    @if($gkey->last_used_at)<span>Último uso {{ $gkey->last_used_at->diffForHumans() }}</span>@endif
                                </div>
                            </div>

                            {{-- Mini stats (hoy) --}}
                            @if($ks['today'] > 0 || $ks['errors_today'] > 0)
                            <div class="hidden sm:flex items-center gap-3 text-xs flex-shrink-0">
                                <div class="text-center">
                                    <p class="font-semibold text-blue-600 dark:text-blue-400">{{ $ks['today'] }}</p>
                                    <p class="text-gray-400 text-[10px]">hoy</p>
                                </div>
                                @if($ks['errors_today'] > 0)
                                <div class="text-center">
                                    <p class="font-semibold text-red-500">{{ $ks['errors_today'] }}</p>
                                    <p class="text-gray-400 text-[10px]">errores</p>
                                </div>
                                @endif
                            </div>
                            @endif

                            {{-- Chevron --}}
                            <svg class="w-4 h-4 text-gray-300 dark:text-gray-600 flex-shrink-0 transition-transform duration-200"
                                 :class="open ? 'rotate-90' : ''"
                                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>

                        {{-- Panel de detalle (expandible) --}}
                        <div x-show="open"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 -translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100"
                             x-transition:leave-end="opacity-0"
                             class="border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/60 px-5 py-4 space-y-4">

                            {{-- URL del gateway --}}
                            @if($gkey->gateway_slug)
                            <div>
                                <p class="text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">URL del gateway (exclusiva)</p>
                                <div class="flex items-center gap-2">
                                    <code id="gw-url-{{ $gkey->id }}"
                                          class="flex-1 font-mono text-xs bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2 text-indigo-700 dark:text-indigo-300 break-all">{{ $gkey->gatewayUrl() }}</code>
                                    <button onclick="navigator.clipboard.writeText(document.getElementById('gw-url-{{ $gkey->id }}').textContent.trim())"
                                            title="Copiar URL"
                                            class="flex-shrink-0 inline-flex items-center justify-center w-8 h-8 rounded-lg bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-gray-400 hover:text-indigo-600 hover:border-indigo-400 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                    </button>
                                </div>
                                <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-1">Agrega el path requerido al final · Header: <code class="font-mono">X-API-Key: &lt;tu_clave&gt;</code></p>
                            </div>
                            @endif

                            {{-- Indicadores de uso --}}
                            <div>
                                <p class="text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Actividad</p>
                                <div class="grid grid-cols-4 gap-2">
                                    @foreach([
                                        ['label' => 'Hoy',           'value' => $ks['today'],                                       'color' => 'blue'],
                                        ['label' => 'Esta semana',   'value' => $ks['week'],                                        'color' => 'indigo'],
                                        ['label' => 'Errores hoy',  'value' => $ks['errors_today'],                                 'color' => $ks['errors_today'] > 0 ? 'red' : 'gray'],
                                        ['label' => 'Tiempo prom.', 'value' => $ks['avg_ms'] > 0 ? $ks['avg_ms'] . ' ms' : '—',    'color' => 'gray'],
                                    ] as $st)
                                    <div class="bg-white dark:bg-gray-700/60 rounded-lg p-3 text-center border border-gray-100 dark:border-gray-700">
                                        <p class="text-base font-bold text-{{ $st['color'] }}-600 dark:text-{{ $st['color'] }}-400">{{ $st['value'] }}</p>
                                        <p class="text-[10px] text-gray-400 mt-0.5">{{ $st['label'] }}</p>
                                    </div>
                                    @endforeach
                                </div>
                                <div class="mt-2 flex items-center gap-4 text-xs text-gray-400 dark:text-gray-500">
                                    <span>Total: <strong class="text-gray-600 dark:text-gray-300">{{ number_format($gkey->total_requests) }}</strong> consultas</span>
                                    @if($gkey->last_used_at)<span>Último uso: {{ $gkey->last_used_at->format('d/m/Y H:i') }}</span>@endif
                                </div>
                            </div>

                            {{-- Documentos del solicitante --}}
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <p class="text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Documentos
                                        @if($gkey->documents->isNotEmpty())
                                        <span class="ml-1 px-1.5 py-0.5 text-[10px] font-medium bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 rounded">{{ $gkey->documents->count() }}</span>
                                        @endif
                                    </p>
                                    @can('services.create/edit/delete')
                                    <button type="button"
                                            onclick="openGwKeyDocModal({{ $gkey->id }}, '{{ addslashes($gkey->name) }}')"
                                            class="inline-flex items-center gap-1 text-[11px] text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-200 transition-colors">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        Adjuntar
                                    </button>
                                    @endcan
                                </div>
                                @if($gkey->documents->isEmpty())
                                <p class="text-xs text-gray-400 dark:text-gray-500 italic">Sin documentos adjuntos. Adjunta la solicitud, resolución u oficio que autoriza este acceso.</p>
                                @else
                                <div class="space-y-1.5">
                                    @foreach($gkey->documents as $doc)
                                    @php
                                        $docExt  = strtolower(pathinfo($doc->original_name, PATHINFO_EXTENSION));
                                        $docMeta = collect([
                                            ['solicitud' => 'Solicitud', 'resolucion_directoral' => 'R.D.', 'resolucion_jefatural' => 'R.J.',
                                             'memorando' => 'Memorando', 'oficio' => 'Oficio', 'contrato' => 'Contrato',
                                             'acta' => 'Acta', 'convenio' => 'Convenio', 'otro' => 'Otro'][$doc->document_type] ?? null,
                                            $doc->document_number,
                                            $doc->document_date?->format('d/m/Y'),
                                        ])->filter()->implode(' · ');
                                        $chipLabel = $doc->description ?: $doc->original_name;
                                    @endphp
                                    <div class="flex items-center gap-2 py-1 px-2 rounded-lg bg-white dark:bg-gray-700/60 border border-gray-200 dark:border-gray-600 group/doc">
                                        <svg class="w-4 h-4 text-indigo-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                        </svg>
                                        <div class="flex-1 min-w-0">
                                            <button type="button"
                                                    onclick='openDocPreview({ name: {{ json_encode($chipLabel) }}, description: {{ json_encode($docMeta ?: null) }}, previewUrl: "{{ route('systems.services.gateway.keys.documents.preview', [$system, $service, $gkey, $doc]) }}", downloadUrl: "{{ route('systems.services.gateway.keys.documents.download', [$system, $service, $gkey, $doc]) }}", ext: {{ json_encode($docExt) }} })'
                                                    class="text-xs text-indigo-700 dark:text-indigo-300 hover:underline truncate block text-left max-w-xs">
                                                {{ $chipLabel }}
                                            </button>
                                            @if($docMeta)
                                            <p class="text-[10px] text-gray-400 dark:text-gray-500">{{ $docMeta }}</p>
                                            @endif
                                        </div>
                                        <div class="flex items-center gap-1 opacity-0 group-hover/doc:opacity-100 transition-opacity flex-shrink-0">
                                            <a href="{{ route('systems.services.gateway.keys.documents.download', [$system, $service, $gkey, $doc]) }}"
                                               title="Descargar"
                                               class="w-6 h-6 flex items-center justify-center rounded text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition-colors">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                            </a>
                                            @can('services.create/edit/delete')
                                            <form method="POST" action="{{ route('systems.services.gateway.keys.documents.destroy', [$system, $service, $gkey, $doc]) }}" class="inline">
                                                @csrf @method('DELETE')
                                                <button type="button" x-data
                                                        @click.prevent="sgDeleteForm($el.closest('form'), '¿Eliminar {{ addslashes($doc->original_name) }}?')"
                                                        class="w-6 h-6 flex items-center justify-center rounded text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </form>
                                            @endcan
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                @endif
                            </div>

                            {{-- Configuración de la clave --}}
                            <div class="grid grid-cols-2 gap-3 text-xs">
                                <div class="space-y-1.5">
                                    <p class="text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Configuración</p>
                                    <div class="flex flex-wrap gap-x-4 gap-y-1 text-gray-500 dark:text-gray-400">
                                        <span>Prefijo: <code class="font-mono text-gray-700 dark:text-gray-300">{{ $gkey->key_prefix }}••••••••</code></span>
                                        @if($gkey->rate_per_minute)<span>Límite: <strong>{{ $gkey->rate_per_minute }}/min</strong></span>@endif
                                        @if($gkey->rate_per_day)<span>Límite: <strong>{{ $gkey->rate_per_day }}/día</strong></span>@endif
                                        @if($gkey->expires_at)
                                        <span class="{{ $keyExpired ? 'text-red-500' : '' }}">
                                            Vence: <strong>{{ $gkey->expires_at->format('d/m/Y') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    @if(!empty($gkey->allowed_ips))
                                    <div>
                                        <p class="text-[11px] text-gray-400 mb-0.5">IPs permitidas:</p>
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($gkey->allowed_ips as $ip)
                                            <code class="px-1.5 py-0.5 bg-gray-100 dark:bg-gray-700 rounded text-[11px] font-mono text-gray-600 dark:text-gray-300">{{ $ip }}</code>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endif
                                </div>
                                @if($gkey->purpose || $gkey->notes || $gkey->persona)
                                <div class="space-y-1">
                                    <p class="text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Notas</p>
                                    @if($gkey->purpose)<p class="text-gray-500 dark:text-gray-400 italic">{{ $gkey->purpose }}</p>@endif
                                    @if($gkey->notes)<p class="text-gray-400 dark:text-gray-500">{{ $gkey->notes }}</p>@endif
                                    @if($gkey->persona && !$isPerson)
                                    <p class="text-gray-500 dark:text-gray-400">Contacto: {{ $gkey->persona->full_name ?? $gkey->persona->name }}</p>
                                    @endif
                                </div>
                                @endif
                            </div>

                            {{-- Acciones --}}
                            @can('services.create/edit/delete')
                            <div class="flex items-center flex-wrap gap-2 pt-1 border-t border-gray-200 dark:border-gray-700">
                                <button type="button" @click="editing = !editing"
                                        :class="editing ? 'bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 hover:text-indigo-600 dark:hover:text-indigo-400'"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    Editar
                                </button>
                                <form method="POST" action="{{ route('systems.services.gateway.keys.toggle', [$system, $service, $gkey]) }}">
                                    @csrf
                                    <button type="submit"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg transition-colors
                                                   {{ $gkey->is_active
                                                       ? 'bg-yellow-50 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400 hover:bg-yellow-100 dark:hover:bg-yellow-900/50'
                                                       : 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 hover:bg-emerald-100 dark:hover:bg-emerald-900/50' }}">
                                        @if($gkey->is_active)
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                        Desactivar
                                        @else
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        Activar
                                        @endif
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('systems.services.gateway.keys.regenerate', [$system, $service, $gkey]) }}" class="inline"
                                      onsubmit="return confirm('¿Regenerar la clave de {{ addslashes($gkey->name) }}?\n\nLa clave actual dejará de funcionar de inmediato.')">
                                    @csrf
                                    <button type="submit"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg bg-orange-50 dark:bg-orange-900/30 text-orange-700 dark:text-orange-400 hover:bg-orange-100 dark:hover:bg-orange-900/50 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                        Regenerar token
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('systems.services.gateway.keys.destroy', [$system, $service, $gkey]) }}" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="button" x-data
                                            @click.prevent="sgDeleteForm($el.closest('form'), '¿Eliminar el acceso de {{ addslashes($gkey->name) }}?')"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/50 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        Eliminar
                                    </button>
                                </form>
                            </div>

                            {{-- Formulario de edición inline --}}
                            <div x-show="editing"
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0 -translate-y-1"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 class="mt-3 pt-3 border-t border-indigo-100 dark:border-indigo-900/40">
                                <p class="text-[11px] font-semibold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider mb-3">Editar configuración del solicitante</p>
                                <form method="POST" action="{{ route('systems.services.gateway.keys.update', [$system, $service, $gkey]) }}"
                                      class="space-y-3">
                                    @csrf @method('PUT')
                                    {{-- Campos ocultos para mantener valores no editados --}}
                                    <input type="hidden" name="consumer_type" value="{{ $gkey->consumer_type ?? 'external' }}">
                                    <input type="hidden" name="requesting_system_id" value="{{ $gkey->requesting_system_id }}">
                                    <input type="hidden" name="consumer_organization" value="{{ $gkey->consumer_organization }}">
                                    <input type="hidden" name="purpose" value="{{ $gkey->purpose }}">

                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                Nombre del acceso
                                            </label>
                                            <input type="text" name="name" value="{{ $gkey->name }}" required maxlength="120"
                                                   class="w-full text-sm px-3 py-1.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                Vencimiento <span class="font-normal text-gray-400">(vacío = sin vencimiento)</span>
                                            </label>
                                            <input type="date" name="expires_at"
                                                   value="{{ $gkey->expires_at ? $gkey->expires_at->format('Y-m-d') : '' }}"
                                                   class="w-full text-sm px-3 py-1.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                Límite por minuto <span class="font-normal text-gray-400">(vacío = usa el global)</span>
                                            </label>
                                            <input type="number" name="rate_per_minute" min="1" max="32767"
                                                   value="{{ $gkey->rate_per_minute }}"
                                                   placeholder="ej. 60"
                                                   class="w-full text-sm px-3 py-1.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                Límite por día <span class="font-normal text-gray-400">(vacío = usa el global)</span>
                                            </label>
                                            <input type="number" name="rate_per_day" min="1" max="32767"
                                                   value="{{ $gkey->rate_per_day }}"
                                                   placeholder="ej. 5000"
                                                   class="w-full text-sm px-3 py-1.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            IPs permitidas <span class="font-normal text-gray-400">(separadas por coma o espacio · vacío = cualquier IP)</span>
                                        </label>
                                        <input type="text" name="allowed_ips"
                                               value="{{ is_array($gkey->allowed_ips) ? implode(', ', $gkey->allowed_ips) : $gkey->allowed_ips }}"
                                               placeholder="192.168.1.10, 10.0.0.5"
                                               class="w-full text-sm font-mono px-3 py-1.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Notas internas</label>
                                        <textarea name="notes" rows="2" maxlength="500"
                                                  class="w-full text-sm px-3 py-1.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none resize-none">{{ $gkey->notes }}</textarea>
                                    </div>
                                    <div class="flex items-center justify-end gap-2">
                                        <button type="button" @click="editing = false"
                                                class="px-3 py-1.5 text-xs text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
                                            Cancelar
                                        </button>
                                        <button type="submit"
                                                class="inline-flex items-center gap-1.5 px-4 py-1.5 text-xs font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            Guardar cambios
                                        </button>
                                    </div>
                                </form>
                            </div>
                            @endcan
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- ── Log reciente ── --}}
            @if($service->gateway_enabled && $gatewayRecentLogs->isNotEmpty())
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Log reciente</h3>
                    </div>
                    <a href="{{ route('systems.services.gateway.logs', [$system, $service]) }}"
                       class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">Ver todo →</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs">
                        <thead>
                            <tr class="border-b border-gray-100 dark:border-gray-700 text-gray-400 dark:text-gray-500">
                                <th class="px-4 py-2 text-left font-medium">Hora</th>
                                <th class="px-4 py-2 text-left font-medium">Método</th>
                                <th class="px-4 py-2 text-left font-medium">IP</th>
                                <th class="px-4 py-2 text-left font-medium">Clave</th>
                                <th class="px-4 py-2 text-left font-medium">Status</th>
                                <th class="px-4 py-2 text-right font-medium">ms</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                            @foreach($gatewayRecentLogs as $log)
                            @php $scolor = $log->statusColor(); @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                <td class="px-4 py-2 font-mono text-gray-400 dark:text-gray-500 whitespace-nowrap">{{ $log->created_at->format('H:i:s') }}</td>
                                <td class="px-4 py-2 font-mono font-bold text-gray-700 dark:text-gray-300">{{ $log->method }}</td>
                                <td class="px-4 py-2 font-mono text-gray-500 dark:text-gray-400">{{ $log->ip_address }}</td>
                                <td class="px-4 py-2 text-gray-500 dark:text-gray-400 max-w-[100px] truncate">
                                    {{ $log->gatewayKey?->name ?? '—' }}
                                </td>
                                <td class="px-4 py-2">
                                    @if($log->response_status)
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-mono font-bold
                                                 bg-{{ $scolor }}-100 dark:bg-{{ $scolor }}-900/40 text-{{ $scolor }}-700 dark:text-{{ $scolor }}-300">
                                        {{ $log->response_status }}
                                    </span>
                                    @else
                                    <span class="text-red-500">ERR</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2 text-right font-mono text-gray-400 dark:text-gray-500">{{ $log->response_time_ms }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            @endif {{-- /exposed --}}

        </div>{{-- /col derecha --}}


    </div>{{-- /grid --}}
</div>{{-- /max-w-6xl --}}

{{-- ════════ MODAL: Adjuntar documento a solicitante ════════ --}}
@if($service->direction === 'exposed')
<div id="modal-gw-key-doc"
     class="hidden fixed inset-0 z-50 items-center justify-center p-4"
     role="dialog" aria-modal="true">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeModal('modal-gw-key-doc')"></div>
    <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                </svg>
                <div>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">Adjuntar Documento</h3>
                    <p id="gw-key-doc-subtitle" class="text-xs text-gray-400 dark:text-gray-500 mt-0.5"></p>
                </div>
            </div>
            <button onclick="closeModal('modal-gw-key-doc')"
                    class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form id="gw-key-doc-form" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="p-6 space-y-4">

                {{-- Drop zone --}}
                <div x-data="{ dragging: false }"
                     @dragover.prevent="dragging = true"
                     @dragleave.prevent="dragging = false"
                     @drop.prevent="dragging = false; $refs.gwDocFile.files = $event.dataTransfer.files; gwUpdateFileName($event.dataTransfer.files[0]?.name)"
                     :class="dragging ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20' : 'border-gray-300 dark:border-gray-600 hover:border-indigo-400'"
                     class="border-2 border-dashed rounded-xl p-6 text-center transition-colors cursor-pointer"
                     onclick="document.getElementById('gw-doc-file-input').click()">
                    <svg class="w-10 h-10 mx-auto text-gray-300 dark:text-gray-600 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    <p id="gw-doc-file-label" class="text-sm text-gray-500 dark:text-gray-400">
                        Arrastra el archivo aquí o <span class="text-indigo-600 dark:text-indigo-400 font-medium">haz clic para seleccionar</span>
                    </p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">PDF, Word, Excel, imagen · Máx. 10 MB</p>
                    <input id="gw-doc-file-input" x-ref="gwDocFile" type="file" name="file"
                           accept=".pdf,.doc,.docx,.xls,.xlsx,.png,.jpg,.jpeg"
                           class="hidden" required
                           onchange="gwUpdateFileName(this.files[0]?.name)">
                </div>

                {{-- Descripción --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Descripción <span class="text-gray-400 font-normal">(opcional)</span>
                    </label>
                    <input type="text" name="description"
                           placeholder="Ej: Oficio de autorización de acceso"
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>

                {{-- Datos del documento --}}
                <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                    <p class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider mb-3">
                        Datos del documento <span class="font-normal text-gray-400 normal-case">(opcional)</span>
                    </p>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Tipo</label>
                            <select name="document_type"
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="">Sin tipo</option>
                                <option value="solicitud">Solicitud</option>
                                <option value="resolucion_directoral">Resolución Directoral</option>
                                <option value="resolucion_jefatural">Resolución Jefatural</option>
                                <option value="memorando">Memorando</option>
                                <option value="oficio">Oficio</option>
                                <option value="contrato">Contrato</option>
                                <option value="acta">Acta</option>
                                <option value="convenio">Convenio</option>
                                <option value="otro">Otro</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">N° de documento</label>
                            <input type="text" name="document_number"
                                   placeholder="Of. N°042-2024-OTI"
                                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Fecha del documento</label>
                            <input type="date" name="document_date"
                                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Observaciones</label>
                            <input type="text" name="document_notes"
                                   placeholder="Notas adicionales..."
                                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 rounded-b-2xl">
                <button type="button" onclick="closeModal('modal-gw-key-doc')"
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancelar
                </button>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    Subir documento
                </button>
            </div>
        </form>
    </div>
</div>

{{-- URLs de documentos por clave (para JS) --}}
<script>
const gwKeyDocUrls = {
    @foreach($service->gatewayKeys as $gkey)
    {{ $gkey->id }}: '{{ route('systems.services.gateway.keys.documents.store', [$system, $service, $gkey]) }}',
    @endforeach
};

function openGwKeyDocModal(keyId, keyName) {
    document.getElementById('gw-key-doc-subtitle').textContent = 'Solicitante: ' + keyName;
    document.getElementById('gw-key-doc-form').action = gwKeyDocUrls[keyId];
    document.getElementById('gw-doc-file-label').innerHTML =
        'Arrastra el archivo aquí o <span class="text-indigo-600 font-medium">haz clic para seleccionar</span>';
    document.getElementById('gw-doc-file-input').value = '';
    openModal('modal-gw-key-doc');
}

function gwUpdateFileName(name) {
    if (name) {
        document.getElementById('gw-doc-file-label').textContent = name;
    }
}
</script>
@endif

{{-- ════════ MODAL: Configuración del Gateway ════════ --}}
@if($service->direction === 'exposed')
<div id="modal-gw-settings" class="hidden fixed inset-0 z-50 items-center justify-center p-4" role="dialog" aria-modal="true">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeModal('modal-gw-settings')"></div>
    <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-sm">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Configuración del Gateway</h3>
            </div>
            <button onclick="closeModal('modal-gw-settings')" class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('systems.services.gateway.settings', [$system, $service]) }}">
            @csrf @method('PUT')
            <div class="p-6 space-y-4">
                <p class="text-xs text-gray-400 dark:text-gray-500">Estos límites se aplican globalmente. Cada solicitante puede tener sus propios límites adicionales.</p>
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Límite por minuto <span class="font-normal text-gray-400">(vacío = sin límite global)</span>
                    </label>
                    <input type="number" name="gateway_rate_per_minute" min="1" max="32767"
                           value="{{ $service->gateway_rate_per_minute }}"
                           class="w-full text-sm px-3 py-1.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Límite por día <span class="font-normal text-gray-400">(vacío = sin límite global)</span>
                    </label>
                    <input type="number" name="gateway_rate_per_day" min="1" max="32767"
                           value="{{ $service->gateway_rate_per_day }}"
                           class="w-full text-sm px-3 py-1.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none">
                </div>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="hidden" name="gateway_require_key" value="0">
                    <input type="checkbox" name="gateway_require_key" value="1"
                           {{ $service->gateway_require_key ? 'checked' : '' }}
                           class="w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500">
                    <span class="text-sm text-gray-700 dark:text-gray-300">Requerir API key en todas las peticiones</span>
                </label>
                <div class="border-t border-gray-200 dark:border-gray-700 pt-3">
                    <p class="text-xs font-medium text-gray-600 dark:text-gray-400 mb-2">Estado del gateway</p>
                    <form method="POST" action="{{ route('systems.services.gateway.toggle', [$system, $service]) }}">
                        @csrf
                        <button type="submit"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg transition-colors
                                       {{ $service->gateway_enabled
                                           ? 'bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 hover:bg-red-100'
                                           : 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-100' }}">
                            @if($service->gateway_enabled)
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                            Desactivar gateway global
                            @else
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3l14 9-14 9V3z"/></svg>
                            Activar gateway
                            @endif
                        </button>
                    </form>
                </div>
            </div>
            <div class="px-6 pb-5 flex justify-end gap-2">
                <button type="button" onclick="closeModal('modal-gw-settings')"
                        class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 transition-colors">
                    Cancelar
                </button>
                <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors">
                    Guardar
                </button>
            </div>
        </form>
    </div>
</div>
@endif

{{-- ════════ MODAL: Agregar / Editar Campo ════════ --}}
<div id="modal-field" class="hidden fixed inset-0 z-50 items-center justify-center p-4" role="dialog" aria-modal="true">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeModal('modal-field')"></div>
    <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-lg">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 id="field-modal-title" class="text-base font-semibold text-gray-900 dark:text-white">Agregar campo</h3>
            <button onclick="closeModal('modal-field')" class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="field-form" action="{{ route('systems.services.fields.store', [$system, $service]) }}" method="POST">
            @csrf
            <span id="field-method"></span>
            <div class="p-6 grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Dirección <span class="text-red-500">*</span></label>
                    <select name="direction" id="field-direction" required
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="request">↑ Request (se envía)</option>
                        <option value="response">↓ Response (se recibe)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Tipo <span class="text-red-500">*</span></label>
                    <select name="field_type" id="field-type" required
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        @foreach(['string','integer','boolean','number','array','object','date','datetime','uuid','other'] as $t)
                        <option value="{{ $t }}">{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Nombre del campo <span class="text-red-500">*</span></label>
                    <input type="text" name="field_name" id="field-name" required
                           placeholder="dni, access_token, lista_matriculas…"
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white font-mono focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Ejemplo</label>
                    <input type="text" name="example_value" id="field-example"
                           placeholder="12345678, true, …"
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white font-mono focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
                <div class="flex items-center gap-3 self-end pb-1">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="hidden" name="is_required" value="0">
                        <input type="checkbox" name="is_required" value="1" id="field-required"
                               class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500">
                        <span class="text-sm text-gray-700 dark:text-gray-300">Obligatorio</span>
                    </label>
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Descripción</label>
                    <input type="text" name="description" id="field-description"
                           placeholder="Breve descripción del campo…"
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 rounded-b-2xl">
                <button type="button" onclick="closeModal('modal-field')"
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancelar
                </button>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span id="field-submit-label">Agregar</span>
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ════════ MODAL: Adjuntar Documento ════════ --}}
<div id="modal-doc" class="hidden fixed inset-0 z-50 items-center justify-center p-4" role="dialog" aria-modal="true">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeModal('modal-doc')"></div>
    <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Adjuntar Documento</h3>
            <button onclick="closeModal('modal-doc')" class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form action="{{ route('systems.services.documents.store', [$system, $service]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Tipo <span class="text-red-500">*</span></label>
                        <select name="document_type" required
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                            <option value="solicitud">Solicitud</option>
                            <option value="acta_entrega">Acta de Entrega</option>
                            <option value="oficio">Oficio</option>
                            <option value="contrato">Contrato</option>
                            <option value="memo">Memorando</option>
                            <option value="resolucion">Resolución</option>
                            <option value="otro">Otro</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Dirección <span class="text-red-500">*</span></label>
                        <select name="direction" required
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                            <option value="sent">↑ Enviado al proveedor</option>
                            <option value="received">↓ Recibido del proveedor</option>
                        </select>
                    </div>
                </div>
                <div x-data="{ dragging: false }"
                     @dragover.prevent="dragging = true" @dragleave.prevent="dragging = false"
                     @drop.prevent="dragging = false; $refs.docFile.files = $event.dataTransfer.files; updateDocName($event.dataTransfer.files[0]?.name)"
                     :class="dragging ? 'border-orange-500 bg-orange-50 dark:bg-orange-900/20' : 'border-gray-300 dark:border-gray-600 hover:border-orange-400'"
                     class="border-2 border-dashed rounded-xl p-6 text-center transition-colors cursor-pointer"
                     onclick="document.getElementById('doc-file-input').click()">
                    <svg class="w-8 h-8 mx-auto text-gray-300 dark:text-gray-600 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    <p id="doc-file-label" class="text-sm text-gray-500 dark:text-gray-400">
                        Arrastra o <span class="text-orange-600 dark:text-orange-400 font-medium">haz clic para seleccionar</span>
                    </p>
                    <p class="text-xs text-gray-400 mt-1">PDF, Word, imagen · Máx. 10 MB</p>
                    <input id="doc-file-input" x-ref="docFile" type="file" name="file"
                           accept=".pdf,.doc,.docx,.xls,.xlsx,.png,.jpg,.jpeg" class="hidden" required
                           onchange="updateDocName(this.files[0]?.name)">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Descripción <span class="text-gray-400 font-normal">(opcional)</span></label>
                    <input type="text" name="description" placeholder="Oficio N°023-2024-OTI…"
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 rounded-b-2xl">
                <button type="button" onclick="closeModal('modal-doc')"
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 transition-colors">Cancelar</button>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2 text-sm font-medium text-white bg-orange-600 rounded-lg hover:bg-orange-700 transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                    Subir
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ════════ MODAL: Nuevo solicitante / consumidor ════════ --}}
@can('services.create/edit/delete')
<div id="modal-gateway-key" class="hidden fixed inset-0 z-50 bg-black/50 backdrop-blur-sm items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto"
         x-data="{ consumerType: 'internal' }">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between sticky top-0 bg-white dark:bg-gray-800 z-10">
            <div>
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Nuevo solicitante de acceso</h3>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Se generará una clave única para este consumidor.</p>
            </div>
            <button onclick="closeModal('modal-gateway-key')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 ml-4 flex-shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('systems.services.gateway.keys.store', [$system, $service]) }}"
              class="p-6 space-y-5">
            @csrf

            {{-- ── Tipo de consumidor ── --}}
            <div>
                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-2 uppercase tracking-wide">¿Quién consume esta API?</label>
                <div class="grid grid-cols-3 gap-2">
                    <label class="relative cursor-pointer">
                        <input type="radio" name="consumer_type" value="internal" x-model="consumerType" class="sr-only peer" checked>
                        <div class="flex flex-col items-center gap-1.5 p-2.5 rounded-lg border-2 transition-all text-center
                                    border-gray-200 dark:border-gray-600 peer-checked:border-indigo-500 dark:peer-checked:border-indigo-400
                                    peer-checked:bg-indigo-50 dark:peer-checked:bg-indigo-900/20">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2"/>
                            </svg>
                            <p class="text-xs font-medium text-gray-800 dark:text-gray-200 leading-tight">Sistema interno</p>
                        </div>
                    </label>
                    <label class="relative cursor-pointer">
                        <input type="radio" name="consumer_type" value="person" x-model="consumerType" class="sr-only peer">
                        <div class="flex flex-col items-center gap-1.5 p-2.5 rounded-lg border-2 transition-all text-center
                                    border-gray-200 dark:border-gray-600 peer-checked:border-violet-500 dark:peer-checked:border-violet-400
                                    peer-checked:bg-violet-50 dark:peer-checked:bg-violet-900/20">
                            <svg class="w-5 h-5 text-violet-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <p class="text-xs font-medium text-gray-800 dark:text-gray-200 leading-tight">Persona</p>
                        </div>
                    </label>
                    <label class="relative cursor-pointer">
                        <input type="radio" name="consumer_type" value="external" x-model="consumerType" class="sr-only peer">
                        <div class="flex flex-col items-center gap-1.5 p-2.5 rounded-lg border-2 transition-all text-center
                                    border-gray-200 dark:border-gray-600 peer-checked:border-amber-500 dark:peer-checked:border-amber-400
                                    peer-checked:bg-amber-50 dark:peer-checked:bg-amber-900/20">
                            <svg class="w-5 h-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            <p class="text-xs font-medium text-gray-800 dark:text-gray-200 leading-tight">Org. externa</p>
                        </div>
                    </label>
                </div>
            </div>

            {{-- ── Sistema interno ── --}}
            <div x-show="consumerType === 'internal'" x-transition>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Sistema solicitante</label>
                <select name="requesting_system_id"
                        class="w-full text-sm px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none">
                    <option value="">— Selecciona el sistema —</option>
                    @foreach($allSystems as $s)
                    <option value="{{ $s->id }}">{{ $s->name }}{{ $s->acronym ? ' ('.$s->acronym.')' : '' }}</option>
                    @endforeach
                </select>
            </div>

            {{-- ── Persona directa ── --}}
            <div x-show="consumerType === 'person'" x-transition>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Persona solicitante <span class="text-red-500">*</span></label>
                <select name="consumer_persona_id"
                        class="w-full text-sm px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none">
                    <option value="">— Selecciona la persona —</option>
                    @foreach(\App\Models\Persona::orderBy('apellido_paterno')->get(['id','nombres','apellido_paterno','apellido_materno']) as $p)
                    <option value="{{ $p->id }}">{{ $p->apellido_paterno }} {{ $p->apellido_materno }}, {{ $p->nombres }}</option>
                    @endforeach
                </select>
                <p class="mt-1 text-[11px] text-gray-400 dark:text-gray-500">La persona recibirá su propia clave de acceso. También se usará como contacto.</p>
            </div>

            {{-- ── Organización externa ── --}}
            <div x-show="consumerType === 'external'" x-transition>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Organización / aplicación</label>
                <input type="text" name="consumer_organization" maxlength="150"
                       placeholder="Ej: SUNEDU, App Móvil Alumnos, Portal Web ORI…"
                       class="w-full text-sm px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none">
            </div>

            {{-- ── Nombre del acceso + propósito ── --}}
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">
                    Nombre de este acceso <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" required maxlength="120"
                       :placeholder="consumerType === 'internal' ? 'Ej: Sistema de Matrícula — consulta DNI' : consumerType === 'person' ? 'Ej: Javier Torres — acceso investigación' : 'Ej: SUNEDU — verificación docentes'"
                       class="w-full text-sm px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none">
                <p class="mt-1 text-[11px] text-gray-400 dark:text-gray-500">Identifica el uso — puedes tener varios accesos para el mismo consumidor.</p>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Propósito / uso</label>
                <input type="text" name="purpose" maxlength="255"
                       placeholder="Ej: Verificar DNI en el proceso de matrícula"
                       class="w-full text-sm px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none">
            </div>

            {{-- ── Contacto (solo si no es persona directa) ── --}}
            <div x-show="consumerType !== 'person'" x-transition>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Persona de contacto</label>
                <select name="persona_id"
                        class="w-full text-sm px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none">
                    <option value="">— Sin contacto —</option>
                    @foreach(\App\Models\Persona::orderBy('apellido_paterno')->get(['id','nombres','apellido_paterno','apellido_materno']) as $p)
                    <option value="{{ $p->id }}">{{ $p->apellido_paterno }} {{ $p->apellido_materno }}, {{ $p->nombres }}</option>
                    @endforeach
                </select>
            </div>

            {{-- ── Límites y restricciones (colapsable) ── --}}
            <div x-data="{ openAdv: false }">
                <button type="button" @click="openAdv = !openAdv"
                        class="flex items-center gap-1.5 text-xs font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
                    <svg class="w-3.5 h-3.5 transition-transform" :class="openAdv ? 'rotate-90' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                    Opciones avanzadas (límites, IPs, vencimiento)
                </button>
                <div x-show="openAdv" x-transition class="mt-3 space-y-3 pl-4 border-l-2 border-gray-100 dark:border-gray-700">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Límite / minuto</label>
                            <input type="number" name="rate_per_minute" min="1" max="32767" placeholder="Sin límite"
                                   class="w-full text-sm px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Límite / día</label>
                            <input type="number" name="rate_per_day" min="1" max="32767" placeholder="Sin límite"
                                   class="w-full text-sm px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">IPs permitidas <span class="font-normal text-gray-400">(vacío = todas)</span></label>
                        <input type="text" name="allowed_ips" placeholder="192.168.1.10, 10.0.0.1"
                               class="w-full text-sm px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Vence el</label>
                        <input type="date" name="expires_at"
                               class="w-full text-sm px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Notas internas</label>
                        <textarea name="notes" rows="2" maxlength="500"
                                  class="w-full text-sm px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none resize-none"></textarea>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-100 dark:border-gray-700">
                <button type="button" onclick="closeModal('modal-gateway-key')"
                        class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition-colors">
                    Cancelar
                </button>
                <button type="submit"
                        class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-white bg-amber-600 hover:bg-amber-700 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                    Registrar y generar clave
                </button>
            </div>
        </form>
    </div>
</div>
@endcan

<x-doc-preview-modal />

@push('scripts')
<script>
(function () {
    const fieldStoreUrl  = "{{ route('systems.services.fields.store', [$system, $service]) }}";
    const fieldUpdateBase = "{{ url('systems/' . $system->id . '/services/' . $service->id . '/fields') }}/";

    function openModal(id) { const m = document.getElementById(id); m.classList.remove('hidden'); m.classList.add('flex'); }
    function closeModal(id) { const m = document.getElementById(id); m.classList.add('hidden'); m.classList.remove('flex'); }
    window.openModal  = openModal;
    window.closeModal = closeModal;

    // ── Agregar campo: reset ─────────────────────────────────────────────────
    document.querySelector('[onclick="openModal(\'modal-field\')"]')?.addEventListener('click', function () {
        document.getElementById('field-modal-title').textContent   = 'Agregar campo';
        document.getElementById('field-submit-label').textContent  = 'Agregar';
        document.getElementById('field-form').action = fieldStoreUrl;
        document.getElementById('field-method').innerHTML = '';
        document.getElementById('field-form').reset();
    });

    // ── Editar campo ─────────────────────────────────────────────────────────
    window.editField = function (id, data) {
        document.getElementById('field-modal-title').textContent   = 'Editar campo';
        document.getElementById('field-submit-label').textContent  = 'Guardar';
        document.getElementById('field-form').action = fieldUpdateBase + id;
        document.getElementById('field-method').innerHTML = '<input type="hidden" name="_method" value="PUT">';
        document.getElementById('field-direction').value   = data.direction   ?? 'request';
        document.getElementById('field-name').value        = data.field_name  ?? '';
        document.getElementById('field-type').value        = data.field_type  ?? 'string';
        document.getElementById('field-required').checked  = !!data.is_required;
        document.getElementById('field-description').value = data.description  ?? '';
        document.getElementById('field-example').value     = data.example_value ?? '';
        openModal('modal-field');
    };

    // ── Credenciales toggle ──────────────────────────────────────────────────
    window.toggleCred = function (field) {
        const mask  = document.getElementById('cred-mask-'  + field);
        const plain = document.getElementById('cred-plain-' + field);
        const show  = mask.classList.toggle('hidden');
        plain.classList.toggle('hidden', !show);
    };

    window.copyToClipboard = function (field) {
        const plain = document.getElementById('cred-plain-' + field);
        navigator.clipboard.writeText(plain.textContent.trim());
    };

    // ── Documento: actualizar nombre ─────────────────────────────────────────
    window.updateDocName = function (name) {
        if (!name) return;
        document.getElementById('doc-file-label').innerHTML =
            '<span class="font-medium text-gray-700 dark:text-gray-300">' + name + '</span>';
    };
})();
</script>
@endpush
@endsection
