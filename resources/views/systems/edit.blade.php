@extends('layouts.app')
@section('title', 'Editar Sistema')

@section('content')
{{-- Script ANTES del x-data --}}
<script>
function wizardEditForm(initialStep) {
    return {
        step: initialStep,
        submitting: false,
        nameError: false,
        systemName:     {!! json_encode(old('name', $system->name)) !!},
        originType:     {!! json_encode(old('origin_type', $system->origin?->origin_type?->value ?? '')) !!},
        currentStatus:  {!! json_encode(old('status', $system->status->value)) !!},
        originalStatus: {!! json_encode($system->status->value) !!},
        tags:           {!! json_encode(old('tech_stack') ? (json_decode(old('tech_stack'), true) ?: []) : ($system->tech_stack ?? [])) !!},
        tagInput: '',
        originLabels: {
            '':            'Sin especificar',
            'donated':     'Donado',
            'third_party': 'Creado por terceros',
            'internal':    'Desarrollo interno',
            'state':       'Sistema del Estado Peruano',
        },
        next() {
            if (this.step === 1 && !this.systemName.trim()) {
                this.nameError = true;
                setTimeout(() => document.getElementById('name')?.focus(), 50);
                return;
            }
            this.nameError = false;
            this.step = Math.min(this.step + 1, 4);
        },
        prev() { this.step = Math.max(this.step - 1, 1); },
        handleSubmit(form) {
            if (this.step < 4) { this.next(); return; }
            this.submitting = true;
            form.submit();
        },
        addTag() {
            const val = this.tagInput.trim();
            if (!val || val.length > 50 || this.tags.includes(val)) return;
            this.tags.push(val);
            this.tagInput = '';
        },
        removeTag(i) { this.tags.splice(i, 1); },
    };
}
</script>

@php
$origin = $system->origin;
$initialStep = 1;
if ($errors->hasAny(['tech_stack','observations','repo_url'])) $initialStep = 4;
elseif ($errors->hasAny(['origin_type','donor_name','company_name','team_name','state_entity'])) $initialStep = 3;
elseif ($errors->hasAny(['status','area_id','responsible_id','status_reason'])) $initialStep = 2;
@endphp

