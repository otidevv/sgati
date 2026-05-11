@extends('layouts.app')
@section('title', 'Dar de baja — ' . ($system->acronym ?? $system->name))

@section('content')
<div class="max-w-3xl mx-auto">

    {{-- Header --}}
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('systems.show', $system) }}"
           class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors flex-shrink-0">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div class="min-w-0">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Dar de baja el sistema</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 truncate">{{ $system->name }}</p>
        </div>
        <x-status-badge :status="$system->status" class="ml-auto flex-shrink-0" />
    </div>

    {{-- Aviso --}}
    <div class="mb-5 flex gap-3 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl">
        <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <p class="text-sm text-red-700 dark:text-red-300">
            Esta acción registra la baja definitiva del sistema y cambia su estado a <strong>Dado de baja</strong>.
            El registro queda guardado con fines de auditoría y no puede deshacerse desde esta pantalla.
        </p>
    </div>

    <form action="{{ route('systems.decommission.store', $system) }}" method="POST" class="space-y-5">
    @csrf

        {{-- Grupo 1: Motivo --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/30">
                <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Motivo de la baja</h2>
            </div>
            <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Tipo de motivo <span class="text-red-500">*</span>
                    </label>
                    <select name="reason_type" required
                            class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                        <option value="">— Seleccionar —</option>
                        <option value="obsolete"         {{ old('reason_type') === 'obsolete'         ? 'selected' : '' }}>Tecnología obsoleta</option>
                        <option value="replaced"         {{ old('reason_type') === 'replaced'         ? 'selected' : '' }}>Reemplazado por otro sistema</option>
                        <option value="contract_expired" {{ old('reason_type') === 'contract_expired' ? 'selected' : '' }}>Contrato/licencia vencida</option>
                        <option value="budget"           {{ old('reason_type') === 'budget'           ? 'selected' : '' }}>Recorte presupuestal</option>
                        <option value="merge"            {{ old('reason_type') === 'merge'            ? 'selected' : '' }}>Fusión con otro sistema</option>
                        <option value="directive"        {{ old('reason_type') === 'directive'        ? 'selected' : '' }}>Directiva institucional</option>
                        <option value="other"            {{ old('reason_type') === 'other'            ? 'selected' : '' }}>Otro motivo</option>
                    </select>
                    @error('reason_type')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Fecha efectiva de baja <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="effective_date" required value="{{ old('effective_date', date('Y-m-d')) }}"
                           class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                    @error('effective_date')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Descripción detallada del motivo <span class="text-red-500">*</span>
                    </label>
                    <textarea name="reason" rows="4" required maxlength="2000"
                              placeholder="Explique con detalle el motivo de la baja..."
                              class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">{{ old('reason') }}</textarea>
                    @error('reason')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fecha de apagado de servidores</label>
                    <input type="date" name="shutdown_date" value="{{ old('shutdown_date') }}"
                           class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                    @error('shutdown_date')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Usuarios afectados (estimado)</label>
                    <input type="number" name="users_affected" min="0" value="{{ old('users_affected') }}"
                           class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                    @error('users_affected')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- Grupo 2: Sustento documental --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/30">
                <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Sustento documental</h2>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Resolución o memorando que respalda la baja</p>
            </div>
            <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">N° Resolución / Acto administrativo</label>
                    <input type="text" name="resolution_number" maxlength="100" value="{{ old('resolution_number') }}"
                           placeholder="Ej: R-OTI-2026-012"
                           class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fecha de la resolución</label>
                    <input type="date" name="resolution_date" value="{{ old('resolution_date') }}"
                           class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Entidad que emite la resolución</label>
                    <input type="text" name="resolution_entity" maxlength="200" value="{{ old('resolution_entity') }}"
                           placeholder="Ej: Oficina de Tecnologías de la Información"
                           class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">N° Memorando / comunicación interna</label>
                    <input type="text" name="memo_number" maxlength="100" value="{{ old('memo_number') }}"
                           placeholder="Ej: MEMO-OTI-2026-045"
                           class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fecha del memorando</label>
                    <input type="date" name="memo_date" value="{{ old('memo_date') }}"
                           class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                </div>
            </div>
        </div>

        {{-- Grupo 3: Autorización --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/30">
                <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Autorización</h2>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Quién aprobó la baja del sistema</p>
            </div>
            <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nombre del que autoriza</label>
                    <input type="text" name="authorized_by_name" maxlength="200" value="{{ old('authorized_by_name') }}"
                           placeholder="Nombre completo"
                           class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cargo</label>
                    <input type="text" name="authorized_by_position" maxlength="200" value="{{ old('authorized_by_position') }}"
                           placeholder="Ej: Jefe de la OTI"
                           class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                </div>
            </div>
        </div>

        {{-- Grupo 4: Sistema sucesor y migración --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/30">
                <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Sistema sucesor y migración de datos</h2>
            </div>
            <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Sistema sucesor (si aplica)</label>
                    <select name="successor_system_id"
                            class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                        <option value="">— Ninguno —</option>
                        @foreach($allSystems as $s)
                        <option value="{{ $s->id }}" {{ old('successor_system_id') == $s->id ? 'selected' : '' }}>
                            {{ $s->acronym ? "[{$s->acronym}] {$s->name}" : $s->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Descripción del sucesor (si es externo)</label>
                    <input type="text" name="successor_description" maxlength="500" value="{{ old('successor_description') }}"
                           placeholder="Si el sucesor no está registrado en el sistema..."
                           class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                </div>
                <div class="sm:col-span-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="hidden" name="data_migrated" value="0">
                        <input type="checkbox" name="data_migrated" value="1"
                               {{ old('data_migrated') ? 'checked' : '' }}
                               class="w-4 h-4 rounded border-gray-300 text-red-600 focus:ring-red-400">
                        <span class="text-sm text-gray-700 dark:text-gray-300">La información del sistema fue migrada a otro destino</span>
                    </label>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Destino de la data migrada</label>
                    <input type="text" name="data_migration_destination" maxlength="500" value="{{ old('data_migration_destination') }}"
                           placeholder="Ej: Migrada al sistema X / Archivada en servidor de respaldo"
                           class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Conservar datos hasta</label>
                    <input type="date" name="data_retention_until" value="{{ old('data_retention_until') }}"
                           class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                </div>
            </div>
        </div>

        {{-- Grupo 5: Notas de auditoría --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/30">
                <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Notas de auditoría</h2>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Notas adicionales</label>
                    <textarea name="audit_notes" rows="3"
                              placeholder="Cualquier información relevante que deba quedar registrada para auditoría..."
                              class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">{{ old('audit_notes') }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Procedimiento de reactivación (si aplica)</label>
                    <textarea name="reactivation_procedure" rows="3"
                              placeholder="Pasos a seguir si se necesita reactivar el sistema en el futuro..."
                              class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">{{ old('reactivation_procedure') }}</textarea>
                </div>
            </div>
        </div>

        {{-- Acciones --}}
        <div class="flex items-center justify-between">
            <a href="{{ route('systems.show', $system) }}"
               class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 transition-colors">
                Cancelar
            </a>
            <button type="submit"
                    class="inline-flex items-center gap-2 px-6 py-2.5 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                </svg>
                Confirmar baja del sistema
            </button>
        </div>

    </form>
</div>
@endsection
