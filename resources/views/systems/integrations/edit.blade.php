@extends('layouts.app')
@section('title', 'Editar Integración')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('systems.show', $system) }}"
           class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Editar Integración</h1>
            <p class="mt-1 text-sm text-gray-500">{{ $system->name }}</p>
        </div>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <form action="{{ route('systems.integrations.update', [$system, $integration]) }}" method="POST" class="p-6 space-y-4">
            @csrf @method('PUT')
            <div>
                <label for="target_system_id" class="block text-sm font-medium text-gray-700 mb-1">Sistema destino <span class="text-red-500">*</span></label>
                <select id="target_system_id" name="target_system_id" required
                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    @foreach($otherSystems as $s)
                    <option value="{{ $s->id }}" {{ old('target_system_id', $integration->target_system_id) == $s->id ? 'selected' : '' }}>
                        {{ $s->name }}{{ $s->acronym ? " ({$s->acronym})" : '' }}
                    </option>
                    @endforeach
                </select>
                @error('target_system_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="connection_type" class="block text-sm font-medium text-gray-700 mb-1">Tipo de Conexión <span class="text-red-500">*</span></label>
                    <select id="connection_type" name="connection_type" required
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        @foreach(['api'=>'API REST/SOAP','direct_db'=>'BD Directa','file'=>'Archivo','sftp'=>'SFTP','other'=>'Otro'] as $v => $l)
                        <option value="{{ $v }}" {{ old('connection_type', $integration->connection_type) === $v ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg self-end">
                    <p class="text-sm font-medium text-gray-900">Integración activa</p>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1"
                               {{ old('is_active', $integration->is_active) ? 'checked' : '' }}
                               class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>
            </div>
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                <textarea id="description" name="description" rows="2"
                          class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">{{ old('description', $integration->description) }}</textarea>
            </div>
            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notas Técnicas</label>
                <textarea id="notes" name="notes" rows="2"
                          class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">{{ old('notes', $integration->notes) }}</textarea>
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
