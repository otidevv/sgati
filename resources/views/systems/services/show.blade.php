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

<div class="max-w-3xl mx-auto space-y-6">

    {{-- ── Encabezado ── --}}
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
        <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
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
</div>

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
        navigator.clipboard.writeText(plain.textContent.trim()).then(() => {
            // feedback visual breve
        });
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
