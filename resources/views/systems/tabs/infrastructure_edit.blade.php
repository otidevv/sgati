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

    <form action="{{ route('systems.infrastructure.update', $system) }}" method="POST" class="space-y-5"
          x-data="{ submitting: false }" @submit="submitting = true">
        @csrf @method('PUT')

        {{-- Servidor --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden"
             x-data="infraServerData()">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/30">
                <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Servidor</h2>
            </div>
            <div class="p-6">
                <label for="server_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Servidor asignado</label>
                <select id="server_id" name="server_id"
                        @change="loadIps($event.target.value)"
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

                {{-- IP + Puerto --}}
                <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            IP del servidor
                            <span class="ml-1 text-xs font-normal text-gray-400 dark:text-gray-500">(conexión)</span>
                        </label>

                        {{-- Select IPs privadas --}}
                        <div x-show="ips.length > 0">
                            <select name="server_ip_id" x-model="serverIpId"
                                    class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm font-mono">
                                <option value="">— Sin IP específica —</option>
                                <template x-for="ip in privateIps" :key="ip.id">
                                    <option :value="String(ip.id)"
                                            x-text="ip.ip_address + (ip.is_primary ? ' · principal' : '') + (ip.interface ? ' (' + ip.interface + ')' : '') + (ip.ports.length ? ' [' + ip.ports.length + ' puerto' + (ip.ports.length > 1 ? 's' : '') + ']' : '')">
                                    </option>
                                </template>
                            </select>

                            {{-- Badge tipo IP --}}
                            <div x-show="selectedIp" class="mt-1.5 flex items-center gap-1.5">
                                <span x-show="selectedIp?.type === 'public'"
                                      class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-900/20 dark:text-emerald-400 dark:border-emerald-700">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/></svg>
                                    IP Pública — accesible desde internet
                                </span>
                                <span x-show="selectedIp?.type === 'private'"
                                      class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 border border-gray-200 dark:bg-gray-700 dark:text-gray-400 dark:border-gray-600">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                    IP Privada — solo red local
                                </span>
                            </div>
                        </div>

                        {{-- Sin IPs registradas: campo manual --}}
                        <div x-show="ips.length === 0">
                            <input type="hidden" name="server_ip_id" value="">
                            <input type="text" name="public_ip"
                                   value="{{ old('public_ip', $infra->public_ip) }}"
                                   class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm font-mono"
                                   placeholder="Ej: 200.10.20.30 (pública) o 192.168.1.10 (privada)"
                                   maxlength="45"
                                   onblur="validatePublicIp(this)">
                            <p id="public_ip-error" class="hidden mt-1 text-xs text-red-600 dark:text-red-400">Ingresa una dirección IP válida (IPv4 o IPv6).</p>
                            <p class="mt-1 text-xs text-amber-600 dark:text-amber-400">
                                El servidor no tiene IPs registradas.
                                <a href="{{ route('admin.servers.index') }}" class="underline hover:no-underline">Registra IPs en el servidor</a> para poder seleccionarlas aquí.
                            </p>
                        </div>
                        @error('server_ip_id')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>

                    {{-- Puerto de la IP seleccionada --}}
                    <div x-show="serverIpId" x-transition>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Puerto
                            <span class="ml-1 text-xs font-normal text-gray-400 dark:text-gray-500">(opcional)</span>
                        </label>

                        {{-- Con puertos registrados --}}
                        <template x-if="selectedIpPorts.length > 0">
                            <div>
                                <select name="port"
                                        class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm font-mono">
                                    <option value="">— Sin puerto —</option>
                                    <template x-for="p in selectedIpPorts" :key="p.id">
                                        <option :value="p.port"
                                                :selected="p.port == {{ old('port', $infra->port ?? 'null') }}"
                                                x-text="':' + p.port + ' ' + p.protocol.toUpperCase() + (p.description ? ' · ' + p.description : '')">
                                        </option>
                                    </template>
                                </select>
                                <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">Puertos registrados en esta IP.</p>
                            </div>
                        </template>

                        {{-- Sin puertos registrados --}}
                        <template x-if="selectedIpPorts.length === 0">
                            <div>
                                <select disabled
                                        class="block w-full rounded-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50 text-gray-400 dark:text-gray-500 shadow-sm sm:text-sm cursor-not-allowed">
                                    <option>Sin puertos registrados</option>
                                </select>
                                <p class="mt-1 text-xs text-amber-600 dark:text-amber-400">
                                    Esta IP no tiene puertos. Agrégalos desde la
                                    <a x-show="serverEditUrl" :href="serverEditUrl" target="_blank" class="underline hover:no-underline">ficha del servidor</a>.
                                </p>
                            </div>
                        </template>

                        @error('port')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Aviso: servidor seleccionado pero sin IPs públicas --}}
                <div class="mt-5" x-show="serverId && publicIps.length === 0" x-transition>
                    <div class="flex items-start gap-3 px-4 py-3 rounded-lg bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700/40">
                        <svg class="w-4 h-4 mt-0.5 flex-shrink-0 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="text-xs text-amber-700 dark:text-amber-400">
                            <p class="font-medium">Este servidor no tiene IPs públicas registradas.</p>
                            <p class="mt-0.5">Para exponer el sistema por una IP pública, primero agrégala en el servidor.</p>
                            <a x-show="serverEditUrl" :href="serverEditUrl + '#step4'"
                               class="inline-flex items-center gap-1 mt-1.5 font-medium underline hover:no-underline">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                Editar servidor → agregar IP pública
                            </a>
                        </div>
                    </div>
                </div>

                {{-- IPs Públicas de Exposición --}}
                <div class="mt-5" x-show="publicIps.length > 0" x-transition>
                    <div class="flex items-center gap-2 mb-3">
                        <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/>
                        </svg>
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            IPs Públicas por las que se expone el sistema
                        </label>
                    </div>

                    <div class="space-y-2">
                        <template x-for="(row, index) in exposedRows" :key="index">
                            <div class="flex items-start gap-2">

                                {{-- Select IP pública --}}
                                <div class="flex-1 min-w-0">
                                    <select :name="'exposed_rows[' + index + '][ip_id]'"
                                            x-model="row.ip_id"
                                            @change="row.port = getPortsForIp(row.ip_id)[0]?.port ?? null"
                                            class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm font-mono">
                                        <option value="">— Seleccionar IP —</option>
                                        <template x-for="ip in publicIps" :key="ip.id">
                                            <option :value="ip.id"
                                                    :selected="String(row.ip_id) === String(ip.id)"
                                                    x-text="ip.ip_address + (ip.is_primary ? ' · principal' : '') + (ip.interface ? ' (' + ip.interface + ')' : '') + (ip.ports.length ? ' [' + ip.ports.length + (ip.ports.length > 1 ? ' puertos]' : ' puerto]') : '')">
                                            </option>
                                        </template>
                                    </select>
                                </div>

                                {{-- Select puerto --}}
                                <div class="w-52 shrink-0">
                                    {{-- IP con puertos --}}
                                    <template x-if="row.ip_id && getPortsForIp(row.ip_id).length > 0">
                                        <select :name="'exposed_rows[' + index + '][port]'"
                                                x-model="row.port"
                                                class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm font-mono">
                                            <option value="">— Sin puerto —</option>
                                            <template x-for="p in getPortsForIp(row.ip_id)" :key="p.id">
                                                <option :value="p.port"
                                                        :selected="String(row.port) === String(p.port)"
                                                        x-text="':' + p.port + ' ' + p.protocol.toUpperCase() + (p.description ? ' · ' + p.description : '')">
                                                </option>
                                            </template>
                                        </select>
                                    </template>
                                    {{-- IP sin puertos --}}
                                    <template x-if="row.ip_id && getPortsForIp(row.ip_id).length === 0">
                                        <div>
                                            <input type="hidden" :name="'exposed_rows[' + index + '][port]'" value="">
                                            <p class="text-xs text-amber-600 dark:text-amber-400 pt-1.5">
                                                Sin puertos registrados.
                                                <a :href="serverEditUrl" target="_blank" class="underline hover:no-underline">Agregar →</a>
                                            </p>
                                        </div>
                                    </template>
                                    {{-- Sin IP seleccionada --}}
                                    <template x-if="!row.ip_id">
                                        <input type="hidden" :name="'exposed_rows[' + index + '][port]'" value="">
                                    </template>
                                </div>

                                {{-- Quitar fila --}}
                                <button type="button" @click="removeExposedRow(index)"
                                        x-show="exposedRows.length > 1"
                                        class="mt-0.5 flex-shrink-0 w-7 h-7 flex items-center justify-center rounded-lg text-gray-300 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/30 transition-all"
                                        title="Quitar">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </template>
                    </div>

                    {{-- Agregar fila --}}
                    <button type="button" @click="addExposedRow()"
                            class="mt-2.5 inline-flex items-center gap-1.5 text-xs text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Agregar otra IP expuesta
                    </button>

                    <p class="mt-2 text-xs text-gray-400 dark:text-gray-500">
                        Varios sistemas pueden compartir una misma IP pública (ej. detrás de un proxy o balanceador).
                    </p>
                </div>
            </div>
        </div>

        {{-- Web & SSL --}}
        @php
            $currentSslType = old('ssl_type',
                $infra->ssl_certificate_id ? 'institutional' : 'custom'
            );
        @endphp
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden"
             x-data="{
                ssl: {{ old('ssl_enabled', $infra->ssl_enabled) ? 'true' : 'false' }},
                sslType: '{{ $currentSslType }}'
             }">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/30">
                <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Web & SSL</h2>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label for="system_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">URL / Dirección del Sistema</label>
                        <input type="text" id="system_url" name="system_url" value="{{ old('system_url', $infra->system_url) }}"
                               class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                               placeholder="https://sistema.unamad.edu.pe  o  192.168.1.10:8585"
                               maxlength="255"
                               oninput="checkDomainHint(this)" onblur="validateSystemUrl(this)">
                        <p id="system_url-error" class="hidden mt-1 text-sm text-red-600 dark:text-red-400">URL inválida. Acepta: dominio, IP o IP:puerto (ej. <code class="font-mono">https://app.unamad.edu.pe</code> o <code class="font-mono">192.168.1.10:8080</code>).</p>
                        <p id="system_url-domain-hint" class="hidden mt-1.5 items-start gap-1.5 text-xs text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700/40 rounded-lg px-3 py-2">
                            <svg class="w-3.5 h-3.5 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Estás usando un dominio — asegúrate de que el servidor tenga una <strong>IP Pública</strong> seleccionada en la sección de arriba.
                        </p>
                        <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">Acepta dominio, IP o IP:puerto (con o sin <code class="font-mono">http://</code>).</p>
                        @error('system_url')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="web_server" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Servidor Web</label>
                        <input type="text" id="web_server" name="web_server" value="{{ old('web_server', $infra->web_server) }}"
                               class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                               placeholder="Nginx / Apache"
                               maxlength="50">
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

                {{-- SSL detalles (solo si habilitado) --}}
                <div x-show="ssl" x-transition class="space-y-4">

                    {{-- Tipo de certificado --}}
                    <div>
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tipo de certificado</p>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="relative flex items-start gap-3 p-3 rounded-lg border cursor-pointer transition-colors"
                                   :class="sslType === 'institutional'
                                       ? 'bg-blue-50 dark:bg-blue-900/20 border-blue-300 dark:border-blue-700'
                                       : 'bg-white dark:bg-gray-700/50 border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700'">
                                <input type="radio" name="ssl_type" value="institutional"
                                       x-model="sslType"
                                       class="mt-0.5 text-blue-600 border-gray-300 dark:border-gray-600 focus:ring-blue-500">
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">Certificado institucional</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Certificado registrado en el repositorio de la OTI</p>
                                </div>
                            </label>
                            <label class="relative flex items-start gap-3 p-3 rounded-lg border cursor-pointer transition-colors"
                                   :class="sslType === 'custom'
                                       ? 'bg-amber-50 dark:bg-amber-900/20 border-amber-300 dark:border-amber-700'
                                       : 'bg-white dark:bg-gray-700/50 border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700'">
                                <input type="radio" name="ssl_type" value="custom"
                                       x-model="sslType"
                                       class="mt-0.5 text-amber-600 border-gray-300 dark:border-gray-600 focus:ring-amber-500">
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">Certificado propio</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Cert externo o específico — solo registra la fecha de vencimiento</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- Selector de certificado institucional --}}
                    <div x-show="sslType === 'institutional'" x-transition>
                        <label for="ssl_certificate_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Certificado SSL
                        </label>
                        <select id="ssl_certificate_id" name="ssl_certificate_id"
                                class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                       dark:text-gray-100 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">— Seleccionar certificado —</option>
                            @foreach($sslCerts as $c)
                            <option value="{{ $c->id }}"
                                    {{ old('ssl_certificate_id', $infra->ssl_certificate_id) == $c->id ? 'selected' : '' }}>
                                {{ $c->name }}
                                @if($c->common_name) ({{ $c->common_name }}) @endif
                                @if($c->valid_until) — vence {{ $c->valid_until->format('d/m/Y') }} @endif
                            </option>
                            @endforeach
                        </select>
                        @if($sslCerts->isEmpty())
                        <p class="mt-1.5 text-xs text-amber-600 dark:text-amber-400">
                            No hay certificados registrados.
                            <a href="{{ route('admin.ssl-certificates.create') }}" target="_blank" class="underline hover:no-underline">Registrar uno aquí</a>.
                        </p>
                        @endif
                        @error('ssl_certificate_id')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>

                    {{-- Fecha de vencimiento para certificado propio --}}
                    <div x-show="sslType === 'custom'" x-transition>
                        <label for="ssl_custom_expiry" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Fecha de vencimiento del certificado
                        </label>
                        <input type="date" id="ssl_custom_expiry" name="ssl_custom_expiry"
                               value="{{ old('ssl_custom_expiry', $infra->ssl_custom_expiry?->format('Y-m-d') ?? $infra->ssl_expiry?->format('Y-m-d')) }}"
                               min="{{ now()->format('Y-m-d') }}"
                               class="block w-full sm:w-48 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                      dark:text-gray-100 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        @error('ssl_custom_expiry')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>

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
                          placeholder="Observaciones sobre la infraestructura…"
                          maxlength="2000">{{ old('notes', $infra->notes) }}</textarea>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('systems.show', $system) }}"
               class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                Cancelar
            </a>
            <button type="submit"
                    :disabled="submitting"
                    class="inline-flex items-center gap-2 px-5 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors shadow-sm disabled:opacity-60 disabled:cursor-not-allowed">
                <svg x-show="!submitting" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <svg x-show="submitting" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                </svg>
                <span x-text="submitting ? 'Guardando…' : 'Guardar Infraestructura'"></span>
            </button>
        </div>
    </form>
