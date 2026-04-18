@extends('layouts.app')

@section('title', isset($server->id) ? 'Editar Servidor' : 'Nuevo Servidor')

@section('content')
@php
    $osVal     = old('operating_system', $server->operating_system ?? '');
    $osLower   = strtolower($osVal);
    $isWindows = str_contains($osLower, 'windows');
    $isUbuntu  = str_contains($osLower, 'ubuntu');

    if ($isWindows) {
        $preOs     = 'windows';
        $preOsName = 'Windows Server';
        $preOsVer  = trim(str_ireplace('Windows Server', '', $osVal));
    } elseif ($isUbuntu) {
        $preOs     = 'ubuntu';
        $preOsName = 'Ubuntu Server';
        $preOsVer  = trim(str_ireplace('Ubuntu Server', '', $osVal));
    } elseif ($osVal !== '') {
        // Tiene valor pero no es ubuntu ni windows → "otro"
        $preOs     = 'otro';
        $preOsName = $osVal;
        $preOsVer  = '';
    } else {
        $preOs     = '';
        $preOsName = '';
        $preOsVer  = '';
    }

    $proto       = $isWindows ? 'rdp' : 'ssh';
    $defaultPort = $isWindows ? 3389 : 22;
@endphp

{{-- Registrar componente Alpine ANTES de que Alpine procese el DOM --}}
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('serverWizard', () => ({
        step:        1,
        osSelected:  @json($preOs),
        osName:      @json($preOsName),
        osVersion:   @json($preOsVer),
        osFullValue: @json($osVal),
        proto:       @json($proto),
        portTouched: false,
        submitting:  false,
        osError:     false,

        updateOsValue() {
            const v = (this.osVersion || '').trim();
            this.osFullValue = v
                ? (this.osName || '') + ' ' + v
                : (this.osName || '');
        },

        selectOs(key, name) {
            if (this.osSelected !== key) this.osVersion = '';
            this.osSelected = key;
            this.osName     = name;
            this.osError    = false;
            this.updateOsValue();
        },

        setProto(p) {
            this.proto = p;
            if (!this.portTouched) this._setDefaultPort();
        },

        _setDefaultPort() {
            const el = document.getElementById('rdp_port');
            if (el) el.value = this.proto === 'rdp' ? 3389 : 22;
        },

        goNext() {
            if (this.step === 1) {
                if (!this.osSelected) {
                    this.osError = true;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                    return;
                }
                const el = document.getElementById('name');
                if (el && !el.value.trim()) {
                    el.focus();
                    el.classList.add('ring-2', 'ring-red-400', 'border-red-400');
                    setTimeout(() => el.classList.remove('ring-2', 'ring-red-400', 'border-red-400'), 2000);
                    return;
                }
            }
            if (this.step < 4) this.step++;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },

        goPrev() {
            if (this.step > 1) this.step--;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },

        submitForm() {
            if (this.submitting) return;
            this.submitting = true;
            this.$el.closest('form').submit();
        },

        init() {
            this.$watch('osVersion', () => this.updateOsValue());
            this.$watch('osName',    () => this.updateOsValue());

            this.$watch('osFullValue', val => {
                if (!this.portTouched) {
                    this.proto = /windows/i.test(val) ? 'rdp' : 'ssh';
                    this._setDefaultPort();
                }
            });

            // Auto-navegar al paso con errores de validación de Laravel
            @if($errors->any())
            const errorFields = @json($errors->keys());
            const step1Fields = ['name', 'function', 'operating_system'];
            const step2Fields = ['host_type', 'cpu_cores', 'ram_gb', 'storage_gb', 'installed_services', 'web_root', 'cloud_provider', 'cloud_region', 'cloud_instance'];
            const step3Fields = ['ssh_user', 'ssh_password', 'rdp_port'];
            if (errorFields.some(f => step1Fields.includes(f))) { this.step = 1; }
            else if (errorFields.some(f => step2Fields.includes(f))) { this.step = 2; }
            else if (errorFields.some(f => step3Fields.includes(f))) { this.step = 3; }
            else { this.step = 4; }
            @endif
        }
    }));
});
</script>

