@extends('layouts.app')
@section('title', 'Infraestructura — ' . $system->name)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('systems.show', $system) }}"
           class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Infraestructura</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $system->name }}</p>
        </div>
    </div>

    <form action="{{ route('systems.infrastructure.update', $system) }}" method="POST" class="space-y-5">
        @csrf @method('PUT')

        {{-- Servidor --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/30">
                <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Servidor</h2>
            </div>
            <div class="p-6">
                <label for="server_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Servidor asignado</label>
                <select id="server_id" name="server_id"
                        class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <option value="">— Sin servidor —</option>
                    @foreach($servers as $srv)
                    <option value="{{ $srv->id }}" {{ old('server_id', $infra->server_id) == $srv->id ? 'selected' : '' }}>
                        {{ $srv->name }}{{ $srv->operating_system ? ' (' . $srv->operating_system . ')' : '' }}
                    </option>
                    @endforeach
                </select>
                @error('server_id')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                @if($servers->isEmpty())
                <p class="mt-2 text-xs text-amber-600 dark:text-amber-400">
                    No hay servidores registrados.
                    <a href="{{ route('admin.servers.create') }}" class="underline hover:no-underline">Registrar uno aquí</a>.
                </p>
                @endif
            </div>
        </div>

        {{-- Web & SSL --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden" x-data="{ ssl: {{ old('ssl_enabled', $infra->ssl_enabled) ? 'true' : 'false' }} }">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/30">
                <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Web & SSL</h2>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label for="system_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">URL del Sistema</label>
                        <input type="url" id="system_url" name="system_url" value="{{ old('system_url', $infra->system_url) }}"
                               class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                               placeholder="https://sistema.unamad.edu.pe">
                        @error('system_url')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="web_server" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Servidor Web</label>
                        <input type="text" id="web_server" name="web_server" value="{{ old('web_server', $infra->web_server) }}"
                               class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                               placeholder="Nginx / Apache">
                        @error('web_server')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="environment" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Ambiente <span class="text-red-500">*</span>
                        </label>
                        <select id="environment" name="environment" required
                                class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="production"  {{ old('environment', $infra->environment?->value) === 'production'  ? 'selected' : '' }}>Producción</option>
                            <option value="staging"     {{ old('environment', $infra->environment?->value) === 'staging'     ? 'selected' : '' }}>Staging</option>
                            <option value="development" {{ old('environment', $infra->environment?->value) === 'development' ? 'selected' : '' }}>Desarrollo</option>
                        </select>
                        @error('environment')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Toggle SSL --}}
                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Certificado SSL habilitado</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">El sistema usa HTTPS con SSL/TLS</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="hidden" name="ssl_enabled" value="0">
                        <input type="checkbox" name="ssl_enabled" value="1"
                               {{ old('ssl_enabled', $infra->ssl_enabled) ? 'checked' : '' }}
                               @change="ssl = $event.target.checked"
                               class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 dark:bg-gray-600 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-100 dark:peer-focus:ring-blue-900 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>

                {{-- SSL expiry --}}
                <div x-show="ssl" x-transition>
                    <label for="ssl_expiry" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fecha de Vencimiento del Certificado</label>
                    <input type="date" id="ssl_expiry" name="ssl_expiry"
                           value="{{ old('ssl_expiry', $infra->ssl_expiry?->format('Y-m-d')) }}"
                           class="block w-full sm:w-48 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    @error('ssl_expiry')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- Notas --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/30">
                <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Notas Adicionales</h2>
            </div>
            <div class="p-6">
                <textarea id="notes" name="notes" rows="3"
                          class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                          placeholder="Observaciones sobre la infraestructura…">{{ old('notes', $infra->notes) }}</textarea>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('systems.show', $system) }}"
               class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                Cancelar
            </a>
            <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Guardar Infraestructura
            </button>
        </div>
    </form>
</div>
@endsection
