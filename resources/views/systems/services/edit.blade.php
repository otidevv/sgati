@extends('layouts.app')
@section('title', 'Editar Servicio')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('systems.show', $system) }}"
           class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Editar Servicio</h1>
            <p class="mt-1 text-sm text-gray-500">{{ $service->service_name }} — {{ $system->name }}</p>
        </div>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <form action="{{ route('systems.services.update', [$system, $service]) }}" method="POST" class="p-6 space-y-4">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label for="service_name" class="block text-sm font-medium text-gray-700 mb-1">Nombre del Servicio <span class="text-red-500">*</span></label>
                    <input type="text" id="service_name" name="service_name" value="{{ old('service_name', $service->service_name) }}" required
                           class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    @error('service_name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="service_type" class="block text-sm font-medium text-gray-700 mb-1">Tipo <span class="text-red-500">*</span></label>
                    <select id="service_type" name="service_type" required
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        @foreach(['rest_api'=>'REST API','soap'=>'SOAP','sftp'=>'SFTP','smtp'=>'SMTP','ldap'=>'LDAP','database'=>'Base de Datos','other'=>'Otro'] as $v => $l)
                        <option value="{{ $v }}" {{ old('service_type', $service->service_type) === $v ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="direction" class="block text-sm font-medium text-gray-700 mb-1">Dirección <span class="text-red-500">*</span></label>
                    <select id="direction" name="direction" required
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="consumed" {{ old('direction', $service->direction) === 'consumed' ? 'selected' : '' }}>Consumido</option>
                        <option value="exposed"  {{ old('direction', $service->direction) === 'exposed'  ? 'selected' : '' }}>Expuesto</option>
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label for="endpoint_url" class="block text-sm font-medium text-gray-700 mb-1">URL / Endpoint</label>
                    <input type="text" id="endpoint_url" name="endpoint_url" value="{{ old('endpoint_url', $service->endpoint_url) }}"
                           class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm font-mono">
                </div>
                <div>
                    <label for="auth_type" class="block text-sm font-medium text-gray-700 mb-1">Autenticación</label>
                    <input type="text" id="auth_type" name="auth_type" value="{{ old('auth_type', $service->auth_type) }}"
                           class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg self-end">
                    <p class="text-sm font-medium text-gray-900">Servicio activo</p>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1"
                               {{ old('is_active', $service->is_active) ? 'checked' : '' }}
                               class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>
            </div>
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                <textarea id="description" name="description" rows="2"
                          class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">{{ old('description', $service->description) }}</textarea>
            </div>
            <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-100">
                <a href="{{ route('systems.show', $system) }}"
                   class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">Cancelar</a>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