</div>
@push('scripts')
<script>
function infraServerData() {
    return {
        serverIpsMap:  @json($serverIpsMap),
        serverEditUrls: @json($serverEditUrls),
        serverId:   '{{ old('server_id',    $infra->server_id    ?? '') }}',
        serverIpId: '{{ old('server_ip_id', $infra->server_ip_id ?? '') }}',
        exposedRows: @json(old('exposed_rows') ?? ($exposedRows->isNotEmpty() ? $exposedRows : [['ip_id' => null, 'port' => null]])),
        ips: [],

        get publicIps()       { return this.ips.filter(function(ip){ return ip.type === 'public'; }); },
        get privateIps()      { return this.ips.filter(function(ip){ return ip.type === 'private'; }); },
        get selectedIp()      { return this.ips.find(function(ip){ return ip.id == this.serverIpId; }, this) ?? null; },
        get selectedIpPorts() { return this.selectedIp ? this.selectedIp.ports : []; },
        get serverEditUrl()   { return this.serverId ? (this.serverEditUrls[this.serverId] ?? null) : null; },

        getPortsForIp(ipId) {
            if (!ipId) return [];
            var ip = this.publicIps.find(function(i){ return String(i.id) === String(ipId); });
            return ip ? ip.ports : [];
        },
        addExposedRow() {
            this.exposedRows.push({ ip_id: null, port: null });
        },
        removeExposedRow(index) {
            this.exposedRows.splice(index, 1);
        },
        init() {
            var self = this;
            var savedServerIpId = String(this.serverIpId || '');
            var sid = document.getElementById('server_id').value;
            if (sid) {
                this.loadIps(sid, false);
                this.$nextTick(function() {
                    self.serverIpId = savedServerIpId;
                });
            }
        },
        loadIps(serverId, resetExposed) {
            if (resetExposed === undefined) resetExposed = true;
            this.serverId = serverId;
            this.ips = serverId ? (this.serverIpsMap[serverId] ?? []) : [];
            if (!serverId) {
                this.serverIpId = '';
                this.exposedRows = [{ ip_id: null, port: null }];
                return;
            }
            if (resetExposed) { this.serverIpId = ''; }

            if (!this.serverIpId) {
                var self = this;
                var selected =
                    this.ips.find(function(ip){ return ip.type === 'private' && ip.is_primary; }) ||
                    this.ips.find(function(ip){ return ip.type === 'private'; }) ||
                    this.ips.find(function(ip){ return ip.type === 'public' && ip.is_primary; }) ||
                    this.ips[0];
                if (selected) this.serverIpId = selected.id;
            }

            if (resetExposed) {
                var publicPrimaries = this.ips.filter(function(ip){ return ip.type === 'public' && ip.is_primary; });
                var autoIps = publicPrimaries.length > 0
                    ? publicPrimaries
                    : (this.ips.find(function(ip){ return ip.type === 'public'; })
                        ? [this.ips.find(function(ip){ return ip.type === 'public'; })]
                        : []);
                this.exposedRows = autoIps.length > 0
                    ? autoIps.map(function(ip){ return { ip_id: ip.id, port: ip.ports.length ? ip.ports[0].port : null }; })
                    : [{ ip_id: null, port: null }];
            }
        }
    };
}

