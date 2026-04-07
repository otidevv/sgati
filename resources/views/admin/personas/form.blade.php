@extends('layouts.app')

@section('title', isset($persona->id) ? 'Editar Persona' : 'Nueva Persona')

@section('content')
<div class="max-w-3xl mx-auto space-y-6"
     x-data="dniForm({{ json_encode(route('admin.personas.dni-lookup', ':dni')) }})">

    {{-- Header --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.personas.index') }}"
           class="flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                {{ isset($persona->id) ? 'Editar Persona' : 'Nueva Persona' }}
            </h1>
            <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">
                {{ isset($persona->id) ? 'Actualizar datos civiles' : 'Registrar datos civiles del personal' }}
            </p>
        </div>
    </div>

    <form action="{{ isset($persona->id) ? route('admin.personas.update', $persona) : route('admin.personas.store') }}"
          method="POST" class="space-y-4">
        @csrf
        @if(isset($persona->id)) @method('PUT') @endif

        {{-- ── Sección: Identificación ── --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-5 py-3.5 border-b border-gray-100 dark:border-gray-700 flex items-center gap-2.5">
                <div class="w-6 h-6 rounded-md bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center flex-shrink-0">
                    <svg class="w-3.5 h-3.5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2"/>
                    </svg>
                </div>
                <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200">Identificación</h3>
            </div>
            <div class="p-5">

                {{-- DNI con buscador --}}
                <div class="mb-4">
                    <label for="dni" class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">
                        DNI <span class="text-red-500">*</span>
                    </label>
                    <div class="flex gap-2">
                        <div class="relative flex-1">
                            <input type="text" id="dni" name="dni" maxlength="8"
                                   value="{{ old('dni', $persona->dni ?? '') }}"
                                   x-model="dni"
                                   @keydown.enter.prevent="buscar"
                                   @input="resetStatus"
                                   placeholder="Ej: 12345678"
                                   inputmode="numeric"
                                   class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm px-3 py-2 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                                   :class="status === 'found' ? 'border-emerald-400 dark:border-emerald-500 focus:ring-emerald-500' : status === 'error' ? 'border-red-400 dark:border-red-500 focus:ring-red-500' : ''"
                                   required>
                            {{-- Ícono de estado --}}
                            <div class="absolute inset-y-0 right-2 flex items-center pointer-events-none">
                                <svg x-show="status === 'found'" class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                                <svg x-show="status === 'error'" class="w-4 h-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                                </svg>
                            </div>
                        </div>
                        <button type="button" @click="buscar"
                                :disabled="loading || dni.length !== 8"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 dark:focus:ring-offset-gray-800">
                            <svg x-show="!loading" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <svg x-show="loading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                            <span x-text="loading ? 'Buscando…' : 'Autocompletar'"></span>
                        </button>
                    </div>

                    {{-- Mensajes de estado --}}
                    <p x-show="status === 'found'" class="mt-1.5 text-xs text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Datos completados desde RENIEC
                    </p>
                    <p x-show="status === 'error'" class="mt-1.5 text-xs text-red-600 dark:text-red-400" x-text="errorMsg"></p>
                    @error('dni')<p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                </div>

                {{-- Sexo + Fecha de nacimiento --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="sexo" class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Sexo</label>
                        <select id="sexo" name="sexo" x-model="form.sexo"
                                class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                            <option value="">Seleccionar</option>
                            <option value="M" {{ old('sexo', $persona->sexo ?? '') === 'M' ? 'selected' : '' }}>Masculino</option>
                            <option value="F" {{ old('sexo', $persona->sexo ?? '') === 'F' ? 'selected' : '' }}>Femenino</option>
                        </select>
                    </div>
                    <div>
                        <label for="fecha_nacimiento" class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Fecha de Nacimiento</label>
                        <input type="date" id="fecha_nacimiento" name="fecha_nacimiento"
                               x-model="form.fecha_nacimiento"
                               value="{{ old('fecha_nacimiento', isset($persona->fecha_nacimiento) ? $persona->fecha_nacimiento->format('Y-m-d') : '') }}"
                               max="{{ now()->subDay()->format('Y-m-d') }}"
                               class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Sección: Nombres ── --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-5 py-3.5 border-b border-gray-100 dark:border-gray-700 flex items-center gap-2.5">
                <div class="w-6 h-6 rounded-md bg-indigo-100 dark:bg-indigo-900/40 flex items-center justify-center flex-shrink-0">
                    <svg class="w-3.5 h-3.5 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200">Nombres y Apellidos</h3>
            </div>
            <div class="p-5 space-y-4">
                <div>
                    <label for="nombres" class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">
                        Nombres <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="nombres" name="nombres"
                           x-model="form.nombres"
                           value="{{ old('nombres', $persona->nombres ?? '') }}"
                           placeholder="Ej: Juan Carlos"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm px-3 py-2 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                           required>
                    @error('nombres')<p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="apellido_paterno" class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">
                            Ap. Paterno <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="apellido_paterno" name="apellido_paterno"
                               x-model="form.apellido_paterno"
                               value="{{ old('apellido_paterno', $persona->apellido_paterno ?? '') }}"
                               placeholder="Ej: García"
                               class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm px-3 py-2 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                               required>
                        @error('apellido_paterno')<p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="apellido_materno" class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">
                            Ap. Materno <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="apellido_materno" name="apellido_materno"
                               x-model="form.apellido_materno"
                               value="{{ old('apellido_materno', $persona->apellido_materno ?? '') }}"
                               placeholder="Ej: López"
                               class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm px-3 py-2 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                               required>
                        @error('apellido_materno')<p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Preview nombre completo --}}
                <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700/60 rounded-lg border border-gray-200 dark:border-gray-600">
                    <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <div class="min-w-0">
                        <p class="text-[10px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Nombre completo</p>
                        <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 truncate" x-text="nombreCompleto"></p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Sección: Contacto ── --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-5 py-3.5 border-b border-gray-100 dark:border-gray-700 flex items-center gap-2.5">
                <div class="w-6 h-6 rounded-md bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center flex-shrink-0">
                    <svg class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                </div>
                <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200">Contacto <span class="text-xs font-normal text-gray-400 dark:text-gray-500 ml-1">(opcional)</span></h3>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="telefono" class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Teléfono</label>
                        <input type="text" id="telefono" name="telefono"
                               value="{{ old('telefono', $persona->telefono ?? '') }}"
                               placeholder="Ej: 987654321"
                               inputmode="numeric"
                               class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm px-3 py-2 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                    </div>
                    <div>
                        <label for="email_personal" class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Email Personal</label>
                        <input type="email" id="email_personal" name="email_personal"
                               value="{{ old('email_personal', $persona->email_personal ?? '') }}"
                               placeholder="Ej: juan@gmail.com"
                               class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm px-3 py-2 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                        @error('email_personal')<p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Acciones --}}
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.personas.index') }}"
               class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                Cancelar
            </a>
            <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 dark:focus:ring-offset-gray-900">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                {{ isset($persona->id) ? 'Actualizar' : 'Registrar' }}
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
function dniForm(lookupUrl) {
    return {
        dni: '{{ old('dni', $persona->dni ?? '') }}',
        loading: false,
        status: null,   // null | 'found' | 'error'
        errorMsg: '',
        form: {
            nombres:          '{{ old('nombres', $persona->nombres ?? '') }}',
            apellido_paterno: '{{ old('apellido_paterno', $persona->apellido_paterno ?? '') }}',
            apellido_materno: '{{ old('apellido_materno', $persona->apellido_materno ?? '') }}',
            fecha_nacimiento: '{{ old('fecha_nacimiento', isset($persona->fecha_nacimiento) ? $persona->fecha_nacimiento->format('Y-m-d') : '') }}',
            sexo:             '{{ old('sexo', $persona->sexo ?? '') }}',
        },

        get nombreCompleto() {
            const p = this.form.apellido_paterno.trim();
            const m = this.form.apellido_materno.trim();
            const n = this.form.nombres.trim();
            if (!p && !m && !n) return '—';
            return `${p} ${m}, ${n}`.replace(/\s+/g, ' ').replace(/,\s*$/, '').trim();
        },

        resetStatus() {
            this.status = null;
            this.errorMsg = '';
        },

        async buscar() {
            if (this.loading || this.dni.length !== 8) return;

            this.loading  = true;
            this.status   = null;
            this.errorMsg = '';

            try {
                const url = lookupUrl.replace(':dni', encodeURIComponent(this.dni));
                const res = await fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();

                if (!res.ok) {
                    this.status   = 'error';
                    this.errorMsg = data.error ?? 'No se encontraron datos.';
                    return;
                }

                this.form.nombres          = data.nombres          ?? this.form.nombres;
                this.form.apellido_paterno = data.apellido_paterno ?? this.form.apellido_paterno;
                this.form.apellido_materno = data.apellido_materno ?? this.form.apellido_materno;
                this.form.fecha_nacimiento = data.fecha_nacimiento ?? this.form.fecha_nacimiento;
                this.form.sexo             = data.sexo             ?? this.form.sexo;

                this.status = 'found';

            } catch {
                this.status   = 'error';
                this.errorMsg = 'Error de conexión al consultar el DNI.';
            } finally {
                this.loading = false;
            }
        },
    };
}
</script>
@endpush
@endsection
