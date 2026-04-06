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

    <form action="{{ route('systems.store') }}" method="POST" class="space-y-5">
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
                               placeholder="Sistema de Gestión Académica" required>
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
                              placeholder="Breve descripción del propósito del sistema…">{{ old('description') }}</textarea>
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

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
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
                    <div>
                        <label for="responsible_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Responsable</label>
                        <select id="responsible_id" name="responsible_id"
                                class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">Sin responsable asignado</option>
                            @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('responsible_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('responsible_id')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Detalles Técnicos --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/30">
                <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Detalles Técnicos</h2>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="tech_stack" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Stack Tecnológico</label>
                        <input type="text" id="tech_stack" name="tech_stack" value="{{ old('tech_stack') }}"
                               class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                               placeholder="Laravel, PostgreSQL, Vue.js…">
                        @error('tech_stack')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="repo_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">URL del Repositorio</label>
                        <input type="url" id="repo_url" name="repo_url" value="{{ old('repo_url') }}"
                               class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                               placeholder="https://github.com/org/repo">
                        @error('repo_url')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div>
                    <label for="observations" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Observaciones</label>
                    <textarea id="observations" name="observations" rows="3"
                              class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                              placeholder="Notas adicionales sobre el sistema…">{{ old('observations') }}</textarea>
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
                    class="inline-flex items-center gap-2 px-5 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Registrar Sistema
            </button>
        </div>
    </form>
</div>
@endsection
