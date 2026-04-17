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
        <form action="{{ isset($user->id) ? route('admin.users.update', $user) : route('admin.users.store') }}"
              method="POST" class="p-6 space-y-6"
              x-data="{
                  submitting: false,
                  emailStatus: null,
                  emailCheckUrl: '{{ route('admin.users.check-email') }}',
                  userId: {{ $user->id ?? 'null' }},

                  personaId: '{{ old('persona_id', $user->persona_id ?? '') }}',
                  personaName: '{{ isset($user->persona) ? addslashes($user->persona->nombre_completo . ' — DNI: ' . $user->persona->dni) : '' }}',
                  personaSearch: '',
                  personaResults: [],
                  personaDropdown: false,
                  personaTimer: null,

                  onPersonaInput() {
                      const q = this.personaSearch.trim();
                      clearTimeout(this.personaTimer);
                      this.personaResults = [];
                      this.personaDropdown = false;
                      if (q.length < 4) return;
                      this.personaTimer = setTimeout(() => this.fetchPersonas(q), 300);
                  },
                  async fetchPersonas(q) {
                      try {
                          const res  = await fetch('{{ route('admin.personas.search') }}?q=' + encodeURIComponent(q), { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                          const data = await res.json();
                          this.personaResults  = data;
                          this.personaDropdown = data.length > 0;
                      } catch {}
                  },
                  selectPersona(p) {
                      this.personaId    = p.id;
                      this.personaName  = p.apellido_paterno + ' ' + (p.apellido_materno ?? '') + ', ' + p.nombres + ' — DNI: ' + p.dni;
                      this.personaSearch   = '';
                      this.personaDropdown = false;
                      this.personaResults  = [];
                  },
                  clearPersona() {
                      this.personaId = '';
                      this.personaName = '';
                  },

                  async checkEmail(value) {
                      const email = value.trim();
                      if (!email) { this.emailStatus = null; return; }
                      this.emailStatus = 'checking';
                      try {
                          const url = this.emailCheckUrl + '?email=' + encodeURIComponent(email)
                              + (this.userId ? '&exclude=' + this.userId : '');
                          const res  = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                          const data = await res.json();
                          this.emailStatus = data.available ? 'ok' : 'taken';
                      } catch { this.emailStatus = null; }
                  }
              }"
              @submit.prevent="if(emailStatus === 'taken' || !personaId) return; submitting = true; $el.submit()"
              @click.outside="personaDropdown = false">
            @csrf
            @if(isset($user->id)) @method('PUT') @endif

            {{-- Persona --}}
            <div>
                <h3 class="text-sm font-semibold text-gray-900 border-b border-gray-100 pb-2 mb-4">Vínculo con Persona</h3>
                <label class="block text-sm font-medium text-gray-700 mb-1">Persona <span class="text-red-500">*</span></label>

                <input type="hidden" name="persona_id" :value="personaId">

                {{-- Persona ya seleccionada --}}
                <div x-show="personaName"
                     class="flex items-center gap-2 px-3 py-2 rounded-lg bg-blue-50 border border-blue-200 mb-2">
                    <svg class="w-4 h-4 text-blue-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <span x-text="personaName" class="flex-1 text-sm font-medium text-blue-700 truncate"></span>
                    <button type="button" @click="clearPersona()"
                            class="text-blue-400 hover:text-red-500 transition-colors flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Campo de búsqueda --}}
                <div x-show="!personaName" class="relative">
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" x-model="personaSearch"
                               @input="onPersonaInput()"
                               @keydown.escape="personaDropdown = false"
                               placeholder="Buscar por DNI o apellido/nombre…"
                               autocomplete="off"
                               class="block w-full pl-9 pr-4 py-2 rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                    <p class="mt-1 text-xs text-gray-400">Escribe al menos 4 caracteres para buscar</p>

                    {{-- Dropdown --}}
                    <div x-show="personaDropdown"
                         class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-xl max-h-52 overflow-y-auto text-sm">
                        <template x-for="p in personaResults" :key="p.id">
                            <button type="button" @click="selectPersona(p)"
                                    class="w-full text-left px-4 py-2.5 text-gray-800 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                                <span x-text="p.apellido_paterno + ' ' + (p.apellido_materno ?? '') + ', ' + p.nombres"></span>
                                <span class="ml-2 font-mono text-xs text-gray-400" x-text="'DNI: ' + p.dni"></span>
                            </button>
                        </template>
                    </div>
                </div>

                <p x-show="!personaId && submitting" class="mt-1 text-sm text-red-600">Debes seleccionar una persona.</p>
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
                               placeholder="jgarcia" maxlength="255" required>
                        @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Institucional <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="email" id="email" name="email" value="{{ old('email', $user->email ?? '') }}"
                                   class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm pr-8"
                                   :class="emailStatus === 'taken' ? 'border-red-400 focus:ring-red-500' : emailStatus === 'ok' ? 'border-emerald-400 focus:ring-emerald-500' : ''"
                                   placeholder="usuario@unamad.edu.pe"
                                   maxlength="255" required
                                   @blur="checkEmail($event.target.value)">
                            <div class="absolute inset-y-0 right-2 flex items-center pointer-events-none">
                                <svg x-show="emailStatus === 'ok'" class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                                <svg x-show="emailStatus === 'taken'" class="w-4 h-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                <svg x-show="emailStatus === 'checking'" class="w-4 h-4 text-gray-400 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                                </svg>
                            </div>
                        </div>
                        <p x-show="emailStatus === 'taken'" class="mt-1 text-sm text-red-600">Este email ya está registrado en otro usuario.</p>
                        <p x-show="emailStatus === 'ok'" class="mt-1 text-sm text-emerald-600">Email disponible.</p>
                        @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div x-data="{ strength: 0, calcStrength(v) { let s=0; if(v.length>=8)s++; if(/[A-Z]/.test(v))s++; if(/[0-9]/.test(v))s++; if(/[^A-Za-z0-9]/.test(v))s++; this.strength=v?s:0; } }">
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                                Contraseña @isset($user->id) <span class="font-normal text-gray-400">(opcional)</span> @else <span class="text-red-500">*</span> @endisset
                            </label>
                            <input type="password" id="password" name="password" {{ !isset($user->id) ? 'required' : '' }}
                                   class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="{{ isset($user->id) ? 'Dejar vacío para mantener' : '••••••••' }}"
                                   minlength="8"
                                   @input="calcStrength($event.target.value)">
                            {{-- Indicador de fortaleza --}}
                            <div x-show="strength > 0" class="mt-1.5 flex gap-1">
                                <div class="h-1 flex-1 rounded-full transition-colors" :class="strength >= 1 ? 'bg-red-400' : 'bg-gray-200'"></div>
                                <div class="h-1 flex-1 rounded-full transition-colors" :class="strength >= 2 ? 'bg-orange-400' : 'bg-gray-200'"></div>
                                <div class="h-1 flex-1 rounded-full transition-colors" :class="strength >= 3 ? 'bg-yellow-400' : 'bg-gray-200'"></div>
                                <div class="h-1 flex-1 rounded-full transition-colors" :class="strength >= 4 ? 'bg-emerald-500' : 'bg-gray-200'"></div>
                            </div>
                            <p x-show="strength > 0" class="mt-0.5 text-xs"
                               :class="{ 'text-red-500': strength===1, 'text-orange-500': strength===2, 'text-yellow-600': strength===3, 'text-emerald-600': strength===4 }"
                               x-text="['','Muy débil','Débil','Aceptable','Fuerte'][strength]"></p>
                            @error('password')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div x-data="{ mismatch: false }"
                             x-on:blur.capture="mismatch = $event.target.value !== '' && $event.target.value !== document.getElementById('password').value">
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirmar Contraseña</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" {{ !isset($user->id) ? 'required' : '' }}
                                   class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   :class="mismatch ? 'border-red-400 focus:ring-red-500' : ''"
                                   placeholder="Confirmar contraseña">
                            <p x-show="mismatch" class="mt-1 text-sm text-red-600">Las contraseñas no coinciden.</p>
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
                <button type="submit"
                        :disabled="submitting || emailStatus === 'taken'"
                        class="inline-flex items-center gap-2 px-5 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors shadow-sm disabled:opacity-60 disabled:cursor-not-allowed">
                    <svg x-show="!submitting" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <svg x-show="submitting" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                    </svg>
                    <span x-text="submitting ? 'Guardando…' : '{{ isset($user->id) ? 'Actualizar' : 'Crear Usuario' }}'"></span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
