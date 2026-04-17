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
        <form action="{{ isset($area->id) ? route('admin.areas.update', $area) : route('admin.areas.store') }}"
              method="POST" class="p-6 space-y-6"
              x-data="{
                  submitting: false,
                  nameStatus: null,
                  checkNameUrl: '{{ route('admin.areas.check-name') }}',
                  areaId: {{ $area->id ?? 'null' }},
                  async checkName(value) {
                      const name = value.trim();
                      if (!name) { this.nameStatus = null; return; }
                      this.nameStatus = 'checking';
                      try {
                          const url = this.checkNameUrl + '?name=' + encodeURIComponent(name)
                              + (this.areaId ? '&exclude=' + this.areaId : '');
                          const res  = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                          const data = await res.json();
                          this.nameStatus = data.available ? 'ok' : 'taken';
                      } catch { this.nameStatus = null; }
                  }
              }"
              @submit.prevent="if(nameStatus === 'taken') return; submitting = true; $el.submit()">
            @csrf
            @if(isset($area->id)) @method('PUT') @endif

            {{-- Name --}}
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">
                    Nombre del Área <span class="text-red-500">*</span>
                </label>
                <div class="relative mt-1">
                    <input type="text" id="name" name="name"
                           value="{{ old('name', $area->name ?? '') }}"
                           class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm pr-8"
                           :class="nameStatus === 'taken' ? 'border-red-400 focus:ring-red-500' : nameStatus === 'ok' ? 'border-emerald-400 focus:ring-emerald-500' : ''"
                           placeholder="Oficina de Tecnología de Información"
                           maxlength="150" required
                           @blur="checkName($event.target.value)">
                    <div class="absolute inset-y-0 right-2 flex items-center pointer-events-none">
                        <svg x-show="nameStatus === 'ok'" class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                        <svg x-show="nameStatus === 'taken'" class="w-4 h-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        <svg x-show="nameStatus === 'checking'" class="w-4 h-4 text-gray-400 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                        </svg>
                    </div>
                </div>
                <p x-show="nameStatus === 'taken'" class="mt-1 text-sm text-red-600">Este nombre de área ya existe.</p>
                <p x-show="nameStatus === 'ok'" class="mt-1 text-sm text-emerald-600">Nombre disponible.</p>
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
                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm font-mono uppercase"
                       placeholder="OTI"
                       maxlength="20"
                       oninput="this.value = this.value.toUpperCase()">
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
                          placeholder="Descripción de las funciones del área..."
                          maxlength="1000">{{ old('description', $area->description ?? '') }}</textarea>
                @error('description')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                <a href="{{ route('admin.areas.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancelar
                </a>
                <button type="submit"
                        :disabled="submitting || nameStatus === 'taken'"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors shadow-sm disabled:opacity-60 disabled:cursor-not-allowed">
                    <svg x-show="!submitting" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <svg x-show="submitting" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                    </svg>
                    <span x-text="submitting ? 'Guardando…' : '{{ isset($area->id) ? 'Actualizar' : 'Registrar' }}'"></span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
