@extends('layouts.app')
@section('title', 'Nueva Base de Datos')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('systems.show', $system) }}"
           class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Nueva Base de Datos</h1>
            <p class="mt-1 text-sm text-gray-500">{{ $system->name }}</p>
        </div>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <form action="{{ route('systems.databases.store', $system) }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="db_name" class="block text-sm font-medium text-gray-700 mb-1">Nombre BD <span class="text-red-500">*</span></label>
                    <input type="text" id="db_name" name="db_name" value="{{ old('db_name') }}" required
                           class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm font-mono"
                           placeholder="db_sgati_prod">
                    @error('db_name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="engine" class="block text-sm font-medium text-gray-700 mb-1">Motor <span class="text-red-500">*</span></label>
                    <select id="engine" name="engine" required
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="">Seleccionar…</option>
                        @foreach(['postgresql'=>'PostgreSQL','mysql'=>'MySQL','mariadb'=>'MariaDB','oracle'=>'Oracle','sqlserver'=>'SQL Server','sqlite'=>'SQLite','mongodb'=>'MongoDB','other'=>'Otro'] as $v => $l)
                        <option value="{{ $v }}" {{ old('engine') === $v ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                    @error('engine')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="sm:col-span-2">
                    <label for="database_server_id" class="block text-sm font-medium text-gray-700 mb-1">Motor de BD (servidor)</label>
                    <select id="database_server_id" name="database_server_id"
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="">— Sin motor asignado —</option>
                        @foreach($databaseServers as $ds)
                        <option value="{{ $ds->id }}" {{ old('database_server_id') == $ds->id ? 'selected' : '' }}>
                            {{ $ds->name ?: strtoupper($ds->engine) }}{{ $ds->version ? ' ' . $ds->version : '' }}{{ $ds->host ? ' — ' . $ds->host : '' }}
                        </option>
                        @endforeach
                    </select>
                    @error('database_server_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="db_user" class="block text-sm font-medium text-gray-700 mb-1">Usuario BD</label>
                    <input type="text" id="db_user" name="db_user" value="{{ old('db_user') }}"
                           class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm font-mono"
                           placeholder="usuario_bd">
                </div>
                <div>
                    <label for="db_password" class="block text-sm font-medium text-gray-700 mb-1">Contraseña BD</label>
                    <input type="password" id="db_password" name="db_password" autocomplete="new-password"
                           class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>
                <div>
                    <label for="schema_name" class="block text-sm font-medium text-gray-700 mb-1">Schema</label>
                    <input type="text" id="schema_name" name="schema_name" value="{{ old('schema_name') }}"
                           class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm font-mono"
                           placeholder="public">
                </div>
                <div>
                    <label for="responsible" class="block text-sm font-medium text-gray-700 mb-1">Responsable</label>
                    <input type="text" id="responsible" name="responsible" value="{{ old('responsible') }}"
                           class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                           placeholder="Nombre del DBA">
                </div>
                <div class="sm:col-span-2">
                    <label for="environment" class="block text-sm font-medium text-gray-700 mb-1">Ambiente <span class="text-red-500">*</span></label>
                    <select id="environment" name="environment" required
                            class="block w-full sm:w-48 rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="production"  {{ old('environment') === 'production'  ? 'selected' : '' }}>Producción</option>
                        <option value="staging"     {{ old('environment') === 'staging'     ? 'selected' : '' }}>Staging</option>
                        <option value="development" {{ old('environment', 'development') === 'development' ? 'selected' : '' }}>Desarrollo</option>
                    </select>
                </div>
            </div>
            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notas</label>
                <textarea id="notes" name="notes" rows="2"
                          class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">{{ old('notes') }}</textarea>
            </div>
            <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-100">
                <a href="{{ route('systems.show', $system) }}"
                   class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">Cancelar</a>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Registrar BD
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
