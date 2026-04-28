@extends('layouts.app')
@section('title', $system->acronym ?? $system->name)

@section('content')
<div class="space-y-6" x-data="{ tab: window.location.hash ? window.location.hash.slice(1) : 'general' }">

    {{-- Hero Header --}}
    @php
    $sv = $system->status->value;
    $gradientColors = match($sv) {
        'active'      => 'linear-gradient(135deg, #16a34a 0%, #059669 100%)',
        'development' => 'linear-gradient(135deg, #2563eb 0%, #4f46e5 100%)',
        'maintenance' => 'linear-gradient(135deg, #f59e0b 0%, #ea580c 100%)',
        'inactive'    => 'linear-gradient(135deg, #6b7280 0%, #374151 100%)',
        default       => 'linear-gradient(135deg, #6b7280 0%, #374151 100%)',
    };
    $accentColor = match($sv) {
        'active'      => '#16a34a',
        'development' => '#2563eb',
        'maintenance' => '#f59e0b',
        'inactive'    => '#6b7280',
        default       => '#6b7280',
    };
    @endphp
    <div class="relative rounded-2xl shadow-lg overflow-hidden">
        {{-- Background gradient --}}
        <div style="position: absolute; inset: 0; background: {{ $gradientColors }}; z-index: 0;"></div>

        {{-- Decorative wave pattern --}}
        <div style="position: absolute; top: 0; left: 0; right: 0; height: 60%; opacity: 0.1; z-index: 1;">
            <svg class="w-full h-full" viewBox="0 0 1440 200" preserveAspectRatio="none">
                <path d="M0,80 C240,120 480,40 720,80 C960,120 1200,40 1440,80 L1440,0 L0,0 Z" fill="rgba(255,255,255,0.3)"/>
            </svg>
        </div>

        {{-- Content --}}
        <div style="position: relative; z-index: 10; padding: 1.5rem 2rem;" class="sm:px-8">
            <div class="flex flex-col lg:flex-row lg:items-center gap-6">
                {{-- Avatar --}}
                <div style="flex-shrink: 0; width: 4rem; height: 4rem; border-radius: 1rem; background: rgba(255,255,255,0.2); backdrop-filter: blur(4px); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 1.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    {{ strtoupper(substr($system->acronym ?? $system->name, 0, 3)) }}
                </div>

                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-start gap-3 flex-wrap">
                        <h1 class="text-2xl font-bold text-white leading-tight">{{ $system->name }}</h1>
                        <span style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.25rem 0.75rem; font-size: 0.75rem; font-weight: 600; border-radius: 9999px; background: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.3);">
                            <span style="width: 0.5rem; height: 0.5rem; border-radius: 50%; background: white; display: inline-block;"></span>
                            {{ $system->status->label() }}
                        </span>
                    </div>
                    <div class="mt-2 flex flex-wrap gap-x-5 gap-y-2 text-sm text-white/90">
                        @if($system->area)
                        <span class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-white/70 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            {{ $system->area->name }}
                        </span>
                        @endif
                        @if($system->responsible)
                        <span class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-white/70 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            {{ $system->responsible->name }}
                        </span>
                        @endif
                        @if($system->infrastructure?->system_url)
                        <a href="{{ $system->infrastructure->system_url }}" target="_blank"
                           class="flex items-center gap-1.5 hover:text-white transition-colors">
                            <svg class="w-4 h-4 text-white/70 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                            <span class="truncate max-w-xs">{{ $system->infrastructure->system_url }}</span>
                        </a>
                        @endif
                        @if(!empty($system->tech_stack))
                        <span class="flex items-center gap-1.5 flex-wrap">
                            <svg class="w-4 h-4 text-white/70 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                            @foreach($system->tech_stack as $tag)
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-white/20 text-white">{{ $tag }}</span>
                            @endforeach
                        </span>
                        @endif
                    </div>
                </div>

                {{-- Acciones --}}
                <div class="flex items-center gap-2 flex-shrink-0">
                    @can('systems.edit')
                    <a href="{{ route('systems.edit', $system) }}"
                       style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.5rem 1rem; background: rgba(255,255,255,0.2); color: white; font-size: 0.875rem; font-weight: 500; border-radius: 0.5rem; border: 1px solid rgba(255,255,255,0.3); backdrop-filter: blur(4px); transition: all 0.2s;">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                        Editar
                    </a>
                    @endcan
                    @can('systems.delete')
                    <form action="{{ route('systems.destroy', $system) }}" method="POST" class="inline">
                        @csrf @method('DELETE')
                        <button type="button" x-data
                                @click.prevent="sgDeleteForm($el.closest('form'), '¿Eliminar este sistema? Esta acción no se puede deshacer.')"
                                style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.5rem 1rem; background: rgba(255,255,255,0.1); color: white; font-size: 0.875rem; font-weight: 500; border-radius: 0.5rem; border: 1px solid rgba(255,255,255,0.2); backdrop-filter: blur(4px); transition: all 0.2s;">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            Eliminar
                        </button>
                    </form>
                    @endcan
                </div>
            </div>
        </div>

        {{-- Quick stats --}}
        <div style="position: relative; z-index: 10; display: grid; grid-template-columns: repeat(2, 1fr); border-top: 1px solid rgba(255,255,255,0.2);" class="sm:grid-cols-4">
            @php
            $quickStats = [
                ['label' => 'Versiones',     'val' => $system->versions->count(),         'tab' => 'versions'],
                ['label' => 'Bases de datos','val' => $system->databases->count(),         'tab' => 'databases'],
                ['label' => 'Servicios',     'val' => $system->services->count(),          'tab' => 'services'],
                ['label' => 'Documentos',    'val' => $system->documents->count(),         'tab' => 'documents'],
            ];
            @endphp
            @foreach($quickStats as $qs)
            <button @click="tab = '{{ $qs['tab'] }}'; window.location.hash = '{{ $qs['tab'] }}'"
                    style="display: flex; flex-direction: column; align-items: center; padding: 1rem; transition: background-color 0.2s; cursor: pointer; border: none; background: none;"
                    onmouseover="this.style.backgroundColor='rgba(255,255,255,0.1)'"
                    onmouseout="this.style.backgroundColor='transparent'">
                <span style="font-size: 1.5rem; font-weight: 700; color: white;">{{ $qs['val'] }}</span>
                <span style="font-size: 0.75rem; color: rgba(255,255,255,0.8); margin-top: 0.25rem;">{{ $qs['label'] }}</span>
            </button>
            @endforeach
        </div>
    </div>

    {{-- Tab Navigation --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
        <div class="border-b border-gray-200 dark:border-gray-700 overflow-x-auto">
            <nav class="flex min-w-max px-4" aria-label="Tabs">
                @php
                $tabs = [
                    ['id' => 'general',         'label' => 'General',        'icon' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                    ['id' => 'infrastructure',  'label' => 'Infraestructura','icon' => 'M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01'],
                    ['id' => 'versions',        'label' => 'Versiones',      'icon' => 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z'],
                    ['id' => 'databases',       'label' => 'Bases de Datos', 'icon' => 'M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4'],
                    ['id' => 'services',        'label' => 'Servicios/APIs', 'icon' => 'M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                    ['id' => 'integrations',    'label' => 'Integraciones',  'icon' => 'M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z'],
                    ['id' => 'repositories',    'label' => 'Repositorios',   'icon' => 'M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4'],
                    ['id' => 'documents',       'label' => 'Documentos',     'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                    ['id' => 'responsibles',    'label' => 'Responsables',   'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
                    ['id' => 'logs',            'label' => 'Historial',      'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                ];
                @endphp
                @foreach($tabs as $t)
                <button @click="tab = '{{ $t['id'] }}'; window.location.hash = '{{ $t['id'] }}'"
                        x-bind:style="tab === '{{ $t['id'] }}' ? 'border-bottom: 2px solid {{ $accentColor }}; color: {{ $accentColor }};' : 'border-bottom: 2px solid transparent; color: rgb(107 114 128);'"
                        class="flex items-center gap-2 px-4 py-4 text-sm font-medium transition-colors whitespace-nowrap focus:outline-none cursor-pointer dark:text-gray-400 dark:hover:text-gray-200">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $t['icon'] }}"/>
                    </svg>
                    {{ $t['label'] }}
                </button>
                @endforeach
            </nav>
        </div>

        {{-- Tab Panels --}}
        <div class="p-5">
            <div x-show="tab === 'general'"        x-cloak> @include('systems.tabs.general') </div>
            <div x-show="tab === 'infrastructure'" x-cloak> @include('systems.tabs.infrastructure') </div>
            <div x-show="tab === 'versions'"       x-cloak> @include('systems.tabs.versions') </div>
            <div x-show="tab === 'databases'"      x-cloak> @include('systems.tabs.databases') </div>
            <div x-show="tab === 'services'"       x-cloak> @include('systems.tabs.services') </div>
            <div x-show="tab === 'integrations'"   x-cloak> @include('systems.tabs.integrations') </div>
            <div x-show="tab === 'repositories'"   x-cloak> @include('systems.tabs.repositories') </div>
            <div x-show="tab === 'documents'"      x-cloak> @include('systems.tabs.documents') </div>
            <div x-show="tab === 'responsibles'"   x-cloak> @include('systems.tabs.responsibles') </div>
            <div x-show="tab === 'logs'"           x-cloak> @include('systems.tabs.logs') </div>
        </div>
    </div>

</div>

{{-- Modal de previsualización de documentos (compartido entre tabs) --}}
<x-doc-preview-modal />

@endsection
