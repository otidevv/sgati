@extends('layouts.app')

@section('title', $server->name)

@section('content')
<div class="space-y-6">

    {{-- Encabezado --}}
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
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
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $server->name }}</h1>
                    @if($server->is_active)
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium
                                 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>Activo
                    </span>
                    @else
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium
                                 bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400">
                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>Inactivo
                    </span>
                    @endif
                </div>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ $server->operating_system ?? 'SO no especificado' }}
                    @if($server->function) · {{ $server->function->label() }} @endif
                    @if($server->host_type === 'cloud' && $server->cloud_provider)
                    · {{ strtoupper($server->cloud_provider) }}
                    @if($server->cloud_region) ({{ $server->cloud_region }}) @endif
                    @endif
                </p>
            </div>
        </div>
        <div class="flex items-center gap-2 flex-wrap">

            {{-- ── Guacamole ── --}}
            @if($server->guacamole_connection_id)
            {{-- Conectar --}}
            <button type="button"
                    id="btn-guac-connect"
                    onclick="guacConnect(this, '{{ route('admin.servers.connect', $server) }}')"
                    title="Abrir escritorio remoto en nueva pestaña"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium
                           text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                Conectar
            </button>
            {{-- Restablecer --}}
            <button type="button"
                    id="btn-guac-reset"
                    onclick="guacReconnect(this, '{{ route('admin.servers.reconnect', $server) }}')"
                    title="Elimina la conexión actual en Guacamole y crea una nueva"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium
                           text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/30
                           border border-amber-200 dark:border-amber-700
                           hover:bg-amber-100 dark:hover:bg-amber-900/50 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Restablecer
            </button>
            @else
            {{-- Sin conexión: CTA para habilitar acceso remoto --}}
            <button type="button"
                    id="btn-guac-reset"
                    onclick="guacReconnect(this, '{{ route('admin.servers.reconnect', $server) }}', true)"
                    title="Registrar este servidor en Guacamole para habilitar el acceso remoto"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium
                           text-emerald-700 dark:text-emerald-400
                           bg-emerald-50 dark:bg-emerald-900/20
                           border border-emerald-200 dark:border-emerald-700/60
                           hover:bg-emerald-100 dark:hover:bg-emerald-900/40 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                Habilitar acceso remoto
            </button>
            @endif

            {{-- Editar --}}
            <a href="{{ route('admin.servers.edit', $server) }}"
               class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium
                      text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                </svg>
                Editar
            </a>
        </div>
    </div>

    {{-- Tarjetas resumen --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        @foreach([
            ['label' => 'IPs',          'value' => $server->ips->count(),              'color' => 'text-slate-700 dark:text-slate-300'],
            ['label' => 'Sistemas',     'value' => $server->deployments->count(),       'color' => 'text-blue-600 dark:text-blue-400'],
            ['label' => 'Contenedores', 'value' => $server->activeContainers->count(),  'color' => 'text-indigo-600 dark:text-indigo-400'],
            ['label' => 'Motores BD',   'value' => $server->databaseServers->count(),   'color' => 'text-violet-600 dark:text-violet-400'],
        ] as $card)
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 px-4 py-3">
            <p class="text-[10px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">{{ $card['label'] }}</p>
            <p class="mt-1 text-2xl font-bold {{ $card['color'] }}">{{ $card['value'] }}</p>
        </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- ── Columna izquierda ── --}}
        <div class="space-y-5">

            {{-- Info general --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="px-5 py-3.5 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Información General</h3>
                </div>
                <dl class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach([
                        ['label' => 'Tipo',    'value' => match($server->host_type ?? 'physical') { 'physical' => 'Físico', 'virtual' => 'Virtual (VM)', 'cloud' => 'Nube', default => '—' }],
                        ['label' => 'SO',      'value' => $server->operating_system],
                        ['label' => 'Función', 'value' => $server->function?->label()],
                        ['label' => 'Web Root','value' => $server->web_root],
                        ['label' => 'SSH User','value' => $server->ssh_user],
                    ] as $row)
                    <div class="flex items-center justify-between px-5 py-2.5">
                        <dt class="text-xs text-gray-500 dark:text-gray-400">{{ $row['label'] }}</dt>
                        <dd class="text-sm font-medium text-gray-800 dark:text-gray-200 font-mono text-right max-w-[170px] truncate">
                            {{ $row['value'] ?? '—' }}
                        </dd>
                    </div>
                    @endforeach
                </dl>
            </div>

            {{-- Recursos --}}
            @if($server->cpu_cores || $server->ram_gb || $server->storage_gb)
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="px-5 py-3.5 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Recursos</h3>
                </div>
                <div class="grid grid-cols-3 divide-x divide-gray-100 dark:divide-gray-700">
                    <div class="px-3 py-4 text-center">
                        <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $server->cpu_cores ?? '—' }}</p>
                        <p class="text-[10px] text-gray-400 mt-0.5 uppercase tracking-wider">Cores</p>
                    </div>
                    <div class="px-3 py-4 text-center">
                        <p class="text-xl font-bold text-blue-600 dark:text-blue-400">{{ $server->ram_gb ? $server->ram_gb.'GB' : '—' }}</p>
                        <p class="text-[10px] text-gray-400 mt-0.5 uppercase tracking-wider">RAM</p>
                    </div>
                    <div class="px-3 py-4 text-center">
                        <p class="text-xl font-bold text-indigo-600 dark:text-indigo-400">
                            @if($server->storage_gb)
                                {{ $server->storage_gb >= 1024 ? round($server->storage_gb/1024,1).'TB' : $server->storage_gb.'GB' }}
                            @else —
                            @endif
                        </p>
                        <p class="text-[10px] text-gray-400 mt-0.5 uppercase tracking-wider">Storage</p>
                    </div>
                </div>
            </div>
            @endif

            {{-- Cloud --}}
            @if($server->host_type === 'cloud' && $server->cloud_provider)
            @php $cloudLabels = ['aws'=>'Amazon AWS','gcp'=>'Google Cloud','azure'=>'Microsoft Azure','digitalocean'=>'DigitalOcean','linode'=>'Linode','other'=>'Otro']; @endphp
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="px-5 py-3.5 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Nube</h3>
                </div>
                <dl class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach([
                        ['label' => 'Proveedor', 'value' => $cloudLabels[$server->cloud_provider] ?? $server->cloud_provider],
                        ['label' => 'Región',    'value' => $server->cloud_region],
                        ['label' => 'Instancia', 'value' => $server->cloud_instance],
                    ] as $row)
                    <div class="flex items-center justify-between px-5 py-2.5">
                        <dt class="text-xs text-gray-500 dark:text-gray-400">{{ $row['label'] }}</dt>
                        <dd class="text-sm font-mono text-gray-800 dark:text-gray-200">{{ $row['value'] ?? '—' }}</dd>
                    </div>
                    @endforeach
                </dl>
            </div>
            @endif

            {{-- IPs --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="px-5 py-3.5 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Direcciones IP</h3>
                </div>
                @if($server->ips->isEmpty())
                <p class="text-sm text-gray-400 text-center py-6">Sin IPs registradas</p>
                @else
                <div class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($server->ips as $ip)
                    <div class="flex items-center justify-between px-5 py-2.5">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full {{ $ip->type === 'public' ? 'bg-blue-400' : 'bg-slate-400' }}"></span>
                            <span class="text-sm font-mono text-gray-800 dark:text-gray-200">{{ $ip->ip_address }}</span>
                            @if($ip->is_primary)
                            <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400">PRINCIPAL</span>
                            @endif
                        </div>
                        <div class="flex items-center gap-2 text-xs text-gray-400">
                            <span>{{ $ip->type === 'public' ? 'Pública' : 'Privada' }}</span>
                            @if($ip->interface)<span class="font-mono">{{ $ip->interface }}</span>@endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Servicios instalados --}}
            @if($server->installed_services)
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="px-5 py-3.5 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Servicios Instalados</h3>
                </div>
                <div class="px-5 py-4 flex flex-wrap gap-2">
                    @foreach($server->installed_services as $svc)
                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium
                                 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300">
                        {{ $svc }}
                    </span>
                    @endforeach
                </div>
            </div>
            @endif

            @if($server->notes)
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-5">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Notas</h3>
                <p class="text-sm text-gray-600 dark:text-gray-300 whitespace-pre-line">{{ $server->notes }}</p>
            </div>
            @endif
        </div>

        {{-- ── Columna derecha ── --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Sistemas alojados --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="px-5 py-3.5 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                        Sistemas Alojados <span class="ml-1 text-xs font-normal text-gray-400">({{ $server->deployments->count() }})</span>
                    </h3>
                </div>
                @if($server->deployments->isEmpty())
                <p class="text-sm text-gray-400 text-center py-8">Sin sistemas desplegados</p>
                @else
                <div class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($server->deployments as $dep)
                    <div class="flex items-center justify-between px-5 py-3.5">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600
                                        flex items-center justify-center text-white text-xs font-bold">
                                {{ strtoupper(substr($dep->system->acronym ?? $dep->system->name, 0, 2)) }}
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $dep->system->name }}</p>
                                @if($dep->system_url)
                                <a href="{{ $dep->system_url }}" target="_blank"
                                   class="text-xs text-blue-500 hover:underline truncate max-w-[200px] block">
                                    {{ $dep->system_url }}
                                </a>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            @if($dep->environment)
                            <span class="text-xs px-2 py-0.5 rounded-md bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                                {{ $dep->environment->label() }}
                            </span>
                            @endif
                            <a href="{{ route('systems.show', $dep->system) }}"
                               class="text-xs text-blue-600 dark:text-blue-400 hover:underline">Ver →</a>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- ── Contenedores Docker ── --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="px-5 py-3.5 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                        Contenedores <span class="ml-1 text-xs font-normal text-gray-400">({{ $server->activeContainers->count() }})</span>
                    </h3>
                    <button onclick="openModal('modal-container')"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium
                                   text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/30
                                   rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Agregar
                    </button>
                </div>

                @if($server->activeContainers->isEmpty())
                <p class="text-sm text-gray-400 text-center py-8">Sin contenedores registrados</p>
                @else
                @php
                    $typeColors = ['frontend'=>'blue','backend'=>'indigo','database'=>'violet','cache'=>'amber','queue'=>'orange','proxy'=>'slate','storage'=>'teal','other'=>'gray'];
                @endphp
                <div class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($server->activeContainers as $container)
                    @php $c = $typeColors[$container->type] ?? 'gray'; @endphp
                    <div class="flex items-center justify-between px-5 py-3">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                         bg-{{ $c }}-100 dark:bg-{{ $c }}-900/30
                                         text-{{ $c }}-700 dark:text-{{ $c }}-400 capitalize w-20 justify-center">
                                {{ $container->type }}
                            </span>
                            <div>
                                <p class="text-sm font-mono font-medium text-gray-900 dark:text-white">{{ $container->name }}</p>
                                <p class="text-xs text-gray-400">{{ $container->image ?? '—' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="text-right">
                                @if($container->port_mapping)
                                <span class="text-xs font-mono text-gray-500 dark:text-gray-400">:{{ $container->external_port }}→{{ $container->internal_port }}</span>
                                @endif
                                @if($container->system)
                                <p class="text-xs text-gray-400">{{ $container->system->acronym ?? $container->system->name }}</p>
                                @endif
                            </div>
                            <div class="flex items-center gap-1">
                                <button onclick="editContainer({{ $container->id }}, {{ $container->toJson() }})"
                                        class="w-7 h-7 flex items-center justify-center rounded-lg text-gray-400
                                               hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-all">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                    </svg>
                                </button>
                                <form action="{{ route('admin.servers.containers.destroy', [$server, $container]) }}"
                                      method="POST" id="del-cnt-{{ $container->id }}" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="button"
                                            onclick="dtConfirmDelete('del-cnt-{{ $container->id }}', '{{ addslashes($container->name) }}')"
                                            class="w-7 h-7 flex items-center justify-center rounded-lg text-gray-400
                                                   hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 transition-all">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- ── Motores de BD ── --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="px-5 py-3.5 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                        Motores de Base de Datos <span class="ml-1 text-xs font-normal text-gray-400">({{ $server->databaseServers->count() }})</span>
                    </h3>
                    <button onclick="openModal('modal-dbserver')"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium
                                   text-violet-600 dark:text-violet-400 bg-violet-50 dark:bg-violet-900/30
                                   rounded-lg hover:bg-violet-100 dark:hover:bg-violet-900/50 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Agregar
                    </button>
                </div>

                @if($server->databaseServers->isEmpty())
                <p class="text-sm text-gray-400 text-center py-8">Sin motores de BD registrados</p>
                @else
                @foreach($server->databaseServers as $dbServer)
                <div class="border-b border-gray-100 dark:border-gray-700 last:border-0">
                    <div class="flex items-center justify-between px-5 py-3 bg-gray-50 dark:bg-gray-700/40">
                        <div class="flex items-center gap-3">
                            <span class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $dbServer->engine_label }}</span>
                            <span class="text-xs font-mono text-gray-500 dark:text-gray-400">{{ $dbServer->connection_string }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-gray-400">{{ $dbServer->databases->count() }} BD(s)</span>
                            <button onclick="editDbServer({{ $dbServer->id }}, {{ $dbServer->toJson() }})"
                                    class="w-7 h-7 flex items-center justify-center rounded-lg text-gray-400
                                           hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-all">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                </svg>
                            </button>
                            <form action="{{ route('admin.servers.database-servers.destroy', [$server, $dbServer]) }}"
                                  method="POST" id="del-db-{{ $dbServer->id }}" class="inline">
                                @csrf @method('DELETE')
                                <button type="button"
                                        onclick="dtConfirmDelete('del-db-{{ $dbServer->id }}', '{{ addslashes($dbServer->engine_label) }}')"
                                        class="w-7 h-7 flex items-center justify-center rounded-lg text-gray-400
                                               hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 transition-all">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                    @foreach($dbServer->databases as $db)
                    <div class="flex items-center justify-between px-5 py-2.5 pl-10">
                        <div class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-violet-400 flex-shrink-0"></span>
                            <span class="text-sm font-mono text-gray-700 dark:text-gray-300">{{ $db->db_name }}</span>
                            @if($db->db_user)<span class="text-xs text-gray-400 font-mono">/ {{ $db->db_user }}</span>@endif
                        </div>
                        @if($db->system)
                        <span class="text-xs text-gray-500">{{ $db->system->acronym ?? $db->system->name }}</span>
                        @endif
                    </div>
                    @endforeach
                </div>
                @endforeach
                @endif
            </div>
            {{-- ── Responsables ── --}}
            @php
                $levelColors = [
                    'principal'   => 'bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300',
                    'soporte'     => 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300',
                    'supervision' => 'bg-violet-100 dark:bg-violet-900/40 text-violet-700 dark:text-violet-300',
                    'operador'    => 'bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300',
                ];
                $levelLabels = ['principal'=>'Principal','soporte'=>'Soporte','supervision'=>'Supervisión','operador'=>'Operador'];
                $docLabels   = ['resolucion_directoral'=>'R.D.','resolucion_jefatural'=>'R.J.','memorando'=>'Memo.','oficio'=>'Oficio','contrato'=>'Contrato','acta'=>'Acta','otro'=>'Doc.'];
            @endphp
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="px-5 py-3.5 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Responsables</h3>
                        @if($server->responsibles->count())
                            <span class="text-xs text-gray-400 dark:text-gray-500">({{ $server->responsibles->count() }})</span>
                        @endif
                    </div>
                    <button onclick="openModal('modal-responsible')"
                            class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium
                                   text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/30
                                   rounded-lg hover:bg-emerald-100 dark:hover:bg-emerald-900/50 transition-colors">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Agregar
                    </button>
                </div>

                @php
                    $avatarColors = [
                        'principal'   => 'bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300',
                        'soporte'     => 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300',
                        'supervision' => 'bg-violet-100 dark:bg-violet-900/50 text-violet-700 dark:text-violet-300',
                        'operador'    => 'bg-amber-100 dark:bg-amber-900/50 text-amber-700 dark:text-amber-300',
                    ];
                    $leftBorder = [
                        'principal'   => 'border-blue-400 dark:border-blue-500',
                        'soporte'     => 'border-slate-300 dark:border-slate-500',
                        'supervision' => 'border-violet-400 dark:border-violet-500',
                        'operador'    => 'border-amber-400 dark:border-amber-500',
                    ];
                @endphp
                @if($server->responsibles->isEmpty())
                <div class="flex flex-col items-center justify-center py-10 text-center">
                    <div class="w-12 h-12 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mb-3">
                        <svg class="w-6 h-6 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Sin responsables asignados</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Usa el botón "Agregar" para asignar uno</p>
                </div>
                @else
                <div class="divide-y divide-gray-100 dark:divide-gray-700/60">
                    @foreach($server->responsibles as $resp)
                    @php
                        $initials = strtoupper(substr($resp->persona->apellido_paterno, 0, 1) . substr($resp->persona->apellido_materno ?? '', 0, 1));
                        $borderClass = $leftBorder[$resp->level] ?? 'border-gray-300';
                        $avatarClass = $avatarColors[$resp->level] ?? 'bg-gray-100 text-gray-600';
                    @endphp
                    <div class="px-4 py-3 border-l-[3px] {{ $borderClass }} {{ !$resp->is_active ? 'opacity-60' : '' }} hover:bg-gray-50/60 dark:hover:bg-gray-700/30 transition-colors group/row">
                        <div class="flex items-start gap-3">
                            {{-- Avatar iniciales --}}
                            <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0 text-xs font-bold mt-0.5 {{ $avatarClass }}">
                                {{ $initials }}
                            </div>
                            {{-- Datos --}}
                            <div class="flex-1 min-w-0">
                                {{-- Fila 1: nombre + acciones al hover --}}
                                <div class="flex items-center gap-1.5 min-w-0">
                                    <span class="text-sm font-semibold text-gray-800 dark:text-gray-100 leading-snug truncate flex-1 min-w-0">
                                        {{ $resp->persona->apellido_paterno }} {{ $resp->persona->apellido_materno }},
                                        <span class="font-normal text-gray-600 dark:text-gray-300">{{ $resp->persona->nombres }}</span>
                                    </span>
                                    {{-- Acciones inline --}}
                                    <div class="flex items-center gap-0.5 flex-shrink-0 opacity-0 group-hover/row:opacity-100 transition-opacity">
                                        <button onclick="openDocUpload({{ $resp->id }})"
                                                title="Adjuntar documento"
                                                class="w-6 h-6 flex items-center justify-center rounded-md text-gray-400 dark:text-gray-500
                                                       hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                            </svg>
                                        </button>
                                        <button onclick="editResponsible({{ $resp->id }}, {{ $resp->toJson() }})"
                                                title="Editar"
                                                class="w-6 h-6 flex items-center justify-center rounded-md text-gray-400 dark:text-gray-500
                                                       hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                            </svg>
                                        </button>
                                        <form action="{{ route('admin.servers.responsibles.destroy', [$server, $resp]) }}"
                                              method="POST" id="del-resp-{{ $resp->id }}" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="button"
                                                    title="Eliminar"
                                                    onclick="dtConfirmDelete('del-resp-{{ $resp->id }}', '{{ addslashes($resp->persona->apellido_paterno . ' ' . $resp->persona->nombres) }}')"
                                                    class="w-6 h-6 flex items-center justify-center rounded-md text-gray-400 dark:text-gray-500
                                                           hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                {{-- Fila 2: badges --}}
                                <div class="flex items-center gap-2 mt-1 flex-wrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold uppercase tracking-wide {{ $levelColors[$resp->level] ?? '' }}">
                                        {{ $levelLabels[$resp->level] ?? $resp->level }}
                                    </span>
                                    @if(!$resp->is_active)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400">
                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400 dark:bg-gray-500"></span>
                                        Inactivo
                                    </span>
                                    @else
                                    <span class="inline-flex items-center gap-1 text-[10px] text-emerald-600 dark:text-emerald-400">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 dark:bg-emerald-400"></span>
                                        Activo
                                    </span>
                                    @endif
                                    <span class="text-[11px] text-gray-400 dark:text-gray-500 flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        Desde {{ $resp->assigned_at->format('d/m/Y') }}
                                    </span>
                                    @if($resp->document_type)
                                    <div class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md
                                                bg-indigo-50 dark:bg-indigo-900/30 border border-indigo-200 dark:border-indigo-700/50
                                                text-[11px] text-indigo-600 dark:text-indigo-400">
                                        <svg class="w-3 h-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <span class="font-medium">{{ $docLabels[$resp->document_type] ?? $resp->document_type }}</span>
                                        @if($resp->document_number)<span class="text-indigo-300 dark:text-indigo-600">·</span> {{ $resp->document_number }}@endif
                                        @if($resp->document_date)<span class="text-indigo-300 dark:text-indigo-600">·</span> {{ $resp->document_date->format('d/m/Y') }}@endif
                                    </div>
                                    @endif
                                </div>
                                {{-- Documentos adjuntos --}}
                                @if($resp->documents->count())
                                <div class="mt-2 flex flex-wrap gap-1.5">
                                    @foreach($resp->documents as $doc)
                                    @php $docExt = strtolower(pathinfo($doc->original_name, PATHINFO_EXTENSION)); @endphp
                                    <div class="inline-flex items-center gap-1 pl-2 pr-1 py-0.5 rounded-full
                                                bg-indigo-50 dark:bg-indigo-900/30 border border-indigo-200 dark:border-indigo-700
                                                text-indigo-700 dark:text-indigo-300 text-[11px] font-medium max-w-xs">
                                        <svg class="w-3 h-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                        </svg>
                                        <button type="button"
                                                onclick='openDocPreview({
                                                    name:        {{ json_encode($doc->description ?: $doc->original_name) }},
                                                    description: {{ json_encode($doc->description ? $doc->original_name : null) }},
                                                    previewUrl:  "{{ route('admin.servers.responsibles.documents.preview', [$server, $resp, $doc]) }}",
                                                    downloadUrl: "{{ route('admin.servers.responsibles.documents.download', [$server, $resp, $doc]) }}",
                                                    ext:         {{ json_encode($docExt) }}
                                                })'
                                                title="Previsualizar {{ $doc->original_name }}"
                                                class="truncate max-w-[160px] hover:underline cursor-pointer">
                                            {{ $doc->description ?: $doc->original_name }}
                                        </button>
                                        <form action="{{ route('admin.servers.responsibles.documents.destroy', [$server, $resp, $doc]) }}"
                                              method="POST" class="inline" onsubmit="sgDeleteForm(this,'¿Eliminar este documento?');return false">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                    class="ml-0.5 w-4 h-4 flex items-center justify-center rounded-full
                                                           text-indigo-400 hover:text-red-500 hover:bg-red-100 dark:hover:bg-red-900/40 transition-colors">
                                                <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                    @endforeach
                                </div>
                                @endif
                            </div>{{-- /datos --}}
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ════════════════════════════════════════════════
     MODAL: Responsable
