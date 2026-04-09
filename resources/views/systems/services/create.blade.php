@extends('layouts.app')
@section('title', 'Nuevo Servicio / API — ' . $system->name)

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    {{-- Encabezado --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('systems.show', $system) }}"
           class="inline-flex items-center justify-center w-10 h-10 rounded-lg
                  bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300
                  hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Nuevo Servicio / API</h1>
            <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">{{ $system->name }}</p>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
        <form action="{{ route('systems.services.store', $system) }}" method="POST">
            @csrf

            {{-- ── Identificación ── --}}
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Identificación</h3>
            </div>
            <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-5">

                <div class="sm:col-span-2">
                    <label for="service_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Nombre del Servicio <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="service_name" name="service_name" value="{{ old('service_name') }}" required
                           placeholder="API de Matrícula"
                           class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    @error('service_name')<p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="service_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Tipo <span class="text-red-500">*</span></label>
                    <select id="service_type" name="service_type" required
                            class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="">Seleccionar…</option>
                        @foreach(['rest_api'=>'REST API','soap'=>'SOAP','sftp'=>'SFTP','smtp'=>'SMTP','ldap'=>'LDAP','database'=>'Base de Datos','other'=>'Otro'] as $v => $l)
                        <option value="{{ $v }}" {{ old('service_type') === $v ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                    @error('service_type')<p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="direction" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Dirección <span class="text-red-500">*</span></label>
                    <select id="direction" name="direction" required
                            class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="consumed" {{ old('direction', 'consumed') === 'consumed' ? 'selected' : '' }}>Consumido (el sistema usa este servicio)</option>
                        <option value="exposed"  {{ old('direction') === 'exposed' ? 'selected' : '' }}>Expuesto (el sistema ofrece este servicio)</option>
                    </select>
                    @error('direction')<p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="environment" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Ambiente <span class="text-red-500">*</span></label>
                    <select id="environment" name="environment" required
                            class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="production"  {{ old('environment', 'production') === 'production'  ? 'selected' : '' }}>Producción</option>
                        <option value="staging"     {{ old('environment') === 'staging'     ? 'selected' : '' }}>Staging</option>
                        <option value="development" {{ old('environment') === 'development' ? 'selected' : '' }}>Desarrollo</option>
                    </select>
                </div>

                <div>
                    <label for="version" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Versión</label>
                    <input type="text" id="version" name="version" value="{{ old('version') }}"
                           placeholder="v1, v2, 2024…"
                           class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>

                <div class="sm:col-span-2">
                    <label for="endpoint_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">URL / Endpoint</label>
                    <input type="text" id="endpoint_url" name="endpoint_url" value="{{ old('endpoint_url') }}"
                           placeholder="https://api.ejemplo.com/v1/endpoint"
                           class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white font-mono shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>

                <div>
                    <label for="valid_from" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Vigencia desde</label>
                    <input type="date" id="valid_from" name="valid_from" value="{{ old('valid_from') }}"
                           class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>

                <div>
                    <label for="valid_until" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Vigencia hasta</label>
                    <input type="date" id="valid_until" name="valid_until" value="{{ old('valid_until') }}"
                           class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>

                <div class="sm:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Descripción</label>
                    <textarea id="description" name="description" rows="2"
                              placeholder="Descripción del servicio o API…"
                              class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm resize-none">{{ old('description') }}</textarea>
                </div>

                <div class="sm:col-span-2 flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Servicio activo</p>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:ring-4 peer-focus:ring-blue-100 dark:peer-focus:ring-blue-900 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>
            </div>

            {{-- ── Proveedor ── --}}
            <div class="px-6 py-4 border-t border-b border-gray-100 dark:border-gray-700">
                <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Proveedor del servicio</h3>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Tipo de proveedor</label>
                    <div class="flex gap-4">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="provider_type" value="" id="prov-none"
                                   {{ old('provider_type', '') === '' ? 'checked' : '' }}
                                   class="text-blue-600 focus:ring-blue-500"
                                   onchange="toggleProvider(this.value)">
                            <span class="text-sm text-gray-700 dark:text-gray-300">Sin proveedor</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="provider_type" value="internal" id="prov-internal"
                                   {{ old('provider_type') === 'internal' ? 'checked' : '' }}
                                   class="text-blue-600 focus:ring-blue-500"
                                   onchange="toggleProvider(this.value)">
                            <span class="text-sm text-gray-700 dark:text-gray-300">Sistema interno</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="provider_type" value="external" id="prov-external"
                                   {{ old('provider_type') === 'external' ? 'checked' : '' }}
                                   class="text-blue-600 focus:ring-blue-500"
                                   onchange="toggleProvider(this.value)">
                            <span class="text-sm text-gray-700 dark:text-gray-300">Entidad externa</span>
                        </label>
                    </div>
                </div>

                <div id="wrap-internal" class="{{ old('provider_type') === 'internal' ? '' : 'hidden' }}">
                    <label for="provider_system_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Sistema proveedor</label>
                    <select id="provider_system_id" name="provider_system_id"
                            class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="">— Seleccionar sistema —</option>
                        @foreach($allSystems as $s)
                        <option value="{{ $s->id }}" {{ old('provider_system_id') == $s->id ? 'selected' : '' }}>
                            {{ $s->name }}{{ $s->acronym ? ' (' . $s->acronym . ')' : '' }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div id="wrap-external" class="{{ old('provider_type') === 'external' ? '' : 'hidden' }}">
                    <label for="provider_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Nombre del proveedor externo</label>
                    <input type="text" id="provider_name" name="provider_name" value="{{ old('provider_name') }}"
                           placeholder="RENIEC, SUNAT, Ministerio de Educación…"
                           class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>

                {{-- Quién solicitó --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Solicitado por</label>
                    <div class="relative" id="requester-search-wrap">
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input type="text" id="requester-search-input" autocomplete="off"
                                   placeholder="Buscar por DNI o nombre…"
                                   class="w-full pl-9 pr-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <input type="hidden" name="requested_by_persona_id" id="requester-persona_id">
                        <div id="requester-dropdown"
                             class="hidden absolute z-50 w-full mt-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg shadow-xl max-h-48 overflow-y-auto text-sm"></div>
                        <div id="requester-selected"
                             class="hidden mt-2 items-center gap-2 px-3 py-2 rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700/40">
                            <svg class="w-4 h-4 text-blue-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <span id="requester-selected-name" class="flex-1 text-sm font-medium text-blue-700 dark:text-blue-300 truncate"></span>
                            <button type="button" onclick="clearRequester()"
                                    class="text-blue-400 hover:text-red-500 transition-colors">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">Escribe al menos 4 caracteres para buscar</p>
                    </div>
                </div>
            </div>

            {{-- ── Autenticación / Credenciales ── --}}
            <div class="px-6 py-4 border-t border-b border-gray-100 dark:border-gray-700">
                <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    Autenticación y credenciales <span class="font-normal normal-case text-gray-400">(opcional)</span>
                </h3>
            </div>
            <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="sm:col-span-2">
                    <label for="auth_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Tipo de autenticación</label>
                    <input type="text" id="auth_type" name="auth_type" value="{{ old('auth_type') }}"
                           placeholder="API Key / OAuth2 / Basic Auth / JWT…"
                           class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>
                <div>
                    <label for="api_key" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">API Key</label>
                    <input type="password" id="api_key" name="api_key" autocomplete="new-password"
                           class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white font-mono shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>
                <div>
                    <label for="api_secret" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">API Secret / Client Secret</label>
                    <input type="password" id="api_secret" name="api_secret" autocomplete="new-password"
                           class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white font-mono shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>
                <div>
                    <label for="token" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Token / Bearer</label>
                    <input type="password" id="token" name="token" autocomplete="new-password"
                           class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white font-mono shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>
                <div>
                    <label for="token_expires_at" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Vencimiento del token</label>
                    <input type="date" id="token_expires_at" name="token_expires_at" value="{{ old('token_expires_at') }}"
                           class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>
            </div>

            {{-- Acciones --}}
            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                <a href="{{ route('systems.show', $system) }}"
                   class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                    Cancelar
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Registrar Servicio
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
(function () {
    const personaSearchUrl = "{{ route('admin.personas.search') }}";
    let timer;

    // ── Proveedor toggle ─────────────────────────────────────────────────────
    window.toggleProvider = function (val) {
        document.getElementById('wrap-internal').classList.toggle('hidden', val !== 'internal');
        document.getElementById('wrap-external').classList.toggle('hidden', val !== 'external');
    };
    // Inicializar
    const checked = document.querySelector('input[name="provider_type"]:checked');
    if (checked) toggleProvider(checked.value);

    // ── Persona autocomplete (solicitante) ───────────────────────────────────
    const searchInput = document.getElementById('requester-search-input');
    const hiddenInput = document.getElementById('requester-persona_id');
    const dropdown    = document.getElementById('requester-dropdown');
    const selected    = document.getElementById('requester-selected');
    const selName     = document.getElementById('requester-selected-name');

    searchInput.addEventListener('input', function () {
        const q = this.value.trim();
        clearTimeout(timer);
        dropdown.classList.add('hidden');
        dropdown.innerHTML = '';
        if (q.length < 4) return;

        dropdown.innerHTML = '<p class="px-4 py-3 text-xs text-gray-400">Buscando...</p>';
        dropdown.classList.remove('hidden');

        timer = setTimeout(async () => {
            try {
                const res  = await fetch(personaSearchUrl + '?q=' + encodeURIComponent(q));
                const data = await res.json();
                dropdown.innerHTML = '';
                if (!data.length) {
                    dropdown.innerHTML = '<p class="px-4 py-3 text-xs text-gray-400">Sin resultados</p>';
                    return;
                }
                data.forEach(p => {
                    const btn = document.createElement('button');
                    btn.type  = 'button';
                    btn.className = 'w-full text-left px-4 py-2.5 text-sm text-gray-800 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors';
                    btn.innerHTML = `${p.apellido_paterno} ${p.apellido_materno ?? ''}, ${p.nombres} &mdash; <span class="font-mono">${p.dni}</span>`;
                    btn.addEventListener('click', () => {
                        hiddenInput.value = p.id;
                        selName.textContent = `${p.apellido_paterno} ${p.apellido_materno ?? ''}, ${p.nombres} (${p.dni})`;
                        searchInput.value = '';
                        dropdown.classList.add('hidden');
                        selected.classList.remove('hidden');
                        selected.classList.add('flex');
                    });
                    dropdown.appendChild(btn);
                });
            } catch {
                dropdown.innerHTML = '<p class="px-4 py-3 text-xs text-red-400">Error al buscar</p>';
            }
        }, 300);
    });

    document.addEventListener('click', e => {
        if (!document.getElementById('requester-search-wrap').contains(e.target))
            dropdown.classList.add('hidden');
    });

    window.clearRequester = function () {
        hiddenInput.value = '';
        selName.textContent = '';
        selected.classList.add('hidden');
        selected.classList.remove('flex');
        searchInput.value = '';
    };
})();
</script>
@endpush
@endsection