<div class="max-w-3xl mx-auto" x-data="wizardEditForm({{ $initialStep }})">

    {{-- Header --}}
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('systems.show', $system) }}"
           class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors flex-shrink-0">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div class="min-w-0">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Editar Sistema</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 truncate">{{ $system->name }}</p>
        </div>
        <x-status-badge :status="$system->status" class="ml-auto flex-shrink-0" />
    </div>

    {{-- ── Indicador de pasos ── --}}
    @php
    $wizardSteps = [
        1 => ['label'=>'Identificación','icon'=>'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
        2 => ['label'=>'Clasificación', 'icon'=>'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z'],
        3 => ['label'=>'Procedencia',   'icon'=>'M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7'],
        4 => ['label'=>'Técnico',       'icon'=>'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z'],
    ];
    @endphp

    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm px-6 py-5 mb-5">
        <div class="flex items-center">
            @foreach($wizardSteps as $n => $ws)
            <div class="flex flex-col items-center flex-shrink-0">
                <div class="w-10 h-10 rounded-full flex items-center justify-center transition-all duration-300"
                     :class="{
                         'bg-blue-600 ring-4 ring-blue-100 dark:ring-blue-900/40 shadow-md': step === {{ $n }},
                         'bg-green-500 shadow-sm': step > {{ $n }},
                         'bg-gray-100 dark:bg-gray-700 border-2 border-gray-200 dark:border-gray-600': step < {{ $n }}
                     }">
                    <svg x-show="step > {{ $n }}" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                    <svg x-show="step === {{ $n }}" class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $ws['icon'] }}"/>
                    </svg>
                    <span x-show="step < {{ $n }}" class="text-sm font-bold text-gray-400 dark:text-gray-500">{{ $n }}</span>
                </div>
                <span class="mt-2 text-xs font-medium hidden sm:block transition-colors duration-200"
                      :class="{
                          'text-blue-600 dark:text-blue-400 font-semibold': step === {{ $n }},
                          'text-green-600 dark:text-green-400': step > {{ $n }},
                          'text-gray-400 dark:text-gray-500': step < {{ $n }}
                      }">{{ $ws['label'] }}</span>
            </div>
            @if($n < 4)
            <div class="flex-1 h-0.5 mx-2 sm:mx-3 rounded-full transition-all duration-500"
                 :class="step > {{ $n }} ? 'bg-green-400' : 'bg-gray-200 dark:bg-gray-700'"></div>
            @endif
            @endforeach
        </div>
    </div>

    <form action="{{ route('systems.update', $system) }}" method="POST"
          @submit.prevent="handleSubmit($el)">
        @csrf
        @method('PUT')

        {{-- ════ PASO 1 · Identificación ════ --}}
        <div x-show="step === 1"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0">
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/30">
                    <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Información General</h2>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Nombre, siglas y descripción del sistema</p>
                </div>
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="sm:col-span-2">
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Nombre del Sistema <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="name" name="name"
                                   x-model="systemName"
                                   @input="nameError = false"
                                   :class="nameError
                                       ? 'border-red-400 focus:ring-red-400 focus:border-red-400'
                                       : 'border-gray-300 dark:border-gray-600 focus:ring-blue-500 focus:border-blue-500'"
                                   class="block w-full rounded-lg dark:bg-gray-700 dark:text-gray-100 shadow-sm sm:text-sm"
                                   maxlength="100">
                            <p x-show="nameError" x-transition
                               class="mt-1 text-sm text-red-600 dark:text-red-400">
                                El nombre del sistema es obligatorio.
                            </p>
                            @error('name')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="acronym" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Siglas / Acrónimo</label>
                            <input type="text" id="acronym" name="acronym" value="{{ old('acronym', $system->acronym) }}"
                                   class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm font-mono uppercase"
                                   maxlength="20">
                            @error('acronym')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Descripción</label>
                        <textarea id="description" name="description" rows="4"
                                  class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">{{ old('description', $system->description) }}</textarea>
                        @error('description')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- ════ PASO 2 · Clasificación ════ --}}
        <div x-show="step === 2"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0">
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/30">
                    <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Clasificación</h2>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Estado, área y responsable del sistema</p>
                </div>
                <div class="p-6 space-y-5">

                    {{-- Estado --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                            Estado del sistema <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            @foreach($statuses as $s)
                            @php
                            $checked = old('status', $system->status->value) === $s->value;
                            $borderActive = match($s->value) {
                                'active'      => 'border-green-500 bg-green-50 dark:bg-green-900/30',
                                'development' => 'border-blue-500 bg-blue-50 dark:bg-blue-900/30',
                                'maintenance' => 'border-yellow-500 bg-yellow-50 dark:bg-yellow-900/30',
                                'inactive'    => 'border-red-500 bg-red-50 dark:bg-red-900/30',
                                default       => 'border-gray-400 bg-gray-50',
                            };
                            $dotColor = match($s->value) {
                                'active'      => 'bg-green-500',
                                'development' => 'bg-blue-500',
                                'maintenance' => 'bg-yellow-400',
                                'inactive'    => 'bg-red-400',
                                default       => 'bg-gray-400',
                            };
                            @endphp
                            <label class="cursor-pointer">
                                <input type="radio" name="status" value="{{ $s->value }}"
                                       {{ $checked ? 'checked' : '' }}
                                       x-model="currentStatus"
                                       class="peer sr-only">
                                <div class="flex flex-col items-center gap-2 p-4 rounded-xl border-2 border-gray-200 dark:border-gray-600
                                            peer-checked:{{ $borderActive }}
                                            transition-all hover:border-gray-300 dark:hover:border-gray-500 text-center">
                                    <span class="w-3 h-3 rounded-full {{ $dotColor }}"></span>
                                    <span class="text-xs font-semibold text-gray-700 dark:text-gray-200 leading-tight">{{ $s->label() }}</span>
                                </div>
                            </label>
                            @endforeach
                        </div>
                        @error('status')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>

                    {{-- Motivo de cambio de estado --}}
                    <div x-show="currentStatus !== originalStatus"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0">
                        <div class="p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl">
                            <label for="status_reason" class="block text-sm font-medium text-amber-800 dark:text-amber-300 mb-1">
                                Motivo del cambio de estado
                            </label>
                            <textarea id="status_reason" name="status_reason" rows="2"
                                      class="block w-full rounded-lg border-amber-300 dark:border-amber-700 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-amber-500 focus:border-amber-500 sm:text-sm"
                                      placeholder="Describe el motivo del cambio…">{{ old('status_reason') }}</textarea>
                        </div>
                    </div>

                    {{-- Área y Responsable --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="area_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Área</label>
                            <select id="area_id" name="area_id"
                                    class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="">Sin área asignada</option>
                                @foreach($areas as $area)
                                <option value="{{ $area->id }}" {{ old('area_id', $system->area_id) == $area->id ? 'selected' : '' }}>
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
                                <option value="{{ $user->id }}" {{ old('responsible_id', $system->responsible_id) == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('responsible_id')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ════ PASO 3 · Procedencia ════ --}}
        <div x-show="step === 3"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0">
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/30">
                    <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Procedencia del Sistema</h2>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">¿Cómo fue obtenido o desarrollado este sistema?</p>
                </div>
                <div class="p-6 space-y-5">

                    {{-- Selector tipo --}}
                    @php
                    $oc = [
                        'donated'     => ['border'=>'border-purple-500','bg'=>'bg-purple-50 dark:bg-purple-900/30','dot'=>'bg-purple-500'],
                        'third_party' => ['border'=>'border-orange-500','bg'=>'bg-orange-50 dark:bg-orange-900/30','dot'=>'bg-orange-500'],
                        'internal'    => ['border'=>'border-teal-500',  'bg'=>'bg-teal-50 dark:bg-teal-900/30',   'dot'=>'bg-teal-500'],
                        'state'       => ['border'=>'border-red-500',   'bg'=>'bg-red-50 dark:bg-red-900/30',     'dot'=>'bg-red-500'],
                    ];
                    @endphp
                    <div class="grid grid-cols-2 sm:grid-cols-5 gap-2">
                        <label class="cursor-pointer">
                            <input type="radio" name="origin_type" value="" x-model="originType" class="peer sr-only">
                            <div class="flex flex-col items-center gap-1.5 p-3 rounded-xl border-2 border-gray-200 dark:border-gray-600
                                        peer-checked:border-gray-400 peer-checked:bg-gray-100 dark:peer-checked:bg-gray-700
                                        transition-all text-center">
                                <span class="w-2.5 h-2.5 rounded-full bg-gray-300 dark:bg-gray-500"></span>
                                <span class="text-xs font-medium text-gray-500 dark:text-gray-400 leading-tight">Sin especificar</span>
                            </div>
                        </label>
                        @foreach($originTypes as $ot)
                        @php $col = $oc[$ot->value]; @endphp
                        <label class="cursor-pointer" title="{{ $ot->description() }}">
                            <input type="radio" name="origin_type" value="{{ $ot->value }}" x-model="originType" class="peer sr-only">
                            <div class="flex flex-col items-center gap-1.5 p-3 rounded-xl border-2 border-gray-200 dark:border-gray-600
                                        peer-checked:{{ $col['border'] }} peer-checked:{{ $col['bg'] }}
                                        transition-all hover:border-gray-300 dark:hover:border-gray-500 text-center">
                                <span class="w-2.5 h-2.5 rounded-full {{ $col['dot'] }}"></span>
                                <span class="text-xs font-medium text-gray-700 dark:text-gray-200 leading-tight">{{ $ot->label() }}</span>
                            </div>
                        </label>
                        @endforeach
                    </div>

                    {{-- Sub-form: Donado --}}
                    <div x-show="originType === 'donated'"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                         class="rounded-xl border border-purple-200 dark:border-purple-800 p-5 space-y-4 bg-purple-50/40 dark:bg-purple-900/10">
                        <p class="text-xs font-bold text-purple-600 dark:text-purple-400 uppercase tracking-widest">Datos de la donación</p>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tipo de donación</label>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                @foreach(['thesis'=>'Tesis','research_project'=>'Proyecto de investigación','direct_donation'=>'Donación directa','agreement'=>'Convenio'] as $val=>$lbl)
                                <label class="cursor-pointer">
                                    <input type="radio" name="donation_type" value="{{ $val }}"
                                           {{ old('donation_type', $origin?->donation_type) === $val ? 'checked' : '' }} class="peer sr-only">
                                    <div class="px-3 py-2 text-center rounded-lg border-2 border-gray-200 dark:border-gray-600
                                                peer-checked:border-purple-500 peer-checked:bg-purple-100 dark:peer-checked:bg-purple-900/40
                                                text-xs font-medium text-gray-600 dark:text-gray-300 transition-all cursor-pointer">{{ $lbl }}</div>
                                </label>
                                @endforeach
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nombre del donante</label>
                                <input type="text" name="donor_name" value="{{ old('donor_name', $origin?->donor_name) }}"
                                       class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Institución del donante</label>
                                <input type="text" name="donor_institution" value="{{ old('donor_institution', $origin?->donor_institution) }}"
                                       class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Título de la tesis / proyecto</label>
                                <input type="text" name="thesis_title" value="{{ old('thesis_title', $origin?->thesis_title) }}"
                                       class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Autor(es)</label>
                                <input type="text" name="thesis_author" value="{{ old('thesis_author', $origin?->thesis_author) }}"
                                       class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Universidad / Institución</label>
                                <input type="text" name="thesis_university" value="{{ old('thesis_university', $origin?->thesis_university) }}"
                                       class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fecha de donación</label>
                                <input type="date" name="donation_date" value="{{ old('donation_date', $origin?->donation_date?->format('Y-m-d')) }}"
                                       class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">N° Resolución / Acta</label>
                                <input type="text" name="donation_document" value="{{ old('donation_document', $origin?->donation_document) }}"
                                       class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                            </div>
                        </div>
                    </div>

                    {{-- Sub-form: Terceros --}}
                    <div x-show="originType === 'third_party'"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                         class="rounded-xl border border-orange-200 dark:border-orange-800 p-5 space-y-4 bg-orange-50/40 dark:bg-orange-900/10">
                        <p class="text-xs font-bold text-orange-600 dark:text-orange-400 uppercase tracking-widest">Datos del proveedor</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="sm:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Empresa / Proveedor</label>
                                <input type="text" name="company_name" value="{{ old('company_name', $origin?->company_name) }}"
                                       class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Persona de contacto</label>
                                <input type="text" name="contact_name" value="{{ old('contact_name', $origin?->contact_name) }}"
                                       class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Correo de contacto</label>
                                <input type="email" name="contact_email" value="{{ old('contact_email', $origin?->contact_email) }}"
                                       class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Teléfono</label>
                                <input type="text" name="contact_phone" value="{{ old('contact_phone', $origin?->contact_phone) }}"
                                       class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">N° de Contrato</label>
                                <input type="text" name="contract_number" value="{{ old('contract_number', $origin?->contract_number) }}"
                                       class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fecha del contrato</label>
                                <input type="date" name="contract_date" value="{{ old('contract_date', $origin?->contract_date?->format('Y-m-d')) }}"
                                       class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Monto del contrato (S/.)</label>
                                <input type="number" name="contract_value" value="{{ old('contract_value', $origin?->contract_value) }}" min="0" step="0.01"
                                       class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fin de garantía</label>
                                <input type="date" name="warranty_expiry" value="{{ old('warranty_expiry', $origin?->warranty_expiry?->format('Y-m-d')) }}"
                                       class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                            </div>
                        </div>
                    </div>

                    {{-- Sub-form: Desarrollo Interno --}}
                    <div x-show="originType === 'internal'"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                         class="rounded-xl border border-teal-200 dark:border-teal-800 p-5 space-y-4 bg-teal-50/40 dark:bg-teal-900/10">
                        <p class="text-xs font-bold text-teal-600 dark:text-teal-400 uppercase tracking-widest">Datos del desarrollo</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Equipo de desarrollo</label>
                                <input type="text" name="team_name" value="{{ old('team_name', $origin?->team_name) }}"
                                       class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-teal-500 focus:border-teal-500 sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Código de proyecto</label>
                                <input type="text" name="project_code" value="{{ old('project_code', $origin?->project_code) }}"
                                       class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-teal-500 focus:border-teal-500 sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fecha de inicio</label>
                                <input type="date" name="dev_start_date" value="{{ old('dev_start_date', $origin?->dev_start_date?->format('Y-m-d')) }}"
                                       class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-teal-500 focus:border-teal-500 sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fecha de finalización</label>
                                <input type="date" name="dev_end_date" value="{{ old('dev_end_date', $origin?->dev_end_date?->format('Y-m-d')) }}"
                                       class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-teal-500 focus:border-teal-500 sm:text-sm">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Metodología</label>
                                <div class="grid grid-cols-3 sm:grid-cols-5 gap-2">
                                    @foreach(['scrum'=>'Scrum','kanban'=>'Kanban','waterfall'=>'Cascada','rup'=>'RUP','other'=>'Otra'] as $val=>$lbl)
                                    <label class="cursor-pointer">
                                        <input type="radio" name="methodology" value="{{ $val }}"
                                               {{ old('methodology', $origin?->methodology) === $val ? 'checked' : '' }} class="peer sr-only">
                                        <div class="px-2 py-2 text-center rounded-lg border-2 border-gray-200 dark:border-gray-600
                                                    peer-checked:border-teal-500 peer-checked:bg-teal-100 dark:peer-checked:bg-teal-900/40
                                                    text-xs font-medium text-gray-600 dark:text-gray-300 transition-all cursor-pointer">{{ $lbl }}</div>
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Sub-form: Sistema del Estado --}}
                    <div x-show="originType === 'state'"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                         class="rounded-xl border border-red-200 dark:border-red-800 p-5 space-y-4 bg-red-50/40 dark:bg-red-900/10">
                        <p class="text-xs font-bold text-red-600 dark:text-red-400 uppercase tracking-widest">Datos de la entidad del Estado</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Entidad del Estado</label>
                                <input type="text" name="state_entity" value="{{ old('state_entity', $origin?->state_entity) }}" placeholder="SUNAT, RENIEC, MEF…"
                                       class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Código de la entidad</label>
                                <input type="text" name="state_entity_code" value="{{ old('state_entity_code', $origin?->state_entity_code) }}"
                                       class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Código del sistema</label>
                                <input type="text" name="state_system_code" value="{{ old('state_system_code', $origin?->state_system_code) }}"
                                       class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fecha de implementación</label>
                                <input type="date" name="state_implementation_date" value="{{ old('state_implementation_date', $origin?->state_implementation_date?->format('Y-m-d')) }}"
                                       class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">URL oficial del sistema</label>
                                <input type="url" name="state_official_url" value="{{ old('state_official_url', $origin?->state_official_url) }}" placeholder="https://…"
                                       class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Base legal</label>
                                <textarea name="legal_basis" rows="2" placeholder="Decreto, resolución o norma…"
                                          class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">{{ old('legal_basis', $origin?->legal_basis) }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Notas comunes --}}
                    <div x-show="originType !== ''" x-transition>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Notas adicionales sobre la procedencia</label>
                        <textarea name="origin_notes" rows="2" placeholder="Observaciones adicionales…"
                                  class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">{{ old('origin_notes', $origin?->origin_notes) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- ════ PASO 4 · Detalles Técnicos ════ --}}
        <div x-show="step === 4"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="space-y-4">

            {{-- Mini resumen --}}
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-xs font-semibold text-blue-700 dark:text-blue-300 uppercase tracking-wider">Resumen de cambios</p>
                    <button type="button" @click="step = 1"
                            class="text-xs text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Editar
                    </button>
                </div>
                <div class="flex flex-wrap gap-2">
                    <span class="inline-flex items-center gap-1.5 bg-white dark:bg-gray-800 rounded-lg px-3 py-1.5 border border-blue-100 dark:border-blue-800/50 text-sm font-medium text-gray-800 dark:text-gray-200">
                        <svg class="w-3.5 h-3.5 text-blue-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span x-text="systemName || '(sin nombre)'"></span>
                    </span>
                    <span x-show="currentStatus !== originalStatus"
                          class="inline-flex items-center gap-1.5 bg-amber-50 dark:bg-amber-900/30 rounded-lg px-3 py-1.5 border border-amber-200 dark:border-amber-700 text-xs text-amber-700 dark:text-amber-300">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        Cambio de estado pendiente
                    </span>
                    <span class="inline-flex items-center gap-1 bg-white dark:bg-gray-800 rounded-lg px-3 py-1.5 border border-blue-100 dark:border-blue-800/50 text-xs text-gray-500 dark:text-gray-400">
                        Procedencia: <span class="font-medium text-gray-700 dark:text-gray-300 ml-1" x-text="originLabels[originType]"></span>
                    </span>
                </div>
            </div>

            {{-- Campos técnicos --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/30">
                    <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Detalles Técnicos</h2>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Stack tecnológico y observaciones</p>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Stack Tecnológico</label>
                        <div class="flex gap-2">
                            <input type="text" x-model="tagInput"
                                   @keydown.enter.prevent="addTag()"
                                   class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="Laravel, PHP, Vue.js…">
                            <button type="button" @click="addTag()"
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
                                    <button type="button" @click="removeTag(i)"
                                            class="ml-0.5 text-blue-400 hover:text-blue-600 leading-none">&times;</button>
                                </span>
                            </template>
                        </div>
                        <input type="hidden" name="tech_stack" :value="JSON.stringify(tags)">
                        @error('tech_stack')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="observations" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Observaciones</label>
                        <textarea id="observations" name="observations" rows="3"
                                  class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">{{ old('observations', $system->observations) }}</textarea>
                        @error('observations')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Navegación ── --}}
        <div class="flex items-center justify-between mt-5">

            {{-- Izquierda --}}
            <div>
                <a href="{{ route('systems.show', $system) }}" x-show="step === 1"
                   class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 transition-colors">
                    Cancelar
                </a>
                <button type="button" @click="prev()" x-show="step > 1"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Anterior
                </button>
            </div>

            {{-- Centro --}}
            <span class="text-xs text-gray-400 dark:text-gray-500 tabular-nums">
                Paso <span x-text="step" class="font-semibold text-gray-600 dark:text-gray-300"></span> de 4
            </span>

            {{-- Derecha --}}
            <div>
                <button type="button" @click="next()" x-show="step < 4"
                        class="inline-flex items-center gap-2 px-5 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                    Siguiente
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>

                <button type="submit" x-show="step === 4" :disabled="submitting"
                        class="inline-flex items-center gap-2 px-5 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors shadow-sm disabled:opacity-60 disabled:cursor-not-allowed">
                    <svg x-show="!submitting" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <svg x-show="submitting" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                    </svg>
                    <span x-text="submitting ? 'Guardando…' : 'Guardar Cambios'"></span>
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
