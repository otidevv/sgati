@extends('layouts.app')
@section('title', $system->acronym ?? $system->name)

@section('content')
<div class="space-y-5" x-data="{ tab: '{{ session('tab', 'general') }}' }">

    {{-- Hero Header --}}
    @php
    $sv = $system->status->value;
    $heroBg = match($sv) {
        'active'      => 'from-green-600 to-emerald-700',
        'development' => 'from-blue-600 to-indigo-700',
        'maintenance' => 'from-yellow-500 to-orange-600',
        'inactive'    => 'from-gray-500 to-gray-700',
        default       => 'from-gray-500 to-gray-700',
    };
    @endphp
    <div class="bg-gradient-to-r {{ $heroBg }} rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-5 flex flex-col sm:flex-row sm:items-center gap-4">
            {{-- Avatar --}}
            <div class="flex-shrink-0 w-16 h-16 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center text-white font-bold text-xl tracking-wide shadow-inner">
                {{ strtoupper(substr($system->acronym ?? $system->name, 0, 3)) }}
            </div>

            {{-- Info --}}
            <div class="flex-1 min-w-0">
                <div class="flex items-start gap-3 flex-wrap">
                    <h1 class="text-xl font-bold text-white leading-tight">{{ $system->name }}</h1>
                    <x-status-badge :status="$system->status"
                        class="bg-white/20 !text-white !ring-white/30 flex-shrink-0 mt-0.5" />
                </div>
                <div class="mt-1.5 flex flex-wrap gap-x-4 gap-y-1 text-sm text-white/80">
                    @if($system->area)
                    <span class="flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-white/60" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        {{ $system->area->name }}
                    </span>
                    @endif
                    @if($system->responsible)
                    <span class="flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-white/60" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        {{ $system->responsible->name }}
                    </span>
                    @endif
                    @if($system->infrastructure?->system_url)
                    <a href="{{ $system->infrastructure->system_url }}" target="_blank"
                       class="flex items-center gap-1 hover:text-white transition-colors">
                        <svg class="w-3.5 h-3.5 text-white/60" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        {{ $system->infrastructure->system_url }}
                    </a>
                    @endif
                    @if($system->tech_stack)
                    <span class="flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-white/60" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                        {{ $system->tech_stack }}
                    </span>
                    @endif
                </div>
            </div>

            {{-- Acciones --}}
            <div class="flex items-center gap-2 flex-shrink-0">
                @can('systems.edit')
                <a href="{{ route('systems.edit', $system) }}"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white/20 hover:bg-white/30 text-white text-xs font-medium rounded-lg transition-colors backdrop-blur-sm">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                    Editar
                </a>
                @endcan
                @can('systems.delete')
                <form action="{{ route('systems.destroy', $system) }}" method="POST" class="inline">
                    @csrf @method('DELETE')
                    <button type="button" x-data
                            @click.prevent="if(confirm('¿Eliminar este sistema? Esta acción no se puede deshacer.')) $el.closest('form').submit()"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white/10 hover:bg-red-500/60 text-white text-xs font-medium rounded-lg transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Eliminar
                    </button>
                </form>
                @endcan
            </div>
        </div>

        {{-- Quick stats --}}
        <div class="grid grid-cols-4 border-t border-white/10">
            @php
            $quickStats = [
                ['label' => 'Versiones',     'val' => $system->versions->count(),         'tab' => 'versions'],
                ['label' => 'Bases de datos','val' => $system->databases->count(),         'tab' => 'databases'],
                ['label' => 'Servicios',     'val' => $system->services->count(),          'tab' => 'services'],
                ['label' => 'Documentos',    'val' => $system->documents->count(),         'tab' => 'documents'],
            ];
            @endphp
            @foreach($quickStats as $qs)
            <button @click="tab = '{{ $qs['tab'] }}'"
                    class="flex flex-col items-center py-3 hover:bg-white/10 transition-colors cursor-pointer">
                <span class="text-xl font-bold text-white">{{ $qs['val'] }}</span>
                <span class="text-xs text-white/70 mt-0.5">{{ $qs['label'] }}</span>
            </button>
            @endforeach
        </div>
    </div>

    {{-- Tab Navigation --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="border-b border-gray-200 overflow-x-auto">
            <nav class="flex min-w-max px-2" aria-label="Tabs">
                @php
                $tabs = [
                    ['id' => 'general',         'label' => 'General',        'icon' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                    ['id' => 'infrastructure',  'label' => 'Infraestructura','icon' => 'M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01'],
                    ['id' => 'versions',        'label' => 'Versiones',      'icon' => 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z'],
                    ['id' => 'databases',       'label' => 'Bases de Datos', 'icon' => 'M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4'],
                    ['id' => 'services',        'label' => 'Servicios/APIs', 'icon' => 'M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                    ['id' => 'integrations',    'label' => 'Integraciones',  'icon' => 'M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z'],
                    ['id' => 'documents',       'label' => 'Documentos',     'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                    ['id' => 'logs',            'label' => 'Historial',      'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                ];
                @endphp
                @foreach($tabs as $t)
                <button @click="tab = '{{ $t['id'] }}'"
                        :class="tab === '{{ $t['id'] }}' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="flex items-center gap-1.5 px-4 py-3.5 text-xs font-medium border-b-2 transition-colors whitespace-nowrap focus:outline-none">
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
            <div x-show="tab === 'documents'"      x-cloak> @include('systems.tabs.documents') </div>
            <div x-show="tab === 'logs'"           x-cloak> @include('systems.tabs.logs') </div>
        </div>
    </div>

</div>
@endsection
