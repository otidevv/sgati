@extends('layouts.app')

@section('title', isset($user->id) ? 'Editar Usuario' : 'Nuevo Usuario')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    {{-- Header --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ isset($user->id) ? 'Editar Usuario' : 'Nuevo Usuario' }}</h1>
            <p class="mt-1 text-sm text-gray-500">{{ isset($user->id) ? 'Actualizar cuenta de acceso' : 'Crear cuenta de acceso al sistema' }}</p>
        </div>
    </div>

    {{-- Info --}}
    @if($personas->isEmpty() && !isset($user->id))
    <x-alert-banner type="warning" message="No hay personas disponibles. Primero registra una persona en Personas." />
    @endif

    {{-- Form --}}
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <form action="{{ isset($user->id) ? route('admin.users.update', $user) : route('admin.users.store') }}" method="POST" class="p-6 space-y-6">
            @csrf
            @if(isset($user->id)) @method('PUT') @endif

            {{-- Persona --}}
            <div>
                <h3 class="text-sm font-semibold text-gray-900 border-b border-gray-100 pb-2 mb-4">Vínculo con Persona</h3>
                <label for="persona_id" class="block text-sm font-medium text-gray-700 mb-1">Persona <span class="text-red-500">*</span></label>
                <select id="persona_id" name="persona_id" required
                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <option value="">Seleccionar persona registrada</option>
                    @forelse($personas as $persona)
                    <option value="{{ $persona->id }}" {{ old('persona_id', $user->persona_id ?? '') == $persona->id ? 'selected' : '' }}>
                        {{ $persona->nombre_completo }} — DNI: {{ $persona->dni }}
                    </option>
                    @empty
                    <option value="" disabled>No hay personas disponibles</option>
                    @endforelse
                </select>
                @error('persona_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            {{-- Credenciales --}}
            <div>
                <h3 class="text-sm font-semibold text-gray-900 border-b border-gray-100 pb-2 mb-4">Credenciales de Acceso</h3>
                <div class="space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nombre de Usuario <span class="text-red-500">*</span></label>
                        <input type="text" id="name" name="name" value="{{ old('name', $user->name ?? '') }}"
                               class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                               placeholder="jgarcia" required>
                        @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Institucional <span class="text-red-500">*</span></label>
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email ?? '') }}"
                               class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                               placeholder="usuario@unamad.edu.pe" required>
                        @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Contraseña {{ isset($user->id) ? '(opcional)' : '<span class="text-red-500">*</span>' }}</label>
                            <input type="password" id="password" name="password" {{ !isset($user->id) ? 'required' : '' }}
                                   class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="{{ isset($user->id) ? 'Dejar vacío para mantener' : '••••••••' }}">
                            @error('password')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirmar Contraseña</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" {{ !isset($user->id) ? 'required' : '' }}
                                   class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="Confirmar contraseña">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Permisos --}}
            <div>
                <h3 class="text-sm font-semibold text-gray-900 border-b border-gray-100 pb-2 mb-4">Permisos y Estado</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="role_id" class="block text-sm font-medium text-gray-700 mb-1">Rol <span class="text-red-500">*</span></label>
                        <select id="role_id" name="role_id" required
                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">Seleccionar rol</option>
                            @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ old('role_id', $user->role_id ?? '') == $role->id ? 'selected' : '' }}>
                                {{ $role->label }}
                            </option>
                            @endforeach
                        </select>
                        @error('role_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="area_id" class="block text-sm font-medium text-gray-700 mb-1">Área</label>
                        <select id="area_id" name="area_id"
                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">Seleccionar área</option>
                            @foreach($areas as $area)
                            <option value="{{ $area->id }}" {{ old('area_id', $user->area_id ?? '') == $area->id ? 'selected' : '' }}>
                                {{ $area->name }}{{ $area->acronym ? " ({$area->acronym})" : '' }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                {{-- Active Toggle --}}
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div>
                        <p class="text-sm font-medium text-gray-900">Usuario activo</p>
                        <p class="text-xs text-gray-500">Los usuarios inactivos no pueden iniciar sesión</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" id="is_active" name="is_active" value="1"
                               {{ old('is_active', $user->is_active ?? true) ? 'checked' : '' }}
                               class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-100 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                <a href="{{ route('admin.users.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">Cancelar</a>
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    {{ isset($user->id) ? 'Actualizar' : 'Crear Usuario' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