function validatePublicIp(input) {
    const val = input.value.trim();
    const err = document.getElementById('public_ip-error');
    if (!val) { err.classList.add('hidden'); input.classList.remove('border-red-400'); return; }
    const ipv4 = /^(\d{1,3}\.){3}\d{1,3}$/;
    const ipv6 = /^[0-9a-fA-F:]{2,39}$/;
    const valid = ipv4.test(val) || ipv6.test(val);
    err.classList.toggle('hidden', valid);
    input.classList.toggle('border-red-400', !valid);
}

function checkDomainHint(input) {
    const val = input.value.trim().replace(/^https?:\/\//i, '').split('/')[0].split(':')[0];
    const hint = document.getElementById('system_url-domain-hint');
    const isDomain = val.length > 0 && /[a-zA-Z]/.test(val) && !/^(\d{1,3}\.){3}\d{1,3}$/.test(val);
    hint.classList.toggle('hidden', !isDomain);
    hint.style.display = isDomain ? 'flex' : '';
}

function validateSystemUrl(input) {
    const val = input.value.trim();
    const err = document.getElementById('system_url-error');
    if (!val) { err.classList.add('hidden'); input.classList.remove('border-red-400'); return; }
    const valid = /^(https?:\/\/)?[\w\-\.]+(\:\d+)?(\/\S*)?$/i.test(val);
    err.classList.toggle('hidden', valid);
    input.classList.toggle('border-red-400', !valid);
}
</script>
@endpush
@endsection
