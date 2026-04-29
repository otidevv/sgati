@extends('layouts.app')
@section('title', 'Nuevo Sistema')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('systems.index') }}"
           class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Nuevo Sistema</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Registrar un sistema de información en el inventario</p>
        </div>
    </div>

    <form action="{{ route('systems.store') }}" method="POST" class="space-y-5"
          x-data="{ submitting: false }" @submit="submitting = true">
        @csrf

        {{-- Información General --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/30">
                <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Información General</h2>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="sm:col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Nombre del Sistema <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}"
                               class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                               placeholder="Sistema de Gestión Académica"
                               maxlength="100" required>
                        @error('name')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="acronym" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Siglas / Acrónimo</label>
                        <input type="text" id="acronym" name="acronym" value="{{ old('acronym') }}"
                               class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm font-mono uppercase"
                               placeholder="SGA" maxlength="20">
                        @error('acronym')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Descripción</label>
                    <textarea id="description" name="description" rows="3"
                              class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                              placeholder="Breve descripción del propósito del sistema…"
                              maxlength="1000">{{ old('description') }}</textarea>
                    @error('description')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- Clasificación --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/30">
                <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Clasificación</h2>
            </div>
            <div class="p-6 space-y-4">
                {{-- Estado --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Estado <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                        @foreach($statuses as $s)
                        @php
                        $checked = old('status', 'active') === $s->value;
                        $colorMap = [
                            'active'      => ['bg' => 'rgb(220, 252, 231)', 'border' => 'rgb(22, 163, 74)', 'text' => 'rgb(22, 101, 52)', 'dot' => 'rgb(22, 163, 74)'],
                            'development' => ['bg' => 'rgb(219, 234, 254)', 'border' => 'rgb(37, 99, 235)', 'text' => 'rgb(30, 64, 175)', 'dot' => 'rgb(37, 99, 235)'],
                            'maintenance' => ['bg' => 'rgb(254, 243, 199)', 'border' => 'rgb(245, 158, 11)', 'text' => 'rgb(146, 64, 14)', 'dot' => 'rgb(245, 158, 11)'],
                            'inactive'    => ['bg' => 'rgb(254, 226, 226)', 'border' => 'rgb(220, 38, 38)', 'text' => 'rgb(153, 27, 27)', 'dot' => 'rgb(220, 38, 38)'],
                        ];
                        $colors = $colorMap[$s->value] ?? ['bg' => 'rgb(243, 244, 246)', 'border' => 'rgb(107, 114, 128)', 'text' => 'rgb(55, 65, 81)', 'dot' => 'rgb(107, 114, 128)'];
                        @endphp
                        <label class="relative cursor-pointer">
                            <input type="radio" name="status" value="{{ $s->value }}" {{ $checked ? 'checked' : '' }}
                                   class="peer sr-only"
                                   onchange="document.querySelectorAll('input[name=status]').forEach(r => {
                                       const div = r.nextElementSibling;
                                       div.style.backgroundColor = 'white';
                                       div.style.borderColor = 'rgb(229, 231, 235)';
                                       div.style.color = 'rgb(55, 65, 81)';
                                   });
                                   this.nextElementSibling.style.backgroundColor = '{{ $colors['bg'] }}';
                                   this.nextElementSibling.style.borderColor = '{{ $colors['border'] }}';
                                   this.nextElementSibling.style.color = '{{ $colors['text'] }}';">
                            <div class="flex items-center gap-2 px-3 py-2.5 rounded-lg border-2 transition-all"
                                 style="background: {{ $checked ? $colors['bg'] : 'white' }}; 
                                        border-color: {{ $checked ? $colors['border'] : 'rgb(229, 231, 235)' }}; 
                                        color: {{ $checked ? $colors['text'] : 'rgb(55, 65, 81)' }};">
                                <span class="w-2 h-2 rounded-full flex-shrink-0"
                                      style="background: {{ $colors['dot'] }};"></span>
                                <span class="text-xs font-medium">{{ $s->label() }}</span>
                            </div>
                        </label>
                        @endforeach
                    </div>
                    @error('status')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="area_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Área</label>
                    <select id="area_id" name="area_id"
                            class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="">Sin área asignada</option>
                        @foreach($areas as $area)
                        <option value="{{ $area->id }}" {{ old('area_id') == $area->id ? 'selected' : '' }}>
                            {{ $area->name }}{{ $area->acronym ? " ({$area->acronym})" : '' }}
                        </option>
                        @endforeach
                    </select>
                    @error('area_id')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                </div>
                <input type="hidden" name="responsible_id" value="{{ auth()->id() }}">
            </div>
        </div>

        {{-- Detalles Técnicos --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/30">
                <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Detalles Técnicos</h2>
            </div>
            <div class="p-6 space-y-4">
                <div x-data="{
                        tags: {{ old('tech_stack') ? old('tech_stack') : '[]' }},
                        input: '',
                        add() {
                            const val = this.input.trim();
                            if (!val || val.length > 50) return;
                            if (!this.tags.includes(val)) this.tags.push(val);
                            this.input = '';
                        },
                        remove(i) { this.tags.splice(i, 1); }
                    }">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Stack Tecnológico</label>
                        <div class="flex gap-2">
                            <input type="text" x-model="input"
                                   @keydown.enter.prevent="add()"
                                   maxlength="50"
                                   class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="Laravel, PHP, Vue.js…">
                            <button type="button" @click="add()"
                                    class="flex-shrink-0 px-3 py-2 text-xs font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                                Agregar
                            </button>
                        </div>
                        <div class="flex flex-wrap gap-1.5 mt-2" x-show="tags.length > 0">
                            <template x-for="(tag, i) in tags" :key="i">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium
                                             bg-blue-50 text-blue-700 border border-blue-200
                                             dark:bg-blue-900/30 dark:text-blue-300 dark:border-blue-700">
                                    <span x-text="tag"></span>
                                    <button type="button" @click="remove(i)"
                                            class="ml-0.5 text-blue-400 hover:text-blue-600 dark:hover:text-blue-200 leading-none">&times;</button>
                                </span>
                            </template>
                        </div>
                        <input type="hidden" name="tech_stack" :value="JSON.stringify(tags)">
                        @error('tech_stack')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>
                <div>
                    <label for="observations" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Observaciones</label>
                    <textarea id="observations" name="observations" rows="3"
                              class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                              placeholder="Notas adicionales sobre el sistema…"
                              maxlength="2000">{{ old('observations') }}</textarea>
                    @error('observations')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('systems.index') }}"
               class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                Cancelar
            </a>
            <button type="submit"
                    :disabled="submitting"
                    class="inline-flex items-center gap-2 px-5 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors shadow-sm disabled:opacity-60 disabled:cursor-not-allowed">
                <svg x-show="!submitting" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <svg x-show="submitting" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                </svg>
                <span x-text="submitting ? 'Registrando…' : 'Registrar Sistema'"></span>
            </button>
        </div>
    </form>
</div>
@endsection