<div class="max-w-3xl mx-auto space-y-6" x-data="serverWizard">

    {{-- ── Encabezado ── --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.servers.index') }}"
           class="inline-flex items-center justify-center w-10 h-10 rounded-lg
                  bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300
                  hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                {{ isset($server->id) ? 'Editar Servidor' : 'Nuevo Servidor' }}
            </h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ isset($server->id) ? $server->name : 'Registrar servidor en la infraestructura de la OTI' }}
            </p>
        </div>
    </div>

    {{-- ── Indicador de pasos ── --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm px-6 py-5">
        <div class="flex items-center">

            {{-- Paso 1 --}}
            <div class="flex flex-col items-center shrink-0">
                <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold transition-all duration-300"
                     :class="step > 1 ? 'bg-blue-500 text-white' : (step === 1 ? 'bg-blue-600 text-white ring-4 ring-blue-100 dark:ring-blue-900/40' : 'bg-gray-100 dark:bg-gray-700 text-gray-400')">
                    <template x-if="step > 1">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                    </template>
                    <template x-if="step <= 1"><span>1</span></template>
                </div>
                <span class="mt-1.5 text-[11px] font-medium hidden sm:block transition-colors"
                      :class="step >= 1 ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400'">S. Operativo</span>
            </div>
            <div class="flex-1 h-0.5 mx-3 mb-0 sm:mb-5 transition-colors duration-500"
                 :class="step > 1 ? 'bg-blue-500' : 'bg-gray-200 dark:bg-gray-700'"></div>

            {{-- Paso 2 --}}
            <div class="flex flex-col items-center shrink-0">
                <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold transition-all duration-300"
                     :class="step > 2 ? 'bg-blue-500 text-white' : (step === 2 ? 'bg-blue-600 text-white ring-4 ring-blue-100 dark:ring-blue-900/40' : 'bg-gray-100 dark:bg-gray-700 text-gray-400')">
                    <template x-if="step > 2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                    </template>
                    <template x-if="step <= 2"><span>2</span></template>
                </div>
                <span class="mt-1.5 text-[11px] font-medium hidden sm:block transition-colors"
                      :class="step >= 2 ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400'">Recursos</span>
            </div>
            <div class="flex-1 h-0.5 mx-3 mb-0 sm:mb-5 transition-colors duration-500"
                 :class="step > 2 ? 'bg-blue-500' : 'bg-gray-200 dark:bg-gray-700'"></div>

            {{-- Paso 3 --}}
            <div class="flex flex-col items-center shrink-0">
                <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold transition-all duration-300"
                     :class="step > 3 ? 'bg-blue-500 text-white' : (step === 3 ? 'bg-blue-600 text-white ring-4 ring-blue-100 dark:ring-blue-900/40' : 'bg-gray-100 dark:bg-gray-700 text-gray-400')">
                    <template x-if="step > 3">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                    </template>
                    <template x-if="step <= 3"><span>3</span></template>
                </div>
                <span class="mt-1.5 text-[11px] font-medium hidden sm:block transition-colors"
                      :class="step >= 3 ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400'">Acceso Remoto</span>
            </div>
            <div class="flex-1 h-0.5 mx-3 mb-0 sm:mb-5 transition-colors duration-500"
                 :class="step > 3 ? 'bg-blue-500' : 'bg-gray-200 dark:bg-gray-700'"></div>

            {{-- Paso 4 --}}
            <div class="flex flex-col items-center shrink-0">
                <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold transition-all duration-300"
                     :class="step === 4 ? 'bg-blue-600 text-white ring-4 ring-blue-100 dark:ring-blue-900/40' : 'bg-gray-100 dark:bg-gray-700 text-gray-400'">
                    <span>4</span>
                </div>
                <span class="mt-1.5 text-[11px] font-medium hidden sm:block transition-colors"
                      :class="step >= 4 ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400'">Red y Extras</span>
            </div>

        </div>
    </div>

    {{-- ── Errores de validación ── --}}
    @if($errors->any())
    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-2xl p-4 flex gap-3">
        <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div>
            <p class="text-sm font-semibold text-red-700 dark:text-red-400">Corrige los siguientes errores antes de guardar:</p>
            <ul class="mt-1 text-sm text-red-600 dark:text-red-400 list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    {{-- ── Formulario ── --}}
    <form action="{{ isset($server->id) ? route('admin.servers.update', $server) : route('admin.servers.store') }}"
          method="POST">
        @csrf
        @if(isset($server->id)) @method('PUT') @endif

        {{-- Hidden OS — gestionado por Alpine --}}
        <input type="hidden" id="operating_system" name="operating_system"
               x-ref="osInput"
               :value="osFullValue">

        {{-- ═══════════ PASO 1 — Sistema Operativo e Identificación ═══════════ --}}
        <div x-show="step === 1" x-transition class="space-y-5">

            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Sistema Operativo</h2>
                    <p class="mt-0.5 text-xs text-gray-400">Selecciona el sistema operativo del servidor</p>
                </div>
                <div class="p-6 space-y-5">

                    {{-- Tarjetas OS --}}
                    <div class="grid grid-cols-3 gap-3">

                        {{-- Ubuntu --}}
                        <div @click="selectOs('ubuntu', 'Ubuntu Server')"
                             :class="osSelected === 'ubuntu'
                                 ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20 ring-2 ring-blue-400 dark:ring-blue-600'
                                 : 'border-gray-200 dark:border-gray-600 hover:border-blue-300 dark:hover:border-blue-700 hover:bg-gray-50 dark:hover:bg-gray-700/50'"
                             class="relative flex flex-col items-center gap-2 p-4 rounded-xl border-2 cursor-pointer transition-all duration-200 select-none">
                            <img src="{{ asset('images/servidores/ubuntu.png') }}" alt="Ubuntu Server"
                                 class="w-12 h-12 object-contain transition-transform duration-200"
                                 :class="osSelected === 'ubuntu' ? 'scale-110' : 'scale-100'">
                            <span class="text-xs font-semibold text-center transition-colors"
                                  :class="osSelected === 'ubuntu' ? 'text-blue-700 dark:text-blue-300' : 'text-gray-700 dark:text-gray-300'">
                                Ubuntu Server
                            </span>
                            <div x-show="osSelected === 'ubuntu'" x-transition
                                 class="absolute top-2 right-2 w-4 h-4 rounded-full bg-blue-500 flex items-center justify-center shadow-sm">
                                <svg class="w-2.5 h-2.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                        </div>

                        {{-- Windows --}}
                        <div @click="selectOs('windows', 'Windows Server')"
                             :class="osSelected === 'windows'
                                 ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20 ring-2 ring-blue-400 dark:ring-blue-600'
                                 : 'border-gray-200 dark:border-gray-600 hover:border-blue-300 dark:hover:border-blue-700 hover:bg-gray-50 dark:hover:bg-gray-700/50'"
                             class="relative flex flex-col items-center gap-2 p-4 rounded-xl border-2 cursor-pointer transition-all duration-200 select-none">
                            <img src="{{ asset('images/servidores/windows.png') }}" alt="Windows Server"
                                 class="w-12 h-12 object-contain transition-transform duration-200"
                                 :class="osSelected === 'windows' ? 'scale-110' : 'scale-100'">
                            <span class="text-xs font-semibold text-center transition-colors"
                                  :class="osSelected === 'windows' ? 'text-blue-700 dark:text-blue-300' : 'text-gray-700 dark:text-gray-300'">
                                Windows Server
                            </span>
                            <div x-show="osSelected === 'windows'" x-transition
                                 class="absolute top-2 right-2 w-4 h-4 rounded-full bg-blue-500 flex items-center justify-center shadow-sm">
                                <svg class="w-2.5 h-2.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                        </div>

                        {{-- Otro --}}
                        <div @click="selectOs('otro', '')"
                             :class="osSelected === 'otro'
                                 ? 'border-violet-500 bg-violet-50 dark:bg-violet-900/20 ring-2 ring-violet-400 dark:ring-violet-600'
                                 : 'border-gray-200 dark:border-gray-600 hover:border-violet-300 dark:hover:border-violet-700 hover:bg-gray-50 dark:hover:bg-gray-700/50'"
                             class="relative flex flex-col items-center gap-2 p-4 rounded-xl border-2 cursor-pointer transition-all duration-200 select-none">
                            <div class="w-12 h-12 flex items-center justify-center rounded-xl transition-colors"
                                 :class="osSelected === 'otro' ? 'bg-violet-100 dark:bg-violet-900/40' : 'bg-gray-100 dark:bg-gray-700'">
                                <svg class="w-6 h-6 transition-colors"
                                     :class="osSelected === 'otro' ? 'text-violet-600 dark:text-violet-400' : 'text-gray-400 dark:text-gray-500'"
                                     fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0H3"/>
                                </svg>
                            </div>
                            <span class="text-xs font-semibold text-center transition-colors"
                                  :class="osSelected === 'otro' ? 'text-violet-700 dark:text-violet-300' : 'text-gray-700 dark:text-gray-300'">
                                Otro
                            </span>
                            <div x-show="osSelected === 'otro'" x-transition
                                 class="absolute top-2 right-2 w-4 h-4 rounded-full bg-violet-500 flex items-center justify-center shadow-sm">
                                <svg class="w-2.5 h-2.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                        </div>

                    </div>

                    {{-- Versión — Ubuntu / Windows: prefijo fijo + versión --}}
                    <div x-show="osSelected === 'ubuntu' || osSelected === 'windows'" x-transition class="pt-1">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Versión del Sistema Operativo
                        </label>
                        <div class="flex items-center gap-2">
                            <span class="shrink-0 px-3 py-2 rounded-lg bg-gray-100 dark:bg-gray-700
                                         text-sm font-medium text-gray-600 dark:text-gray-300"
                                  x-text="osName"></span>
                            <input type="text"
                                   x-model="osVersion"
                                   placeholder="22.04 LTS, 2022, 24.04..."
                                   class="flex-1 rounded-lg border-gray-300 dark:border-gray-600
                                          dark:bg-gray-700 dark:text-white
                                          focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>
                        <p class="mt-1.5 text-xs text-gray-400">
                            Se registrará como: <strong class="text-gray-600 dark:text-gray-300" x-text="osFullValue"></strong>
                        </p>
                    </div>

                    {{-- Nombre y versión libres — Otro --}}
                    <div x-show="osSelected === 'otro'" x-transition class="pt-1 space-y-3">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                    Nombre del Sistema Operativo
                                </label>
                                <input type="text"
                                       x-model="osName"
                                       placeholder="Debian, CentOS, FreeBSD..."
                                       class="w-full rounded-lg border-gray-300 dark:border-gray-600
                                              dark:bg-gray-700 dark:text-white
                                              focus:ring-violet-500 focus:border-violet-500 sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                    Versión
                                </label>
                                <input type="text"
                                       x-model="osVersion"
                                       placeholder="12, 9.4, 14.2..."
                                       class="w-full rounded-lg border-gray-300 dark:border-gray-600
                                              dark:bg-gray-700 dark:text-white
                                              focus:ring-violet-500 focus:border-violet-500 sm:text-sm">
                            </div>
                        </div>
                        <p class="text-xs text-gray-400">
                            Se registrará como: <strong class="text-gray-600 dark:text-gray-300" x-text="osFullValue || '(escribe el nombre)'"></strong>
                        </p>
                    </div>

                    <p x-show="osSelected === '' && !osError" class="text-xs text-amber-600 dark:text-amber-400 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Selecciona un sistema operativo para continuar
                    </p>
                    <p x-show="osError" x-transition class="text-xs text-red-600 dark:text-red-400 flex items-center gap-1.5 font-medium">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Debes seleccionar un sistema operativo antes de continuar
                    </p>

                </div>
            </div>

            {{-- Nombre y Función --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Identificación</h2>
                </div>
                <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-5">

                    <div class="sm:col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Nombre del Servidor <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="name" name="name"
                               value="{{ old('name', $server->name ?? '') }}"
                               placeholder="ADMISION, PRODUCCION..."
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600
                                      dark:bg-gray-700 dark:text-white
                                      focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                        @error('name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label for="function" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Función Principal <span class="text-red-500">*</span>
                        </label>
                        <select id="function" name="function"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600
                                       dark:bg-gray-700 dark:text-white
                                       focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            @foreach($functions as $fn)
                            <option value="{{ $fn->value }}"
                                {{ old('function', $server->function?->value ?? '') === $fn->value ? 'selected' : '' }}>
                                {{ $fn->label() }}
                            </option>
                            @endforeach
                        </select>
                        @error('function')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>

                </div>
            </div>

        </div>{{-- /paso 1 --}}

        {{-- ═══════════ PASO 2 — Tipo y Recursos ═══════════ --}}
        <div x-show="step === 2" x-transition class="space-y-5">

            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center gap-2">
                    <svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2v-4M9 21H5a2 2 0 01-2-2v-4m0 0h18"/>
                    </svg>
                    <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Tipo y Recursos</h2>
                </div>
                <div class="p-6 space-y-5">

                    <div x-data="{ hostType: '{{ old('host_type', $server->host_type ?? 'physical') }}' }">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Tipo de Servidor <span class="text-red-500">*</span>
                        </label>
                        <div class="flex gap-3">
                            @foreach([
                                'physical' => ['label' => 'Físico',       'icon' => 'M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01'],
                                'virtual'  => ['label' => 'Virtual (VM)',  'icon' => 'M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4'],
                                'cloud'    => ['label' => 'Nube',          'icon' => 'M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z'],
                            ] as $val => $opt)
                            <label @click="hostType = '{{ $val }}'"
                                   :class="hostType === '{{ $val }}'
                                       ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20'
                                       : 'border-gray-200 dark:border-gray-600 hover:border-blue-300'"
                                   class="flex-1 flex flex-col items-center gap-2 p-3 rounded-xl border-2 cursor-pointer transition-all">
                                <input type="radio" name="host_type" value="{{ $val }}" class="sr-only"
                                       :checked="hostType === '{{ $val }}'">
                                <svg :class="hostType === '{{ $val }}' ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400'"
                                     class="w-6 h-6 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $opt['icon'] }}"/>
                                </svg>
                                <span :class="hostType === '{{ $val }}' ? 'text-blue-700 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400'"
                                      class="text-xs font-medium transition-colors">{{ $opt['label'] }}</span>
                            </label>
                            @endforeach
                        </div>

                        <div x-show="hostType === 'cloud'" x-cloak
                             class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-4 mt-4 border-t border-gray-100 dark:border-gray-700">
                            <div>
                                <label for="cloud_provider" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Proveedor Cloud</label>
                                <select id="cloud_provider" name="cloud_provider"
                                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                    <option value="">Seleccionar...</option>
                                    @foreach(['aws' => 'Amazon AWS', 'gcp' => 'Google Cloud', 'azure' => 'Microsoft Azure', 'digitalocean' => 'DigitalOcean', 'linode' => 'Linode / Akamai', 'other' => 'Otro'] as $v => $lbl)
                                    <option value="{{ $v }}" {{ old('cloud_provider', $server->cloud_provider ?? '') === $v ? 'selected' : '' }}>{{ $lbl }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="cloud_region" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Región</label>
                                <input type="text" id="cloud_region" name="cloud_region"
                                       value="{{ old('cloud_region', $server->cloud_region ?? '') }}"
                                       placeholder="us-east-1"
                                       class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white font-mono focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            </div>
                            <div>
                                <label for="cloud_instance" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Tipo de Instancia</label>
                                <input type="text" id="cloud_instance" name="cloud_instance"
                                       value="{{ old('cloud_instance', $server->cloud_instance ?? '') }}"
                                       placeholder="t3.medium"
                                       class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white font-mono focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label for="cpu_cores" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Núcleos CPU</label>
                            <div class="relative">
                                <input type="number" id="cpu_cores" name="cpu_cores" min="1" max="512"
                                       value="{{ old('cpu_cores', $server->cpu_cores ?? '') }}" placeholder="4"
                                       class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500 sm:text-sm pr-14">
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400">cores</span>
                            </div>
                        </div>
                        <div>
                            <label for="ram_gb" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Memoria RAM</label>
                            <div class="relative">
                                <input type="number" id="ram_gb" name="ram_gb" min="1" max="65536"
                                       value="{{ old('ram_gb', $server->ram_gb ?? '') }}" placeholder="16"
                                       class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500 sm:text-sm pr-8">
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400">GB</span>
                            </div>
                        </div>
                        <div>
                            <label for="storage_gb" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Almacenamiento</label>
                            <div class="relative">
                                <input type="number" id="storage_gb" name="storage_gb" min="1"
                                       value="{{ old('storage_gb', $server->storage_gb ?? '') }}" placeholder="500"
                                       class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500 sm:text-sm pr-8">
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400">GB</span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="installed_services" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Servicios Instalados <span class="font-normal text-gray-400 text-xs ml-1">(separados por coma)</span>
                        </label>
                        <input type="text" id="installed_services" name="installed_services"
                               value="{{ old('installed_services', implode(', ', $server->installed_services ?? [])) }}"
                               placeholder="Docker, Nginx, Node.js, PostgreSQL..."
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>

                    <div>
                        <label for="web_root" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Ruta Web Root</label>
                        <input type="text" id="web_root" name="web_root"
                               value="{{ old('web_root', $server->web_root ?? '') }}"
                               placeholder="/var/www/html"
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white font-mono focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>

                </div>
            </div>

        </div>{{-- /paso 2 --}}

        {{-- ═══════════ PASO 3 — Acceso Remoto ═══════════ --}}
        <div x-show="step === 3" x-transition>

            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">

                {{-- Header --}}
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center gap-2">
                    <svg class="w-4 h-4 text-amber-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Acceso Remoto</h2>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold tracking-wide transition-colors"
                          :class="proto === 'rdp'
                              ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300'
                              : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300'"
                          x-text="proto.toUpperCase()"></span>
                    <span class="text-xs text-gray-400">(credenciales encriptadas)</span>
                </div>

                {{-- Selector de protocolo --}}
                <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                        Protocolo de Conexión
                        <span class="font-normal text-gray-400 ml-1">— elige cómo conectarte a este servidor</span>
                    </label>
                    <div class="grid grid-cols-2 gap-3">

                        {{-- RDP --}}
                        <div @click="setProto('rdp')"
                             :class="proto === 'rdp'
                                 ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20 ring-2 ring-blue-400 dark:ring-blue-600'
                                 : 'border-gray-200 dark:border-gray-600 hover:border-blue-300 dark:hover:border-blue-700 hover:bg-gray-50 dark:hover:bg-gray-700/50'"
                             class="relative flex items-center gap-4 p-4 rounded-xl border-2 cursor-pointer transition-all duration-200 select-none">
                            <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0 transition-colors"
                                 :class="proto === 'rdp' ? 'bg-blue-100 dark:bg-blue-900/40' : 'bg-gray-100 dark:bg-gray-700'">
                                <svg class="w-5 h-5 transition-colors"
                                     :class="proto === 'rdp' ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500'"
                                     fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <div class="text-sm font-bold transition-colors"
                                     :class="proto === 'rdp' ? 'text-blue-700 dark:text-blue-300' : 'text-gray-800 dark:text-gray-200'">
                                    RDP
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 truncate">Remote Desktop · Puerto 3389</div>
                            </div>
                            <div x-show="proto === 'rdp'" x-transition
                                 class="absolute top-2.5 right-2.5 w-4 h-4 rounded-full bg-blue-500 flex items-center justify-center shadow-sm">
                                <svg class="w-2.5 h-2.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                        </div>

                        {{-- SSH --}}
                        <div @click="setProto('ssh')"
                             :class="proto === 'ssh'
                                 ? 'border-emerald-500 bg-emerald-50 dark:bg-emerald-900/20 ring-2 ring-emerald-400 dark:ring-emerald-600'
                                 : 'border-gray-200 dark:border-gray-600 hover:border-emerald-300 dark:hover:border-emerald-700 hover:bg-gray-50 dark:hover:bg-gray-700/50'"
                             class="relative flex items-center gap-4 p-4 rounded-xl border-2 cursor-pointer transition-all duration-200 select-none">
                            <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0 transition-colors"
                                 :class="proto === 'ssh' ? 'bg-emerald-100 dark:bg-emerald-900/40' : 'bg-gray-100 dark:bg-gray-700'">
                                <svg class="w-5 h-5 transition-colors"
                                     :class="proto === 'ssh' ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-400 dark:text-gray-500'"
                                     fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="M6.75 7.5l3 2.25-3 2.25m4.5 0h3m-9 8.25h13.5A2.25 2.25 0 0021 18V6a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 6v12a2.25 2.25 0 002.25 2.25z"/>
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <div class="text-sm font-bold transition-colors"
                                     :class="proto === 'ssh' ? 'text-emerald-700 dark:text-emerald-300' : 'text-gray-800 dark:text-gray-200'">
                                    SSH
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 truncate">Secure Shell · Puerto 22</div>
                            </div>
                            <div x-show="proto === 'ssh'" x-transition
                                 class="absolute top-2.5 right-2.5 w-4 h-4 rounded-full bg-emerald-500 flex items-center justify-center shadow-sm">
                                <svg class="w-2.5 h-2.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                        </div>

                    </div>
                    <p class="mt-2 text-xs text-gray-400 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Se sugiere automáticamente según el sistema operativo elegido, pero puedes cambiarlo.
                    </p>
                </div>

                {{-- Credenciales --}}
                <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-5">

                    <div>
                        <label for="ssh_user" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            <span x-text="proto === 'rdp' ? 'Usuario Windows' : 'Usuario SSH'"></span>
                        </label>
                        <input type="text" id="ssh_user" name="ssh_user"
                               value="{{ old('ssh_user', $server->ssh_user ?? '') }}"
                               :placeholder="proto === 'rdp' ? 'Administrator' : 'root'"
                               autocomplete="off"
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white font-mono focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>

                    <div>
                        <label for="ssh_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Contraseña
                            @if(isset($server->id))
                            <span class="font-normal text-gray-400 text-xs ml-1">(dejar vacío para no cambiar)</span>
                            @endif
                        </label>
                        <input type="password" id="ssh_password" name="ssh_password"
                               autocomplete="new-password"
                               placeholder="{{ isset($server->id) ? '••••••••' : 'Contraseña' }}"
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>

                    <div>
                        <label for="rdp_port" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Puerto <span x-text="proto.toUpperCase()"></span>
                        </label>
                        <div class="relative">
                            <input type="number" id="rdp_port" name="rdp_port" min="1" max="65535"
                                   value="{{ old('rdp_port', $server->rdp_port ?? $defaultPort) }}"
                                   :placeholder="proto === 'rdp' ? 3389 : 22"
                                   @input="portTouched = true"
                                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white font-mono focus:ring-blue-500 focus:border-blue-500 sm:text-sm pr-16">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-mono font-semibold transition-colors"
                                  :class="proto === 'rdp' ? 'text-blue-400' : 'text-emerald-400'"
                                  x-text="proto.toUpperCase()"></span>
                        </div>
                        <p class="mt-1 text-xs text-gray-400"
                           x-text="proto === 'rdp' ? 'Puerto RDP por defecto: 3389' : 'Puerto SSH por defecto: 22'"></p>
                    </div>

                    <div class="flex items-end pb-1">
                        <div class="inline-flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-colors"
                             :class="proto === 'rdp'
                                 ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300'
                                 : 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300'">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            Protocolo <strong class="ml-1" x-text="proto.toUpperCase()"></strong>&nbsp;configurado
                        </div>
                    </div>

                </div>
            </div>

        </div>{{-- /paso 3 --}}

        {{-- ═══════════ PASO 4 — Red y Extras ═══════════ --}}
        <div x-show="step === 4" x-transition class="space-y-5">

            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Direcciones IP</h2>
                    <button type="button" onclick="addIpRow()"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium
                                   text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/30
                                   rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Agregar IP
                    </button>
                </div>
                <div class="p-6">
                    <div id="ip-rows" class="space-y-3">
                        @php $existingIps = old('ips', $server->ips?->toArray() ?? []); @endphp
                        @forelse($existingIps as $i => $ip)
                        <div class="ip-row flex flex-wrap sm:flex-nowrap items-center gap-2">
                            <input type="text" name="ips[{{ $i }}][ip_address]"
                                   value="{{ $ip['ip_address'] ?? '' }}" placeholder="192.168.x.x"
                                   class="flex-1 min-w-0 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white font-mono focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <select name="ips[{{ $i }}][type]"
                                    class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="private" {{ ($ip['type'] ?? 'private') === 'private' ? 'selected' : '' }}>Privada</option>
                                <option value="public"  {{ ($ip['type'] ?? '') === 'public'  ? 'selected' : '' }}>Pública</option>
                            </select>
                            <input type="text" name="ips[{{ $i }}][interface]"
                                   value="{{ $ip['interface'] ?? '' }}" placeholder="eth0"
                                   class="w-20 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white font-mono focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <label class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap cursor-pointer">
                                <input type="checkbox" name="ips[{{ $i }}][is_primary]" value="1"
                                       {{ !empty($ip['is_primary']) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                Principal
                            </label>
                            <button type="button" onclick="this.closest('.ip-row').remove()"
                                    class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/30 transition-all">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        @empty
                        <p class="text-sm text-gray-400 dark:text-gray-500 text-center py-4">
                            Sin IPs registradas. Haz clic en "Agregar IP".
                        </p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Estado y Notas</h2>
                </div>
                <div class="p-6 space-y-5">

                    <div class="flex items-center gap-3">
                        <button type="button" id="toggle-active" role="switch" onclick="toggleActive()"
                                class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors
                                       {{ old('is_active', $server->is_active ?? true) ? 'bg-emerald-500' : 'bg-gray-300 dark:bg-gray-600' }}">
                            <span id="toggle-thumb"
                                  class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform
                                         {{ old('is_active', $server->is_active ?? true) ? 'translate-x-6' : 'translate-x-1' }}">
                            </span>
                        </button>
                        <input type="hidden" name="is_active" id="is_active"
                               value="{{ old('is_active', $server->is_active ?? true) ? '1' : '0' }}">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300 cursor-pointer" onclick="toggleActive()">
                            Servidor activo
                        </label>
                    </div>

                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Notas</label>
                        <textarea id="notes" name="notes" rows="3"
                                  placeholder="Observaciones adicionales..."
                                  class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500 sm:text-sm">{{ old('notes', $server->notes ?? '') }}</textarea>
                    </div>

                </div>
            </div>

        </div>{{-- /paso 4 --}}

        {{-- ── Navegación ── --}}
        <div class="flex items-center justify-between pt-2">

            <button type="button" @click="goPrev()" x-show="step > 1"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium
                           text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700
                           border border-gray-300 dark:border-gray-600 rounded-lg
                           hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Anterior
            </button>
            <div x-show="step === 1"></div>

            <div class="flex items-center gap-3">
                <a x-show="step === 1" href="{{ route('admin.servers.index') }}"
                   class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300
                          bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600
                          rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                    Cancelar
                </a>

                <button type="button" @click="goNext()" x-show="step < 4"
                        class="inline-flex items-center gap-2 px-5 py-2 text-sm font-medium
                               text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                    Siguiente
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>

                <button type="button" x-show="step === 4" @click="submitForm()"
                        :disabled="submitting"
                        :class="submitting ? 'opacity-70 cursor-not-allowed' : 'hover:bg-blue-700'"
                        class="inline-flex items-center gap-2 px-5 py-2 text-sm font-medium
                               text-white bg-blue-600 rounded-lg transition-colors shadow-sm">
                    <template x-if="submitting">
                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 14 6.477 14 12h-4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </template>
                    <template x-if="!submitting">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </template>
                    <span x-text="submitting ? 'Guardando...' : '{{ isset($server->id) ? 'Actualizar' : 'Registrar Servidor' }}'"></span>
                </button>
            </div>

        </div>

    </form>
</div>
@endsection

@push('scripts')
<script>
// ── Filas de IP ──────────────────────────────────────────────────────
let ipIndex = {{ count(old('ips', $server->ips?->toArray() ?? [])) }};

function addIpRow() {
    const container = document.getElementById('ip-rows');
    const empty = container.querySelector('p');
    if (empty) empty.remove();

    const div = document.createElement('div');
    div.className = 'ip-row flex flex-wrap sm:flex-nowrap items-center gap-2';
    div.innerHTML = `
        <input type="text" name="ips[${ipIndex}][ip_address]" placeholder="192.168.x.x"
               class="flex-1 min-w-0 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                      dark:text-white font-mono focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
        <select name="ips[${ipIndex}][type]"
                class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                       dark:text-white focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            <option value="private">Privada</option>
            <option value="public">Pública</option>
        </select>
        <input type="text" name="ips[${ipIndex}][interface]" placeholder="eth0"
               class="w-20 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                      dark:text-white font-mono focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
        <label class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap cursor-pointer">
            <input type="checkbox" name="ips[${ipIndex}][is_primary]" value="1"
                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
            Principal
        </label>
        <button type="button" onclick="this.closest('.ip-row').remove()"
                class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-lg
                       text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/30 transition-all">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>`;
    container.appendChild(div);
    div.querySelector('input[type=text]').focus();
    ipIndex++;
}

// ── Toggle activo ────────────────────────────────────────────────────
function toggleActive() {
    const btn    = document.getElementById('toggle-active');
    const thumb  = document.getElementById('toggle-thumb');
    const input  = document.getElementById('is_active');
    const active = input.value === '1';

    input.value = active ? '0' : '1';
    btn.classList.toggle('bg-emerald-500',     !active);
    btn.classList.toggle('bg-gray-300',         active);
    btn.classList.toggle('dark:bg-gray-600',    active);
    thumb.classList.toggle('translate-x-6',    !active);
    thumb.classList.toggle('translate-x-1',     active);
}
</script>
@endpush
