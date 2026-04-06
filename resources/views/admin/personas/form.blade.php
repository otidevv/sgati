@extends('layouts.app')

@section('title', isset($persona->id) ? 'Editar Persona' : 'Nueva Persona')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    {{-- Header --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.personas.index') }}" class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ isset($persona->id) ? 'Editar Persona' : 'Nueva Persona' }}</h1>
            <p class="mt-1 text-sm text-gray-500">{{ isset($persona->id) ? 'Actualizar datos civiles' : 'Registrar datos civiles del personal' }}</p>
        </div>
    </div>

    {{-- Form --}}
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <form action="{{ isset($persona->id) ? route('admin.personas.update', $persona) : route('admin.personas.store') }}" method="POST" class="p-6 space-y-6">
            @csrf
            @if(isset($persona->id)) @method('PUT') @endif

            {{-- Datos de Identificación --}}
            <div>
                <h3 class="text-sm font-semibold text-gray-900 border-b border-gray-100 pb-2 mb-4">Datos de Identificación</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="dni" class="block text-sm font-medium text-gray-700 mb-1">DNI <span class="text-red-500">*</span></label>
                        <input type="text" id="dni" name="dni" maxlength="8" value="{{ old('dni', $persona->dni ?? '') }}"
                               class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                               placeholder="12345678" required>
                        @error('dni')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="sexo" class="block text-sm font-medium text-gray-700 mb-1">Sexo</label>
                        <select id="sexo" name="sexo" class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">Seleccionar</option>
                            <option value="M" {{ old('sexo', $persona->sexo ?? '') === 'M' ? 'selected' : '' }}>Masculino</option>
                            <option value="F" {{ old('sexo', $persona->sexo ?? '') === 'F' ? 'selected' : '' }}>Femenino</option>
                        </select>
                    </div>
                    <div>
                        <label for="fecha_nacimiento" class="block text-sm font-medium text-gray-700 mb-1">Fecha de Nacimiento</label>
                        <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" 
                               value="{{ old('fecha_nacimiento', isset($persona->fecha_nacimiento) ? $persona->fecha_nacimiento->format('Y-m-d') : '') }}"
                               class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                               max="{{ now()->subDay()->format('Y-m-d') }}">
                    </div>
                </div>
            </div>

            {{-- Nombres y Apellidos --}}
            <div>
                <h3 class="text-sm font-semibold text-gray-900 border-b border-gray-100 pb-2 mb-4">Nombres y Apellidos</h3>
                <div class="space-y-4">
                    <div>
                        <label for="nombres" class="block text-sm font-medium text-gray-700 mb-1">Nombres <span class="text-red-500">*</span></label>
                        <input type="text" id="nombres" name="nombres" value="{{ old('nombres', $persona->nombres ?? '') }}"
                               class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                               placeholder="Juan Carlos" required>
                        @error('nombres')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="apellido_paterno" class="block text-sm font-medium text-gray-700 mb-1">Apellido Paterno <span class="text-red-500">*</span></label>
                            <input type="text" id="apellido_paterno" name="apellido_paterno" value="{{ old('apellido_paterno', $persona->apellido_paterno ?? '') }}"
                                   class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="García" required>
                            @error('apellido_paterno')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="apellido_materno" class="block text-sm font-medium text-gray-700 mb-1">Apellido Materno <span class="text-red-500">*</span></label>
                            <input type="text" id="apellido_materno" name="apellido_materno" value="{{ old('apellido_materno', $persona->apellido_materno ?? '') }}"
                                   class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="López" required>
                            @error('apellido_materno')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>
                {{-- Preview --}}
                <div class="mt-4 p-3 bg-gray-50 rounded-lg border border-gray-200">
                    <p class="text-xs font-medium text-gray-500 mb-1">Nombre completo:</p>
                    <p id="nombreCompleto" class="text-sm font-semibold text-gray-900">—</p>
                </div>
            </div>

            {{-- Contacto --}}
            <div>
                <h3 class="text-sm font-semibold text-gray-900 border-b border-gray-100 pb-2 mb-4">Información de Contacto</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="telefono" class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                        <input type="text" id="telefono" name="telefono" value="{{ old('telefono', $persona->telefono ?? '') }}"
                               class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                               placeholder="987654321">
                    </div>
                    <div>
                        <label for="email_personal" class="block text-sm font-medium text-gray-700 mb-1">Email Personal</label>
                        <input type="email" id="email_personal" name="email_personal" value="{{ old('email_personal', $persona->email_personal ?? '') }}"
                               class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                               placeholder="juan@gmail.com">
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                <a href="{{ route('admin.personas.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">Cancelar</a>
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    {{ isset($persona->id) ? 'Actualizar' : 'Registrar' }}
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const ap = document.getElementById('apellido_paterno');
    const am = document.getElementById('apellido_materno');
    const nm = document.getElementById('nombres');
    const preview = document.getElementById('nombreCompleto');

    function update() {
        const p = ap.value.trim(), m = am.value.trim(), n = nm.value.trim();
        preview.textContent = (p || m || n) ? `${p} ${m}, ${n}`.replace(/\s+/g, ' ').replace(/,\s*$/, '').trim() : '—';
    }
    [ap, am, nm].forEach(el => el.addEventListener('input', update));
    update();
});
</script>
@endpush
@endsection
