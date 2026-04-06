@extends('layouts.app')

@section('title', isset($area->id) ? 'Editar Área' : 'Nueva Área')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    {{-- Page Header --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.areas.index') }}" class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                {{ isset($area->id) ? 'Editar Área' : 'Nueva Área' }}
            </h1>
            <p class="mt-1 text-sm text-gray-500">
                {{ isset($area->id) ? 'Actualizar unidad organizativa' : 'Registrar unidad organizativa de UNAMAD' }}
            </p>
        </div>
    </div>

    {{-- Form Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200/60 overflow-hidden">
        <form action="{{ isset($area->id) ? route('admin.areas.update', $area) : route('admin.areas.store') }}" method="POST" class="p-6 space-y-6">
            @csrf
            @if(isset($area->id)) @method('PUT') @endif

            {{-- Name --}}
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">
                    Nombre del Área <span class="text-red-500">*</span>
                </label>
                <input type="text" id="name" name="name" 
                       value="{{ old('name', $area->name ?? '') }}"
                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                       placeholder="Oficina de Tecnología de Información" required>
                @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Acronym --}}
            <div>
                <label for="acronym" class="block text-sm font-medium text-gray-700">
                    Siglas / Acrónimo
                </label>
                <input type="text" id="acronym" name="acronym" 
                       value="{{ old('acronym', $area->acronym ?? '') }}"
                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                       placeholder="OTI">
                @error('acronym')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Description --}}
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">
                    Descripción
                </label>
                <textarea id="description" name="description" rows="4"
                          class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                          placeholder="Descripción de las funciones del área...">{{ old('description', $area->description ?? '') }}</textarea>
                @error('description')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                <a href="{{ route('admin.areas.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancelar
                </a>
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    {{ isset($area->id) ? 'Actualizar' : 'Registrar' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
