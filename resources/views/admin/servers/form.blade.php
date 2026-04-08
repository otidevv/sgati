@extends('layouts.app')

@section('title', isset($server->id) ? 'Editar Servidor' : 'Nuevo Servidor')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    {{-- Encabezado --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.servers.index') }}"
           class="inline-flex items-center justify-center w-10 h-10 rounded-lg
                  bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300
                  hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                {{ isset($server->id) ? 'Editar Servidor' : 'Nuevo Servidor' }}
            </h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ isset($server->id) ? $server->name : 'Registrar servidor en la infraestructura de la OTI' }}
            </p>
        </div>
    </div>

    <form action="{{ isset($server->id) ? route('admin.servers.update', $server) : route('admin.servers.store') }}"
          method="POST">
        @csrf
        @if(isset($server->id)) @method('PUT') @endif

        <div class="space-y-5">

            {{-- ── Información general ── --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                        Información General
                    </h2>
                </div>
                <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-5">

                    {{-- Nombre --}}
                    <div class="sm:col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Nombre del Servidor <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="name" name="name"
                               value="{{ old('name', $server->name ?? '') }}"
                               placeholder="ADMISION, PRODUCCION..."
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600
                                      dark:bg-gray-700 dark:text-white
                                      focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                        @error('name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>

                    {{-- Sistema operativo --}}
                    <div>
                        <label for="operating_system" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Sistema Operativo
                        </label>
                        <input type="text" id="operating_system" name="operating_system"
                               value="{{ old('operating_system', $server->operating_system ?? '') }}"
                               placeholder="Ubuntu Server 24.04.2 LTS"
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600
                                      dark:bg-gray-700 dark:text-white
                                      focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        @error('operating_system')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>

                    {{-- Función --}}
                    <div>
                        <label for="function" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Función Principal <span class="text-red-500">*</span>
                        </label>
                        <select id="function" name="function"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600
                                       dark:bg-gray-700 dark:text-white
                                       focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            @foreach($functions as $fn)
                            <option value="{{ $fn->value }}"
                                {{ old('function', $server->function?->value ?? '') === $fn->value ? 'selected' : '' }}>
                                {{ $fn->label() }}
                            </option>
                            @endforeach
                        </select>
                        @error('function')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>

                    {{-- Servicios instalados --}}
                    <div class="sm:col-span-2">
                        <label for="installed_services" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Servicios Instalados
                            <span class="font-normal text-gray-400 text-xs ml-1">(separados por coma)</span>
                        </label>
                        <input type="text" id="installed_services" name="installed_services"
                               value="{{ old('installed_services', implode(', ', $server->installed_services ?? [])) }}"
                               placeholder="Docker, Nginx, Node.js, PostgreSQL..."
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600
                                      dark:bg-gray-700 dark:text-white
                                      focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <p class="mt-1 text-xs text-gray-400">Ej: Docker, Nginx, Redis, Node.js</p>
                    </div>

                    {{-- Web root --}}
                    <div>
                        <label for="web_root" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Ruta Web Root
                        </label>
                        <input type="text" id="web_root" name="web_root"
                               value="{{ old('web_root', $server->web_root ?? '') }}"
                               placeholder="/var/www/html"
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600
                                      dark:bg-gray-700 dark:text-white font-mono
                                      focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>

                    {{-- Estado --}}
                    <div class="flex items-center gap-3 pt-6">
                        <button type="button" id="toggle-active" role="switch"
                                onclick="toggleActive()"
                                class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors
                                       {{ old('is_active', $server->is_active ?? true) ? 'bg-emerald-500' : 'bg-gray-300 dark:bg-gray-600' }}">
                            <span id="toggle-thumb"
                                  class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform
                                         {{ old('is_active', $server->is_active ?? true) ? 'translate-x-6' : 'translate-x-1' }}">
                            </span>
                        </button>
                        <input type="hidden" name="is_active" id="is_active"
                               value="{{ old('is_active', $server->is_active ?? true) ? '1' : '0' }}">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300 cursor-pointer"
                               onclick="toggleActive()">
                            Servidor activo
                        </label>
                    </div>

                    {{-- Notas --}}
                    <div class="sm:col-span-2">
                        <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Notas
                        </label>
                        <textarea id="notes" name="notes" rows="3"
                                  placeholder="Observaciones adicionales..."
                                  class="w-full rounded-lg border-gray-300 dark:border-gray-600
                                         dark:bg-gray-700 dark:text-white
                                         focus:ring-blue-500 focus:border-blue-500 sm:text-sm">{{ old('notes', $server->notes ?? '') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- ── Tipo y Recursos ── --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center gap-2">
                    <svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2v-4M9 21H5a2 2 0 01-2-2v-4m0 0h18"/>
                    </svg>
                    <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                        Tipo y Recursos
                    </h2>
                </div>
                <div class="p-6 space-y-5">

                    {{-- Tipo de servidor --}}
                    <div x-data="{ hostType: '{{ old('host_type', $server->host_type ?? 'physical') }}' }">

                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Tipo de Servidor <span class="text-red-500">*</span>
                        </label>

                        <div class="flex gap-3">
                            @foreach([
                                'physical' => ['label' => 'Físico',       'icon' => 'M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01'],
                                'virtual'  => ['label' => 'Virtual (VM)',  'icon' => 'M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4'],
                                'cloud'    => ['label' => 'Nube',          'icon' => 'M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z'],
                            ] as $val => $opt)
                            <label @click="hostType = '{{ $val }}'"
                                   :class="hostType === '{{ $val }}'
                                       ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20'
                                       : 'border-gray-200 dark:border-gray-600 hover:border-blue-300 dark:hover:border-blue-600'"
                                   class="flex-1 flex flex-col items-center gap-2 p-3 rounded-xl border-2 cursor-pointer transition-all">
                                <input type="radio" name="host_type" value="{{ $val }}" class="sr-only"
                                       :checked="hostType === '{{ $val }}'">
                                <svg :class="hostType === '{{ $val }}' ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500'"
                                     class="w-6 h-6 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $opt['icon'] }}"/>
                                </svg>
                                <span :class="hostType === '{{ $val }}' ? 'text-blue-700 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400'"
                                      class="text-xs font-medium transition-colors">
                                    {{ $opt['label'] }}
                                </span>
                            </label>
                            @endforeach
                        </div>

                        {{-- Campos nube: solo visibles cuando hostType === 'cloud' --}}
                        <div x-show="hostType === 'cloud'" x-cloak
                             class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-4 mt-4 border-t border-gray-100 dark:border-gray-700">
                            <div>
                                <label for="cloud_provider" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                    Proveedor Cloud <span class="text-red-500">*</span>
                                </label>
                                <select id="cloud_provider" name="cloud_provider"
                                        class="w-full rounded-lg border-gray-300 dark:border-gray-600
                                               dark:bg-gray-700 dark:text-white
                                               focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                    <option value="">Seleccionar...</option>
                                    @foreach(['aws' => 'Amazon AWS', 'gcp' => 'Google Cloud', 'azure' => 'Microsoft Azure', 'digitalocean' => 'DigitalOcean', 'linode' => 'Linode / Akamai', 'other' => 'Otro'] as $v => $lbl)
                                    <option value="{{ $v }}" {{ old('cloud_provider', $server->cloud_provider ?? '') === $v ? 'selected' : '' }}>{{ $lbl }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="cloud_region" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Región</label>
                                <input type="text" id="cloud_region" name="cloud_region"
                                       value="{{ old('cloud_region', $server->cloud_region ?? '') }}"
                                       placeholder="us-east-1, sa-east-1..."
                                       class="w-full rounded-lg border-gray-300 dark:border-gray-600
                                              dark:bg-gray-700 dark:text-white font-mono
                                              focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            </div>
                            <div>
                                <label for="cloud_instance" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Tipo de Instancia</label>
                                <input type="text" id="cloud_instance" name="cloud_instance"
                                       value="{{ old('cloud_instance', $server->cloud_instance ?? '') }}"
                                       placeholder="t3.medium, e2-standard-2..."
                                       class="w-full rounded-lg border-gray-300 dark:border-gray-600
                                              dark:bg-gray-700 dark:text-white font-mono
                                              focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            </div>
                        </div>

                    </div>{{-- /x-data hostType --}}

                    {{-- Recursos de hardware --}}
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label for="cpu_cores" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Núcleos CPU</label>
                            <div class="relative">
                                <input type="number" id="cpu_cores" name="cpu_cores" min="1" max="512"
                                       value="{{ old('cpu_cores', $server->cpu_cores ?? '') }}"
                                       placeholder="4"
                                       class="w-full rounded-lg border-gray-300 dark:border-gray-600
                                              dark:bg-gray-700 dark:text-white
                                              focus:ring-blue-500 focus:border-blue-500 sm:text-sm pr-14">
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400">cores</span>
                            </div>
                        </div>
                        <div>
                            <label for="ram_gb" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Memoria RAM</label>
                            <div class="relative">
                                <input type="number" id="ram_gb" name="ram_gb" min="1" max="65536"
                                       value="{{ old('ram_gb', $server->ram_gb ?? '') }}"
                                       placeholder="16"
                                       class="w-full rounded-lg border-gray-300 dark:border-gray-600
                                              dark:bg-gray-700 dark:text-white
                                              focus:ring-blue-500 focus:border-blue-500 sm:text-sm pr-8">
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400">GB</span>
                            </div>
                        </div>
                        <div>
                            <label for="storage_gb" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Almacenamiento</label>
                            <div class="relative">
                                <input type="number" id="storage_gb" name="storage_gb" min="1"
                                       value="{{ old('storage_gb', $server->storage_gb ?? '') }}"
                                       placeholder="500"
                                       class="w-full rounded-lg border-gray-300 dark:border-gray-600
                                              dark:bg-gray-700 dark:text-white
                                              focus:ring-blue-500 focus:border-blue-500 sm:text-sm pr-8">
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400">GB</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- ── Acceso Remoto (RDP / SSH) ── --}}
            @php
                $osVal       = old('operating_system', $server->operating_system ?? '');
                $isWindows   = str_contains(strtolower($osVal), 'windows');
                $proto       = $isWindows ? 'rdp' : 'ssh';
                $defaultPort = $isWindows ? 3389 : 22;
            @endphp
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center gap-2">
                    <svg class="w-4 h-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    <h2 id="access-section-title"
                        class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                        Acceso Remoto
                    </h2>
                    {{-- Badge de protocolo detectado --}}
                    <span id="proto-badge"
                          class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold tracking-wide
                                 {{ $proto === 'rdp'
                                    ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300'
                                    : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300' }}">
                        {{ strtoupper($proto) }}
                    </span>
                    <span class="text-xs text-gray-400 font-normal">(credenciales encriptadas)</span>
                </div>
                <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-5">

                    {{-- Usuario --}}
                    <div>
                        <label for="ssh_user" id="label-user"
                               class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            {{ $proto === 'rdp' ? 'Usuario Windows' : 'Usuario SSH' }}
                        </label>
                        <input type="text" id="ssh_user" name="ssh_user"
                               value="{{ old('ssh_user', $server->ssh_user ?? '') }}"
                               placeholder="{{ $proto === 'rdp' ? 'Administrator' : 'root' }}"
                               autocomplete="off"
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600
                                      dark:bg-gray-700 dark:text-white font-mono
                                      focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>

                    {{-- Contraseña --}}
                    <div>
                        <label for="ssh_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Contraseña
                            @if(isset($server->id))
                            <span class="font-normal text-gray-400 text-xs ml-1">(dejar vacío para no cambiar)</span>
                            @endif
                        </label>
                        <input type="password" id="ssh_password" name="ssh_password"
                               autocomplete="new-password"
                               placeholder="{{ isset($server->id) ? '••••••••' : 'Contraseña' }}"
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600
                                      dark:bg-gray-700 dark:text-white
                                      focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>

                    {{-- Puerto --}}
                    <div>
                        <label for="rdp_port" id="label-port"
                               class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Puerto {{ strtoupper($proto) }}
                        </label>
                        <div class="relative">
                            <input type="number" id="rdp_port" name="rdp_port"
                                   min="1" max="65535"
                                   value="{{ old('rdp_port', $server->rdp_port ?? $defaultPort) }}"
                                   placeholder="{{ $defaultPort }}"
                                   class="w-full rounded-lg border-gray-300 dark:border-gray-600
                                          dark:bg-gray-700 dark:text-white font-mono
                                          focus:ring-blue-500 focus:border-blue-500 sm:text-sm pr-16">
                            <span id="port-suffix"
                                  class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-mono font-semibold
                                         {{ $proto === 'rdp' ? 'text-blue-400' : 'text-emerald-400' }}">
                                {{ strtoupper($proto) }}
                            </span>
                        </div>
                        <p id="port-hint" class="mt-1 text-xs text-gray-400">
                            {{ $proto === 'rdp' ? 'Puerto RDP por defecto: 3389' : 'Puerto SSH por defecto: 22' }}
                        </p>
                    </div>

                    {{-- Indicador visual de qué se va a crear en Guacamole --}}
                    <div class="flex items-end pb-1">
                        <div id="guac-info"
                             class="inline-flex items-center gap-2 px-3 py-2 rounded-lg text-xs
                                    {{ $proto === 'rdp'
                                       ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300'
                                       : 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300' }}">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <span id="guac-info-text">
                                Se creará conexión <strong>{{ strtoupper($proto) }}</strong> en Guacamole automáticamente
                            </span>
                        </div>
                    </div>

                </div>
            </div>

            {{-- ── Direcciones IP ── --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                        Direcciones IP
                    </h2>
                    <button type="button" onclick="addIpRow()"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium
                                   text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/30
                                   rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Agregar IP
                    </button>
                </div>
                <div class="p-6">
                    <div id="ip-rows" class="space-y-3">
                        @php $existingIps = old('ips', $server->ips?->toArray() ?? []); @endphp
                        @forelse($existingIps as $i => $ip)
                        <div class="ip-row flex items-center gap-3">
                            <input type="text" name="ips[{{ $i }}][ip_address]"
                                   value="{{ $ip['ip_address'] ?? '' }}"
                                   placeholder="192.168.x.x"
                                   class="flex-1 rounded-lg border-gray-300 dark:border-gray-600
                                          dark:bg-gray-700 dark:text-white font-mono
                                          focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <select name="ips[{{ $i }}][type]"
                                    class="rounded-lg border-gray-300 dark:border-gray-600
                                           dark:bg-gray-700 dark:text-white
                                           focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="private" {{ ($ip['type'] ?? 'private') === 'private' ? 'selected' : '' }}>Privada</option>
                                <option value="public"  {{ ($ip['type'] ?? '') === 'public'  ? 'selected' : '' }}>Pública</option>
                            </select>
                            <input type="text" name="ips[{{ $i }}][interface]"
                                   value="{{ $ip['interface'] ?? '' }}"
                                   placeholder="eth0"
                                   class="w-24 rounded-lg border-gray-300 dark:border-gray-600
                                          dark:bg-gray-700 dark:text-white font-mono
                                          focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <label class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap cursor-pointer">
                                <input type="checkbox" name="ips[{{ $i }}][is_primary]" value="1"
                                       {{ !empty($ip['is_primary']) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                Principal
                            </label>
                            <button type="button" onclick="this.closest('.ip-row').remove()"
                                    class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-lg
                                           text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/30 transition-all">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        @empty
                        <p class="text-sm text-gray-400 dark:text-gray-500 text-center py-4">
                            Sin IPs registradas. Haz clic en "Agregar IP".
                        </p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Botones --}}
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('admin.servers.index') }}"
                   class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300
                          bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600
                          rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                    Cancelar
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2 text-sm font-medium
                               text-white bg-blue-600 rounded-lg hover:bg-blue-700
                               transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    {{ isset($server->id) ? 'Actualizar' : 'Registrar Servidor' }}
                </button>
            </div>

        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
// ── Host type: manejado por Alpine.js (x-data="{ hostType }") ──────────

let ipIndex = {{ count(old('ips', $server->ips?->toArray() ?? [])) }};

function addIpRow() {
    const container = document.getElementById('ip-rows');

    // Quitar mensaje vacío si existe
    const empty = container.querySelector('p');
    if (empty) empty.remove();

    const div = document.createElement('div');
    div.className = 'ip-row flex items-center gap-3';
    div.innerHTML = `
        <input type="text" name="ips[${ipIndex}][ip_address]" placeholder="192.168.x.x"
               class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                      dark:text-white font-mono focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
        <select name="ips[${ipIndex}][type]"
                class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                       dark:text-white focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            <option value="private">Privada</option>
            <option value="public">Pública</option>
        </select>
        <input type="text" name="ips[${ipIndex}][interface]" placeholder="eth0"
               class="w-24 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                      dark:text-white font-mono focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
        <label class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap cursor-pointer">
            <input type="checkbox" name="ips[${ipIndex}][is_primary]" value="1"
                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
            Principal
        </label>
        <button type="button" onclick="this.closest('.ip-row').remove()"
                class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-lg
                       text-gray-400 hover:text-red-500 hover:bg-red-50 transition-all">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>`;
    container.appendChild(div);
    div.querySelector('input[type=text]').focus();
    ipIndex++;
}

// ── Detección de protocolo según OS ─────────────────────────────────
(function () {
    const osInput    = document.getElementById('operating_system');
    const badge      = document.getElementById('proto-badge');
    const labelUser  = document.getElementById('label-user');
    const userInput  = document.getElementById('ssh_user');
    const labelPort  = document.getElementById('label-port');
    const portInput  = document.getElementById('rdp_port');
    const portSuffix = document.getElementById('port-suffix');
    const portHint   = document.getElementById('port-hint');
    const guacInfo   = document.getElementById('guac-info');
    const guacText   = document.getElementById('guac-info-text');

    function applyProtocol(os) {
        const isWin = /windows/i.test(os);
        const proto = isWin ? 'rdp' : 'ssh';
        const port  = isWin ? 3389 : 22;

        // Badge
        badge.textContent = proto.toUpperCase();
        badge.className = badge.className
            .replace(/bg-\w+-100|text-\w+-700|dark:bg-\w+-900\/40|dark:text-\w+-300/g, '').trim();
        if (isWin) {
            badge.classList.add('bg-blue-100','text-blue-700','dark:bg-blue-900/40','dark:text-blue-300');
        } else {
            badge.classList.add('bg-emerald-100','text-emerald-700','dark:bg-emerald-900/40','dark:text-emerald-300');
        }

        // Labels y placeholders
        labelUser.textContent = isWin ? 'Usuario Windows' : 'Usuario SSH';
        userInput.placeholder = isWin ? 'Administrator' : 'root';
        labelPort.textContent = `Puerto ${proto.toUpperCase()}`;
        portSuffix.textContent = proto.toUpperCase();
        portSuffix.className = portSuffix.className
            .replace(/text-\w+-400/g, '').trim() + (isWin ? ' text-blue-400' : ' text-emerald-400');
        portHint.textContent = isWin ? 'Puerto RDP por defecto: 3389' : 'Puerto SSH por defecto: 22';

        // Solo cambiar el puerto si el usuario no lo ha tocado o está vacío
        if (!portInput.dataset.touched) {
            portInput.value       = port;
            portInput.placeholder = port;
        }

        // Info Guacamole
        guacText.innerHTML = `Se creará conexión <strong>${proto.toUpperCase()}</strong> en Guacamole automáticamente`;
        guacInfo.className = guacInfo.className
            .replace(/bg-\w+-50|dark:bg-\w+-900\/20|text-\w+-700|dark:text-\w+-300/g, '').trim();
        if (isWin) {
            guacInfo.classList.add('bg-blue-50','dark:bg-blue-900/20','text-blue-700','dark:text-blue-300');
        } else {
            guacInfo.classList.add('bg-emerald-50','dark:bg-emerald-900/20','text-emerald-700','dark:text-emerald-300');
        }
    }

    // Marcar si el usuario toca el puerto manualmente
    portInput.addEventListener('input', () => { portInput.dataset.touched = '1'; });

    // Escuchar cambios en el campo OS
    osInput.addEventListener('input', () => applyProtocol(osInput.value));
})();

function toggleActive() {
    const btn   = document.getElementById('toggle-active');
    const thumb = document.getElementById('toggle-thumb');
    const input = document.getElementById('is_active');
    const active = input.value === '1';

    input.value = active ? '0' : '1';
    btn.classList.toggle('bg-emerald-500', !active);
    btn.classList.toggle('bg-gray-300',    active);
    btn.classList.toggle('dark:bg-gray-600', active);
    thumb.classList.toggle('translate-x-6', !active);
    thumb.classList.toggle('translate-x-1',  active);
}
</script>
@endpush