════════════════════════════════════════════════ --}}
<div id="modal-responsible"
     class="hidden fixed inset-0 z-50 items-center justify-center p-4"
     role="dialog" aria-modal="true">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeModal('modal-responsible')"></div>
    <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 id="resp-modal-title" class="text-base font-semibold text-gray-900 dark:text-white">
                Asignar Responsable
            </h3>
            <button onclick="closeModal('modal-responsible')"
                    class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400
                           hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form id="resp-form"
              action="{{ route('admin.servers.responsibles.store', $server) }}"
              method="POST">
            @csrf
            <span id="resp-method"></span>

            <div class="p-6 space-y-4">

                {{-- Persona --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Persona <span class="text-red-500">*</span>
                    </label>
                    <select name="persona_id" id="resp-persona_id" required
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                   dark:text-white focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                        <option value="">— Seleccionar —</option>
                        @foreach($personas as $persona)
                        <option value="{{ $persona->id }}">
                            {{ $persona->apellido_paterno }} {{ $persona->apellido_materno }}, {{ $persona->nombres }}
                            ({{ $persona->dni }})
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    {{-- Nivel --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Nivel <span class="text-red-500">*</span>
                        </label>
                        <select name="level" id="resp-level" required
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                       dark:text-white focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                            <option value="principal">Responsable Principal</option>
                            <option value="soporte">Soporte Técnico</option>
                            <option value="supervision">Supervisión</option>
                            <option value="operador">Operador</option>
                        </select>
                    </div>

                    {{-- Fecha asignación --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Fecha de asignación <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="assigned_at" id="resp-assigned_at" required
                               value="{{ now()->format('Y-m-d') }}"
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                      dark:text-white focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                    </div>
                </div>

                {{-- Activo --}}
                <div class="flex items-center gap-3">
                    <input type="checkbox" name="is_active" id="resp-is_active" value="1"
                           checked class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                    <label for="resp-is_active" class="text-sm text-gray-700 dark:text-gray-300 cursor-pointer">
                        Asignación activa
                    </label>
                </div>

                {{-- Separador documento --}}
                <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                    <p class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider mb-3 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Documento de respaldo <span class="font-normal text-gray-400 normal-case">(opcional)</span>
                    </p>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Tipo</label>
                            <select name="document_type" id="resp-document_type"
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                           dark:text-white focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="">Sin documento</option>
                                <option value="resolucion_directoral">Resolución Directoral</option>
                                <option value="resolucion_jefatural">Resolución Jefatural</option>
                                <option value="memorando">Memorando</option>
                                <option value="oficio">Oficio</option>
                                <option value="contrato">Contrato</option>
                                <option value="acta">Acta</option>
                                <option value="otro">Otro</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">N° de documento</label>
                            <input type="text" name="document_number" id="resp-document_number"
                                   placeholder="R.D. N°042-2024-OTI"
                                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                          dark:text-white focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Fecha del documento</label>
                            <input type="date" name="document_date" id="resp-document_date"
                                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                          dark:text-white focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Observaciones</label>
                            <input type="text" name="document_notes" id="resp-document_notes"
                                   placeholder="Notas adicionales..."
                                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                          dark:text-white focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                    </div>
                </div>

            </div>

            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                <button type="button" onclick="closeModal('modal-responsible')"
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700
                               border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                    Cancelar
                </button>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2 text-sm font-medium text-white
                               bg-emerald-600 rounded-lg hover:bg-emerald-700 transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span id="resp-submit-label">Asignar</span>
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ════════════════════════════════════════════════
     MODAL: Adjuntar Documento a Responsable
════════════════════════════════════════════════ --}}
<div id="modal-doc-upload"
     class="hidden fixed inset-0 z-50 items-center justify-center p-4"
     role="dialog" aria-modal="true">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeModal('modal-doc-upload')"></div>
    <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                </svg>
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Adjuntar Documento</h3>
            </div>
            <button onclick="closeModal('modal-doc-upload')"
                    class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400
                           hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form id="doc-upload-form" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="p-6 space-y-4">

                {{-- Drop zone --}}
                <div x-data="{ dragging: false }"
                     @dragover.prevent="dragging = true"
                     @dragleave.prevent="dragging = false"
                     @drop.prevent="dragging = false; $refs.fileInput.files = $event.dataTransfer.files; updateFileName($event.dataTransfer.files[0]?.name)"
                     :class="dragging ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20' : 'border-gray-300 dark:border-gray-600 hover:border-indigo-400 dark:hover:border-indigo-500'"
                     class="border-2 border-dashed rounded-xl p-6 text-center transition-colors cursor-pointer"
                     onclick="document.getElementById('doc-file-input').click()">
                    <svg class="w-10 h-10 mx-auto text-gray-300 dark:text-gray-600 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    <p id="doc-file-label" class="text-sm text-gray-500 dark:text-gray-400">
                        Arrastra el archivo aquí o <span class="text-indigo-600 dark:text-indigo-400 font-medium">haz clic para seleccionar</span>
                    </p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">PDF, Word, Excel, imagen · Máx. 10 MB</p>
                    <input id="doc-file-input" x-ref="fileInput" type="file" name="file"
                           accept=".pdf,.doc,.docx,.xls,.xlsx,.png,.jpg,.jpeg"
                           class="hidden" required
                           onchange="updateFileName(this.files[0]?.name)">
                </div>

                {{-- Descripción --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Descripción <span class="text-gray-400 font-normal">(opcional)</span>
                    </label>
                    <input type="text" name="description" id="doc-description"
                           placeholder="Ej: Resolución de nombramiento 2024"
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                  dark:text-white focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>

            </div>

            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 rounded-b-2xl">
                <button type="button" onclick="closeModal('modal-doc-upload')"
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700
                               border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                    Cancelar
                </button>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2 text-sm font-medium text-white
                               bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    Subir documento
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ════════════════════════════════════════════════
     MODAL: Contenedor Docker
════════════════════════════════════════════════ --}}
<div id="modal-container"
     class="hidden fixed inset-0 z-50 items-center justify-center p-4"
     role="dialog" aria-modal="true">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeModal('modal-container')"></div>
    <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 id="cnt-modal-title" class="text-base font-semibold text-gray-900 dark:text-white">
                Agregar Contenedor
            </h3>
            <button onclick="closeModal('modal-container')"
                    class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400
                           hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form id="cnt-form"
              action="{{ route('admin.servers.containers.store', $server) }}"
              method="POST">
            @csrf
            <span id="cnt-method"></span>
            <div class="p-6 space-y-4">

                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Nombre <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="cnt-name" required
                               placeholder="cepre-frontend"
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                      dark:text-white font-mono focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Imagen
                        </label>
                        <input type="text" name="image" id="cnt-image"
                               placeholder="nginx:alpine"
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                      dark:text-white font-mono focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Tipo <span class="text-red-500">*</span>
                        </label>
                        <select name="type" id="cnt-type" required
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                       dark:text-white focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            @foreach(['frontend'=>'Frontend','backend'=>'Backend','database'=>'Base de Datos','cache'=>'Cache','queue'=>'Queue/Worker','proxy'=>'Proxy/Nginx','storage'=>'Storage','other'=>'Otro'] as $v => $l)
                            <option value="{{ $v }}">{{ $l }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Sistema</label>
                        <select name="system_id" id="cnt-system"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                       dark:text-white focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">— Compartido —</option>
                            @foreach(\App\Models\System::orderBy('name')->get() as $sys)
                            <option value="{{ $sys->id }}">{{ $sys->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Puerto Externo</label>
                        <input type="number" name="external_port" id="cnt-ext-port" min="1" max="65535"
                               placeholder="8080"
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                      dark:text-white font-mono focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Puerto Interno</label>
                        <input type="number" name="internal_port" id="cnt-int-port" min="1" max="65535"
                               placeholder="80"
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                      dark:text-white font-mono focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Volúmenes
                            <span class="font-normal text-gray-400 text-xs ml-1">(uno por línea)</span>
                        </label>
                        <textarea name="volumes" id="cnt-volumes" rows="2"
                                  placeholder="/data/cepre:/var/www/html"
                                  class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                         dark:text-white font-mono focus:ring-blue-500 focus:border-blue-500 sm:text-sm"></textarea>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Notas</label>
                        <textarea name="notes" id="cnt-notes" rows="2"
                                  class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                         dark:text-white focus:ring-blue-500 focus:border-blue-500 sm:text-sm"></textarea>
                    </div>
                </div>

            </div>
            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                <button type="button" onclick="closeModal('modal-container')"
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300
                               bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                    Cancelar
                </button>
                <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg
                               hover:bg-blue-700 transition-colors shadow-sm">
                    Guardar
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ════════════════════════════════════════════════
     MODAL: Motor de Base de Datos
════════════════════════════════════════════════ --}}
<div id="modal-dbserver"
     class="hidden fixed inset-0 z-50 items-center justify-center p-4"
     role="dialog" aria-modal="true">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeModal('modal-dbserver')"></div>
    <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-lg">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 id="db-modal-title" class="text-base font-semibold text-gray-900 dark:text-white">
                Agregar Motor de BD
            </h3>
            <button onclick="closeModal('modal-dbserver')"
                    class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400
                           hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form id="db-form"
              action="{{ route('admin.servers.database-servers.store', $server) }}"
              method="POST">
            @csrf
            <span id="db-method"></span>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Nombre / Alias</label>
                        <input type="text" name="name" id="db-name"
                               placeholder="PostgreSQL Producción"
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                      dark:text-white focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Motor <span class="text-red-500">*</span>
                        </label>
                        <select name="engine" id="db-engine" required
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                       dark:text-white focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            @foreach(['postgresql'=>'PostgreSQL','mysql'=>'MySQL','mariadb'=>'MariaDB','oracle'=>'Oracle','sqlserver'=>'SQL Server','sqlite'=>'SQLite','mongodb'=>'MongoDB','other'=>'Otro'] as $v => $l)
                            <option value="{{ $v }}">{{ $l }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Versión</label>
                        <input type="text" name="version" id="db-version"
                               placeholder="16.2"
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                      dark:text-white font-mono focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Host</label>
                        <input type="text" name="host" id="db-host"
                               placeholder="192.168.254.5 o localhost"
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                      dark:text-white font-mono focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Puerto</label>
                        <input type="number" name="port" id="db-port" min="1" max="65535"
                               placeholder="5432"
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                      dark:text-white font-mono focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Usuario Admin</label>
                        <input type="text" name="admin_user" id="db-admin-user"
                               placeholder="postgres" autocomplete="off"
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                      dark:text-white font-mono focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Contraseña Admin
                            <span class="font-normal text-gray-400 text-xs ml-1" id="db-pass-hint">(encriptada)</span>
                        </label>
                        <input type="password" name="admin_password" id="db-admin-pass"
                               autocomplete="new-password" placeholder="••••••••"
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                      dark:text-white focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Notas</label>
                        <textarea name="notes" id="db-notes" rows="2"
                                  class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                         dark:text-white focus:ring-blue-500 focus:border-blue-500 sm:text-sm"></textarea>
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                <button type="button" onclick="closeModal('modal-dbserver')"
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300
                               bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                    Cancelar
                </button>
                <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-violet-600 rounded-lg
                               hover:bg-violet-700 transition-colors shadow-sm">
                    Guardar
                </button>
            </div>
        </form>
    </div>
</div>
{{-- Modal previsualización de documentos (reutilizable) --}}
<x-doc-preview-modal />

@endsection

@push('scripts')
<script>
// ── Guacamole ─────────────────────────────────────────────────────────
async function guacConnect(btn, url) {
    btn.disabled = true;
    const original = btn.innerHTML;
    btn.innerHTML = `<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
    </svg><span>Conectando…</span>`;

    try {
        const res  = await fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
        const data = await res.json();
        if (data.url) {
            window.open(data.url, '_blank', 'noopener,noreferrer');
        } else {
            sgToast('error', 'Error al conectar: ' + (data.error ?? 'Respuesta inesperada'));
        }
    } catch (e) {
        sgToast('error', 'No se pudo contactar con Guacamole. Verifica la configuración.');
    } finally {
        btn.disabled = false;
        btn.innerHTML = original;
    }
}

async function guacReconnect(btn, url, isNew = false) {
    const confirmed = await sgConfirm(isNew ? {
        title: '¿Habilitar acceso remoto?',
        text: 'Se registrará este servidor en Guacamole y se creará la conexión.',
        confirmButtonText: 'Sí, habilitar',
        confirmButtonColor: '#16a34a',
    } : {
        title: '¿Restablecer conexión?',
        text: 'Se eliminará la conexión actual y se creará una nueva. Las sesiones activas se cerrarán.',
        confirmButtonText: 'Sí, restablecer',
        confirmButtonColor: '#d97706',
    });
    if (!confirmed) return;

    btn.disabled = true;
    const original = btn.innerHTML;
    const loadingLabel = isNew ? 'Creando conexión…' : 'Restableciendo…';
    btn.innerHTML = `<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
    </svg><span>${loadingLabel}</span>`;

    try {
        const res  = await fetch(url, {
            method: 'POST',
            headers: {
                'Accept':           'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN':     document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
        });
        const data = await res.json();
        if (data.success) {
            location.reload();
        } else {
            sgToast('error', data.error ?? 'No se pudo restablecer la conexión.');
            btn.disabled = false;
            btn.innerHTML = original;
        }
    } catch (e) {
        sgToast('error', 'No se pudo contactar con Guacamole. Verifica la configuración.');
        btn.disabled = false;
        btn.innerHTML = original;
    }
}

// ── Helpers de modal ─────────────────────────────────────────────────
function openModal(id) {
    const m = document.getElementById(id);
    m.classList.remove('hidden');
    m.classList.add('flex');
}
function closeModal(id) {
    const m = document.getElementById(id);
    m.classList.add('hidden');
    m.classList.remove('flex');
}

// ── Responsables ─────────────────────────────────────────────────────
const respStoreUrl  = "{{ route('admin.servers.responsibles.store', $server) }}";
const respUpdateBase = "{{ url('admin/servers/' . $server->id . '/responsibles') }}/";

function editResponsible(id, data) {
    document.getElementById('resp-modal-title').textContent  = 'Editar Responsable';
    document.getElementById('resp-submit-label').textContent = 'Guardar';
    document.getElementById('resp-form').action = respUpdateBase + id;
    document.getElementById('resp-method').innerHTML = '<input type="hidden" name="_method" value="PUT">';

    document.getElementById('resp-persona_id').value     = data.persona_id     ?? '';
    document.getElementById('resp-level').value          = data.level          ?? 'soporte';
    document.getElementById('resp-assigned_at').value    = data.assigned_at    ?? '';
    document.getElementById('resp-is_active').checked    = data.is_active      == 1;
    document.getElementById('resp-document_type').value  = data.document_type  ?? '';
    document.getElementById('resp-document_number').value= data.document_number ?? '';
    document.getElementById('resp-document_date').value  = data.document_date  ?? '';
    document.getElementById('resp-document_notes').value = data.document_notes ?? '';

    openModal('modal-responsible');
}

document.getElementById('modal-responsible').addEventListener('click', function(e) {
    if (e.target === this) closeModal('modal-responsible');
});

// Reset modal al abrir para agregar
document.querySelector('[onclick="openModal(\'modal-responsible\')"]')?.addEventListener('click', function() {
    document.getElementById('resp-modal-title').textContent  = 'Asignar Responsable';
    document.getElementById('resp-submit-label').textContent = 'Asignar';
    document.getElementById('resp-form').action = respStoreUrl;
    document.getElementById('resp-method').innerHTML = '';
    document.getElementById('resp-form').reset();
    document.getElementById('resp-assigned_at').value = new Date().toISOString().slice(0, 10);
    document.getElementById('resp-is_active').checked = true;
});

// ── Documentos de Responsables ───────────────────────────────────────
const docUploadBase = "{{ url('admin/servers/' . $server->id . '/responsibles') }}/";

function openDocUpload(responsibleId) {
    document.getElementById('doc-upload-form').action = docUploadBase + responsibleId + '/documents';
    document.getElementById('doc-file-input').value  = '';
    document.getElementById('doc-description').value = '';
    document.getElementById('doc-file-label').innerHTML =
        'Arrastra el archivo aquí o <span class="text-indigo-600 dark:text-indigo-400 font-medium">haz clic para seleccionar</span>';
    openModal('modal-doc-upload');
}

function updateFileName(name) {
    if (!name) return;
    document.getElementById('doc-file-label').innerHTML =
        '<span class="font-medium text-gray-700 dark:text-gray-300">' + name + '</span>';
}

// ── Contenedores ─────────────────────────────────────────────────────
const cntStoreUrl  = "{{ route('admin.servers.containers.store', $server) }}";
const cntUpdateBase = "{{ url('admin/servers/' . $server->id . '/containers') }}/";

function editContainer(id, data) {
    document.getElementById('cnt-modal-title').textContent = 'Editar Contenedor';
    document.getElementById('cnt-form').action = cntUpdateBase + id;
    document.getElementById('cnt-method').innerHTML = '<input type="hidden" name="_method" value="PUT">';
    document.getElementById('cnt-name').value      = data.name       ?? '';
    document.getElementById('cnt-image').value     = data.image      ?? '';
    document.getElementById('cnt-type').value      = data.type       ?? 'backend';
    document.getElementById('cnt-system').value    = data.system_id  ?? '';
    document.getElementById('cnt-ext-port').value  = data.external_port ?? '';
    document.getElementById('cnt-int-port').value  = data.internal_port ?? '';
    document.getElementById('cnt-volumes').value   = (data.volumes ?? []).join('\n');
    document.getElementById('cnt-notes').value     = data.notes      ?? '';
    openModal('modal-container');
}

document.getElementById('modal-container').querySelector('button[onclick="closeModal(\'modal-container\')"]')
    ?.closest('.relative')
    ?.querySelector('.absolute')
    ?.addEventListener('click', () => {
        // reset form on close
        document.getElementById('cnt-modal-title').textContent = 'Agregar Contenedor';
        document.getElementById('cnt-form').action = cntStoreUrl;
        document.getElementById('cnt-method').innerHTML = '';
        document.getElementById('cnt-form').reset();
    });

// ── Motores de BD ────────────────────────────────────────────────────
const dbStoreUrl   = "{{ route('admin.servers.database-servers.store', $server) }}";
const dbUpdateBase = "{{ url('admin/servers/' . $server->id . '/database-servers') }}/";

function editDbServer(id, data) {
    document.getElementById('db-modal-title').textContent = 'Editar Motor de BD';
    document.getElementById('db-form').action = dbUpdateBase + id;
    document.getElementById('db-method').innerHTML = '<input type="hidden" name="_method" value="PUT">';
    document.getElementById('db-name').value       = data.name        ?? '';
    document.getElementById('db-engine').value     = data.engine      ?? 'postgresql';
    document.getElementById('db-version').value    = data.version     ?? '';
    document.getElementById('db-host').value       = data.host        ?? '';
    document.getElementById('db-port').value       = data.port        ?? '';
    document.getElementById('db-admin-user').value = data.admin_user  ?? '';
    document.getElementById('db-admin-pass').placeholder = '••••••••  (dejar vacío para no cambiar)';
    document.getElementById('db-notes').value      = data.notes       ?? '';
    openModal('modal-dbserver');
}

// Reset modal BD al cerrarse
function resetDbModal() {
    document.getElementById('db-modal-title').textContent = 'Agregar Motor de BD';
    document.getElementById('db-form').action = dbStoreUrl;
    document.getElementById('db-method').innerHTML = '';
    document.getElementById('db-admin-pass').placeholder = '••••••••';
    document.getElementById('db-form').reset();
}
</script>
@endpush
