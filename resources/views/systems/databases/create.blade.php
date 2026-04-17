@extends('layouts.app')
@section('title', 'Nueva Base de Datos — ' . $system->name)

@section('content')
@php
    $engines = [
        'postgresql' => 'PostgreSQL',
        'mysql'      => 'MySQL',
        'mariadb'    => 'MariaDB',
        'oracle'     => 'Oracle',
        'sqlserver'  => 'SQL Server',
        'sqlite'     => 'SQLite',
        'mongodb'    => 'MongoDB',
        'other'      => 'Otro',
    ];
@endphp

<div class="max-w-2xl mx-auto space-y-6">

    {{-- Encabezado --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('systems.show', $system) }}"
           class="inline-flex items-center justify-center w-10 h-10 rounded-lg
                  bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300
                  hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Nueva Base de Datos</h1>
            <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">{{ $system->name }}</p>
        </div>
    </div>

    {{-- Formulario --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
        <form action="{{ route('systems.databases.store', $system) }}" method="POST"
              x-data="{ submitting: false }" @submit="submitting = true">
            @csrf

            {{-- Sección: Identificación --}}
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Identificación</h3>
            </div>
            <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-5">

                {{-- Nombre BD --}}
                <div>
                    <label for="db_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Nombre de la BD <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="db_name" name="db_name" value="{{ old('db_name') }}" required
                           placeholder="db_sgati_prod" maxlength="100"
                           class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                  dark:text-white font-mono shadow-sm
                                  focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    @error('db_name')
                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Ambiente --}}
                <div>
                    <label for="environment" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Ambiente <span class="text-red-500">*</span>
                    </label>
                    <select id="environment" name="environment" required
                            class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                   dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="production"  {{ old('environment') === 'production'  ? 'selected' : '' }}>Producción</option>
                        <option value="staging"     {{ old('environment') === 'staging'     ? 'selected' : '' }}>Staging</option>
                        <option value="development" {{ old('environment', 'development') === 'development' ? 'selected' : '' }}>Desarrollo</option>
                    </select>
                    @error('environment')
                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Schema --}}
                <div>
                    <label for="schema_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Schema</label>
                    <input type="text" id="schema_name" name="schema_name" value="{{ old('schema_name') }}"
                           placeholder="public" maxlength="100"
                           class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                  dark:text-white font-mono shadow-sm
                                  focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>

            </div>

            {{-- Sección: Motor / Servidor --}}
            <div class="px-6 py-4 border-t border-b border-gray-100 dark:border-gray-700">
                <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Motor de base de datos</h3>
            </div>
            <div class="p-6 space-y-5">

                {{-- Servidor registrado --}}
                <div>
                    <label for="database_server_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Gestor registrado en el sistema
                    </label>
                    <select id="database_server_id" name="database_server_id"
                            class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                   dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="">— Sin gestor asignado —</option>
                        @foreach($databaseServers as $ds)
                        <option value="{{ $ds->id }}"
                                data-engine="{{ $ds->engine }}"
                                data-label="{{ $ds->name ?: strtoupper($ds->engine) }}{{ $ds->version ? ' ' . $ds->version : '' }}"
                                data-host="{{ $ds->host ?? '' }}"
                                {{ old('database_server_id') == $ds->id ? 'selected' : '' }}>
                            {{ $ds->name ?: strtoupper($ds->engine) }}{{ $ds->version ? ' ' . $ds->version : '' }}{{ $ds->host ? ' — ' . $ds->host : '' }}
                        </option>
                        @endforeach
                    </select>
                    @error('database_server_id')
                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror

                    {{-- Hint del servidor seleccionado --}}
                    <div id="server-hint" class="hidden mt-2 items-center gap-2 px-3 py-2 rounded-lg
                                                  bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700/40 text-xs">
                        <svg class="w-3.5 h-3.5 text-blue-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2"/>
                        </svg>
                        <span id="server-hint-text" class="text-blue-700 dark:text-blue-300"></span>
                    </div>
                </div>

                {{-- Motor (se bloquea si se elige gestor) --}}
                <div>
                    <label for="engine" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Motor <span class="text-red-500">*</span>
                        <span id="engine-lock-badge"
                              class="hidden ml-1 text-[10px] font-normal text-blue-600 dark:text-blue-400
                                     bg-blue-50 dark:bg-blue-900/30 px-1.5 py-0.5 rounded-full">
                            determinado por el gestor
                        </span>
                    </label>
                    <select id="engine" name="engine" required
                            class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                   dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="">Seleccionar…</option>
                        @foreach($engines as $v => $l)
                        <option value="{{ $v }}" {{ old('engine') === $v ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                    @error('engine')
                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Sección: Credenciales --}}
            <div class="px-6 py-4 border-t border-b border-gray-100 dark:border-gray-700">
                <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Credenciales de acceso <span class="font-normal normal-case text-gray-400">(opcional)</span></h3>
            </div>
            <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-5">

                <div>
                    <label for="db_user" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Usuario BD</label>
                    <input type="text" id="db_user" name="db_user" value="{{ old('db_user') }}"
                           placeholder="usuario_bd" maxlength="100"
                           class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                  dark:text-white font-mono shadow-sm
                                  focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>

                <div>
                    <label for="db_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Contraseña BD</label>
                    <input type="password" id="db_password" name="db_password" autocomplete="new-password"
                           class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                  dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>
            </div>

            {{-- Notas --}}
            <div class="px-6 pb-6 border-t border-gray-100 dark:border-gray-700 pt-5">
                <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Notas</label>
                <textarea id="notes" name="notes" rows="2"
                          placeholder="Observaciones adicionales..." maxlength="2000"
                          class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm resize-none">{{ old('notes') }}</textarea>
            </div>

            {{-- Acciones --}}
            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                <a href="{{ route('systems.show', $system) }}"
                   class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700
                          border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                    Cancelar
                </a>
                <button type="submit"
                        :disabled="submitting"
                        class="inline-flex items-center gap-2 px-5 py-2 text-sm font-medium text-white
                               bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors shadow-sm
                               disabled:opacity-60 disabled:cursor-not-allowed">
                    <svg x-show="!submitting" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <svg x-show="submitting" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                    </svg>
                    <span x-text="submitting ? 'Registrando…' : 'Registrar BD'"></span>
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
(function () {
    const serverSelect = document.getElementById('database_server_id');
    const engineSelect = document.getElementById('engine');
    const hint         = document.getElementById('server-hint');
    const hintText     = document.getElementById('server-hint-text');
    const lockBadge    = document.getElementById('engine-lock-badge');

    function syncEngine() {
        const opt    = serverSelect.options[serverSelect.selectedIndex];
        const engine = opt?.dataset?.engine ?? '';
        const label  = opt?.dataset?.label  ?? '';
        const host   = opt?.dataset?.host   ?? '';

        if (engine) {
            // Sincronizar valor y bloquear visualmente (sin usar disabled para que se envíe el valor)
            engineSelect.value = engine;
            engineSelect.classList.add('opacity-60', 'cursor-not-allowed', 'pointer-events-none');
            lockBadge.classList.remove('hidden');

            // Mostrar hint
            hintText.textContent = label + (host ? '  ·  ' + host : '');
            hint.classList.remove('hidden');
            hint.classList.add('flex');
        } else {
            engineSelect.classList.remove('opacity-60', 'cursor-not-allowed', 'pointer-events-none');
            lockBadge.classList.add('hidden');
            hint.classList.add('hidden');
            hint.classList.remove('flex');
        }
    }

    serverSelect.addEventListener('change', syncEngine);

    // Ejecutar al cargar si ya hay un valor pre-seleccionado (old() / error de validación)
    syncEngine();
})();
</script>
@endpush
@endsection
