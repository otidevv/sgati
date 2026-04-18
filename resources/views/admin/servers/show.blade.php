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
                <div class="px-5 py-3.5 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                        Direcciones IP
                        <span class="ml-1 text-xs font-normal text-gray-400">({{ $server->ips->count() }})</span>
                    </h3>
                    @can('servers.edit')
                    <button onclick="openModal('modal-ip')"
                            class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-medium rounded-lg
                                   bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400
                                   hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Agregar IP
                    </button>
                    @endcan
                </div>

                @if($server->ips->isEmpty())
                <p class="text-sm text-gray-400 text-center py-6">Sin IPs registradas</p>
                @else
                <div class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($server->ips as $ip)
                    <div class="flex items-center justify-between px-5 py-2.5 group">
                        <div class="flex items-center gap-2 min-w-0">
                            <span class="w-2 h-2 rounded-full shrink-0 {{ $ip->type === 'public' ? 'bg-blue-400' : 'bg-slate-400' }}"></span>
                            <span class="text-sm font-mono text-gray-800 dark:text-gray-200">{{ $ip->ip_address }}</span>
                            @if($ip->port)
                            <span class="text-xs font-mono text-gray-500 dark:text-gray-400">:{{ $ip->port }}</span>
                            @endif
                            @if($ip->is_primary)
                            <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400">PRINCIPAL</span>
                            @endif
                        </div>
                        <div class="flex items-center gap-3 text-xs text-gray-400">
                            <span>{{ $ip->type === 'public' ? 'Pública' : 'Privada' }}</span>
                            @if($ip->interface)<span class="font-mono">{{ $ip->interface }}</span>@endif
                            @can('servers.edit')
                            <form method="POST" action="{{ route('admin.servers.ips.destroy', [$server, $ip]) }}"
                                  onsubmit="return confirm('¿Eliminar la IP {{ $ip->ip_address }}{{ $ip->port ? ':'.$ip->port : '' }}?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="opacity-0 group-hover:opacity-100 flex items-center justify-center w-6 h-6 rounded text-gray-300 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/30 transition-all">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                            @endcan
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
                    <div class="px-5 py-3.5">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600
                                            flex items-center justify-center text-white text-xs font-bold shrink-0">
                                    {{ strtoupper(substr($dep->system->acronym ?? $dep->system->name, 0, 2)) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $dep->system->name }}</p>
                                    @if($dep->system_url)
                                    <a href="{{ str_starts_with($dep->system_url, 'http') ? $dep->system_url : 'http://'.$dep->system_url }}"
                                       target="_blank"
                                       class="text-xs text-blue-500 hover:underline truncate max-w-[200px] block">
                                        {{ $dep->system_url }}
                                    </a>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-2 shrink-0 ml-3">
                                @if($dep->environment)
                                <span class="text-xs px-2 py-0.5 rounded-md bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                                    {{ $dep->environment->label() }}
                                </span>
                                @endif
                                <a href="{{ route('systems.show', $dep->system) }}"
                                   class="text-xs text-blue-600 dark:text-blue-400 hover:underline">Ver →</a>
                            </div>
                        </div>

                        {{-- IPs del sistema --}}
                        @if($dep->serverIp || $dep->exposedIps->count())
                        <div class="mt-2 flex flex-wrap items-center gap-1.5 pl-11">
                            {{-- IP privada (conexión interna) --}}
                            @if($dep->serverIp)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs font-mono
                                         bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300
                                         border border-slate-200 dark:border-slate-600">
                                <svg class="w-3 h-3 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                {{ $dep->serverIp->ip_address }}{{ $dep->serverIp->port ? ':'.$dep->serverIp->port : '' }}
                            </span>
                            @endif
                            {{-- IPs públicas de exposición --}}
                            @foreach($dep->exposedIps as $eip)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs font-mono
                                         bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300
                                         border border-emerald-200 dark:border-emerald-700">
                                <svg class="w-3 h-3 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/>
                                </svg>
                                {{ $eip->ip_address }}{{ $eip->port ? ':'.$eip->port : ($dep->port ? ':'.$dep->port : '') }}
                            </span>
                            @endforeach
                        </div>
                        @endif
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
                            <a href="{{ route('admin.servers.database-servers.show', [$server, $dbServer]) }}"
                               title="Ver detalle"
                               class="w-7 h-7 flex items-center justify-center rounded-lg text-gray-400
                                      hover:text-violet-600 hover:bg-violet-50 dark:hover:bg-violet-900/30 transition-all">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>
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
                @php
                    $activeResponsibles   = $server->responsibles->where('is_active', true);
                    $historicalResponsibles = $server->responsibles->where('is_active', false)->sortByDesc('unassigned_at');
                @endphp

                {{-- ── Sin ningún responsable ── --}}
                @if($activeResponsibles->isEmpty() && $historicalResponsibles->isEmpty())
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

                {{-- ══ RESPONSABLES ACTIVOS ══ --}}
                @if($activeResponsibles->isEmpty())
                <div class="px-5 py-4 flex items-center gap-2 text-sm text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    </svg>
                    Sin responsables activos actualmente
                </div>
                @else
                <div class="divide-y divide-gray-100 dark:divide-gray-700/60">
                    @foreach($activeResponsibles as $resp)
                    @php
                        $initials    = strtoupper(substr($resp->persona->apellido_paterno, 0, 1) . substr($resp->persona->apellido_materno ?? '', 0, 1));
                        $borderClass = $leftBorder[$resp->level] ?? 'border-gray-300';
                        $avatarClass = $avatarColors[$resp->level] ?? 'bg-gray-100 text-gray-600';
                    @endphp
                    <div class="px-4 py-3 border-l-[3px] {{ $borderClass }} hover:bg-gray-50/60 dark:hover:bg-gray-700/30 transition-colors group/row">
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0 text-xs font-bold mt-0.5 {{ $avatarClass }}">
                                {{ $initials }}
                            </div>
                            <div class="flex-1 min-w-0">
                                {{-- Nombre + acciones --}}
                                <div class="flex items-center gap-1.5 min-w-0">
                                    <span class="text-sm font-semibold text-gray-800 dark:text-gray-100 leading-snug truncate flex-1 min-w-0">
                                        {{ $resp->persona->apellido_paterno }} {{ $resp->persona->apellido_materno }},
                                        <span class="font-normal text-gray-600 dark:text-gray-300">{{ $resp->persona->nombres }}</span>
                                    </span>
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
                                        {{-- DAR DE BAJA (reemplaza eliminar) --}}
                                        <button onclick="openDeactivate({{ $resp->id }}, '{{ addslashes($resp->persona->apellido_paterno . ' ' . $resp->persona->nombres) }}')"
                                                title="Dar de baja"
                                                class="w-6 h-6 flex items-center justify-center rounded-md text-gray-400 dark:text-gray-500
                                                       hover:text-orange-600 dark:hover:text-orange-400 hover:bg-orange-50 dark:hover:bg-orange-900/20 transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                {{-- Badges --}}
                                <div class="flex items-center gap-2 mt-1 flex-wrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold uppercase tracking-wide {{ $levelColors[$resp->level] ?? '' }}">
                                        {{ $levelLabels[$resp->level] ?? $resp->level }}
                                    </span>
                                    <span class="inline-flex items-center gap-1 text-[10px] text-emerald-600 dark:text-emerald-400">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 dark:bg-emerald-400"></span>
                                        Activo
                                    </span>
                                    <span class="text-[11px] text-gray-400 dark:text-gray-500 flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        Desde {{ $resp->assigned_at->format('d/m/Y') }}
                                    </span>
                                    @php $firstDoc = $resp->documents->first(); @endphp
                                    @if($firstDoc?->document_type)
                                    <div class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md
                                                bg-indigo-50 dark:bg-indigo-900/30 border border-indigo-200 dark:border-indigo-700/50
                                                text-[11px] text-indigo-600 dark:text-indigo-400">
                                        <svg class="w-3 h-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <span class="font-medium">{{ $docLabels[$firstDoc->document_type] ?? $firstDoc->document_type }}</span>
                                        @if($firstDoc->document_number)<span class="text-indigo-300 dark:text-indigo-600">·</span> {{ $firstDoc->document_number }}@endif
                                        @if($firstDoc->document_date)<span class="text-indigo-300 dark:text-indigo-600">·</span> {{ $firstDoc->document_date->format('d/m/Y') }}@endif
                                    </div>
                                    @endif
                                </div>
                                {{-- Documentos adjuntos --}}
                                @if($resp->documents->count())
                                <div class="mt-2 flex flex-wrap gap-1.5">
                                    @foreach($resp->documents as $doc)
                                    @php
                                        $docExt     = strtolower(pathinfo($doc->original_name, PATHINFO_EXTENSION));
                                        $docMeta    = collect([$docLabels[$doc->document_type] ?? null, $doc->document_number, $doc->document_date?->format('d/m/Y')])->filter()->implode(' · ');
                                        $chipLabel  = $doc->description ?: $doc->original_name;
                                    @endphp
                                    <div class="inline-flex items-center gap-1 pl-2 pr-1 py-0.5 rounded-full
                                                bg-indigo-50 dark:bg-indigo-900/30 border border-indigo-200 dark:border-indigo-700
                                                text-indigo-700 dark:text-indigo-300 text-[11px] font-medium max-w-xs">
                                        <svg class="w-3 h-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                        </svg>
                                        <button type="button"
                                                onclick='openDocPreview({
                                                    name:        {{ json_encode($chipLabel) }},
                                                    description: {{ json_encode($docMeta ?: ($doc->description ? $doc->original_name : null)) }},
                                                    previewUrl:  "{{ route('admin.servers.responsibles.documents.preview', [$server, $resp, $doc]) }}",
                                                    downloadUrl: "{{ route('admin.servers.responsibles.documents.download', [$server, $resp, $doc]) }}",
                                                    ext:         {{ json_encode($docExt) }}
                                                })'
                                                title="{{ $docMeta ? $docMeta . ' — ' . $doc->original_name : $doc->original_name }}"
                                                class="truncate max-w-[160px] hover:underline cursor-pointer">
                                            {{ $chipLabel }}
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
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif

                {{-- ══ HISTORIAL DE RESPONSABLES ══ --}}
                @if($historicalResponsibles->isNotEmpty())
                <div class="border-t border-gray-100 dark:border-gray-700/60">
                    <button type="button" onclick="toggleHistory()"
                            class="w-full flex items-center justify-between px-5 py-2.5 text-xs font-semibold
                                   text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/30
                                   uppercase tracking-wider transition-colors">
                        <span class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Historial de responsables
                            <span class="px-1.5 py-0.5 rounded-full bg-gray-200 dark:bg-gray-600 text-gray-600 dark:text-gray-300 normal-case tracking-normal font-semibold">
                                {{ $historicalResponsibles->count() }}
                            </span>
                        </span>
                        <svg id="history-chevron" class="w-3.5 h-3.5 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div id="resp-history" class="hidden divide-y divide-gray-100 dark:divide-gray-700/60 bg-gray-50/50 dark:bg-gray-800/30">
                        @foreach($historicalResponsibles as $resp)
                        @php
                            $initials    = strtoupper(substr($resp->persona->apellido_paterno, 0, 1) . substr($resp->persona->apellido_materno ?? '', 0, 1));
                            $borderClass = $leftBorder[$resp->level] ?? 'border-gray-300';
                            $avatarClass = 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400';
                        @endphp
                        <div class="px-4 py-3 border-l-[3px] border-gray-300 dark:border-gray-600 opacity-75 hover:opacity-100 hover:bg-gray-100/60 dark:hover:bg-gray-700/30 transition-all group/hist">
                            <div class="flex items-start gap-3">
                                <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0 text-xs font-bold mt-0.5 {{ $avatarClass }}">
                                    {{ $initials }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    {{-- Nombre + acción eliminar --}}
                                    <div class="flex items-center gap-1.5 min-w-0">
                                        <span class="text-sm font-semibold text-gray-600 dark:text-gray-400 leading-snug truncate flex-1 min-w-0">
                                            {{ $resp->persona->apellido_paterno }} {{ $resp->persona->apellido_materno }},
                                            <span class="font-normal">{{ $resp->persona->nombres }}</span>
                                        </span>
                                        <div class="flex items-center gap-0.5 flex-shrink-0 opacity-0 group-hover/hist:opacity-100 transition-opacity">
                                            <button onclick="openReactivate({{ $resp->id }}, '{{ addslashes($resp->persona->apellido_paterno . ' ' . $resp->persona->nombres) }}')"
                                                    title="Reactivar"
                                                    class="w-6 h-6 flex items-center justify-center rounded-md text-gray-400 dark:text-gray-500
                                                           hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition-colors">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                                </svg>
                                            </button>
                                            <form action="{{ route('admin.servers.responsibles.destroy', [$server, $resp]) }}"
                                                  method="POST" id="del-hist-{{ $resp->id }}" class="inline">
                                                @csrf @method('DELETE')
                                                <button type="button"
                                                        title="Eliminar del historial"
                                                        onclick="dtConfirmDelete('del-hist-{{ $resp->id }}', '{{ addslashes($resp->persona->apellido_paterno . ' ' . $resp->persona->nombres) }}')"
                                                        class="w-6 h-6 flex items-center justify-center rounded-md text-gray-400 dark:text-gray-500
                                                               hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    {{-- Badges histórico --}}
                                    <div class="flex items-center gap-2 mt-1 flex-wrap">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold uppercase tracking-wide {{ $levelColors[$resp->level] ?? '' }} opacity-60">
                                            {{ $levelLabels[$resp->level] ?? $resp->level }}
                                        </span>
                                        {{-- Período --}}
                                        <span class="inline-flex items-center gap-1 text-[11px] text-gray-400 dark:text-gray-500 font-medium">
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            {{ $resp->assigned_at->format('d/m/Y') }}
                                            <svg class="w-3 h-3 text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                            </svg>
                                            {{ $resp->unassigned_at?->format('d/m/Y') ?? 'N/A' }}
                                            @php
                                                $dias = $resp->assigned_at->diffInDays($resp->unassigned_at ?? now());
                                            @endphp
                                            <span class="text-gray-300 dark:text-gray-600">({{ $dias }} {{ $dias === 1 ? 'día' : 'días' }})</span>
                                        </span>
                                        @php $histDoc = $resp->documents->first(); @endphp
                                        @if($histDoc?->document_type)
                                        <div class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md
                                                    bg-gray-100 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600
                                                    text-[11px] text-gray-500 dark:text-gray-400">
                                            <span class="font-medium">{{ $docLabels[$histDoc->document_type] ?? $histDoc->document_type }}</span>
                                            @if($histDoc->document_number)<span class="text-gray-300 dark:text-gray-600">·</span> {{ $histDoc->document_number }}@endif
                                        </div>
                                        @endif
                                        @if($histDoc?->document_notes)
                                        <span class="text-[11px] text-gray-400 dark:text-gray-500 italic">{{ $histDoc->document_notes }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

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
              method="POST"
              onsubmit="return respFormSubmit(this)">
            @csrf
            <span id="resp-method"></span>

            <div class="p-6 space-y-4">

                {{-- Persona --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Persona <span class="text-red-500">*</span>
                    </label>
                    <div class="relative" id="resp-search-wrap">
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input type="text" id="resp-search-input" autocomplete="off"
                                   placeholder="Buscar por DNI o apellido/nombre..."
                                   class="w-full pl-9 pr-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600
                                          dark:bg-gray-700 dark:text-white text-sm
                                          focus:ring-emerald-500 focus:border-emerald-500">
                        </div>
                        <input type="hidden" name="persona_id" id="resp-persona_id" required>
                        {{-- Dropdown resultados --}}
                        <div id="resp-dropdown"
                             class="hidden absolute z-50 w-full mt-1 bg-white dark:bg-gray-800
                                    border border-gray-200 dark:border-gray-600 rounded-lg shadow-xl
                                    max-h-48 overflow-y-auto text-sm">
                        </div>
                        {{-- Persona seleccionada --}}
                        <div id="resp-selected"
                             class="hidden mt-2 items-center gap-2 px-3 py-2 rounded-lg
                                    bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-700/40">
                            <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <span id="resp-selected-name" class="flex-1 text-sm font-medium text-emerald-700 dark:text-emerald-300 truncate"></span>
                            <button type="button" onclick="clearPersonaSearch('resp')"
                                    class="text-emerald-400 hover:text-red-500 transition-colors flex-shrink-0">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">Escribe al menos 4 caracteres para buscar</p>
                    </div>
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
     MODAL: Dar de Baja a Responsable
════════════════════════════════════════════════ --}}
<div id="modal-deactivate"
     class="hidden fixed inset-0 z-50 items-center justify-center p-4"
     role="dialog" aria-modal="true">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeModal('modal-deactivate')"></div>
    <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                </svg>
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Dar de Baja Responsable</h3>
            </div>
            <button onclick="closeModal('modal-deactivate')"
                    class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400
                           hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form id="deactivate-form" method="POST">
            @csrf
            <input type="hidden" name="_method" value="PATCH">

            <div class="p-6 space-y-4">
                <div class="flex items-start gap-3 p-3 rounded-lg bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-700/40">
                    <svg class="w-4 h-4 text-orange-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-sm text-orange-700 dark:text-orange-300">
                        El responsable <strong id="deactivate-name" class="font-semibold"></strong> pasará al historial conservando su período de gestión.
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Fecha de baja <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="unassigned_at" id="deactivate-date" required
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                  dark:text-white focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                    <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">Debe ser igual o posterior a la fecha de asignación.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Motivo / Observaciones <span class="text-gray-400 font-normal">(opcional)</span>
                    </label>
                    <textarea name="deactivate_notes" id="deactivate-notes" rows="2"
                              placeholder="Ej: Renuncia voluntaria, Fin de contrato, Reasignación..."
                              class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                     dark:text-white focus:ring-orange-500 focus:border-orange-500 sm:text-sm resize-none"></textarea>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                <button type="button" onclick="closeModal('modal-deactivate')"
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700
                               border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                    Cancelar
                </button>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2 text-sm font-medium text-white
                               bg-orange-600 rounded-lg hover:bg-orange-700 transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                    </svg>
                    Dar de Baja
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ════════════════════════════════════════════════
     MODAL: Reactivar Responsable
════════════════════════════════════════════════ --}}
<div id="modal-reactivate"
     class="hidden fixed inset-0 z-50 items-center justify-center p-4"
     role="dialog" aria-modal="true">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeModal('modal-reactivate')"></div>
    <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-sm">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Reactivar Responsable</h3>
            </div>
            <button onclick="closeModal('modal-reactivate')"
                    class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400
                           hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form id="reactivate-form" method="POST">
            @csrf
            <input type="hidden" name="_method" value="PATCH">

            <div class="p-6 space-y-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    <strong id="reactivate-name" class="text-gray-800 dark:text-gray-200 font-semibold"></strong>
                    volverá a figurar como responsable activo con el nuevo período de asignación.
                </p>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Fecha de reactivación <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="assigned_at" id="reactivate-date" required
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                  dark:text-white focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                <button type="button" onclick="closeModal('modal-reactivate')"
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
                    Reactivar
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
    <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
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

                {{-- Datos del documento de respaldo --}}
                <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                    <p class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider mb-3 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Datos del documento <span class="font-normal text-gray-400 normal-case">(opcional)</span>
                    </p>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Tipo</label>
                            <select name="document_type" id="doc-document_type"
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                           dark:text-white focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="">Sin tipo</option>
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
                            <input type="text" name="document_number" id="doc-document_number"
                                   placeholder="R.D. N°042-2024-OTI"
                                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                          dark:text-white focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Fecha del documento</label>
                            <input type="date" name="document_date" id="doc-document_date"
                                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                          dark:text-white focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Observaciones</label>
                            <input type="text" name="document_notes" id="doc-document_notes"
                                   placeholder="Notas adicionales..."
                                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                          dark:text-white focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                    </div>
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
     MODAL: Agregar IP
════════════════════════════════════════════════ --}}
@can('servers.edit')
<div id="modal-ip"
     class="hidden fixed inset-0 z-50 items-center justify-center p-4"
     role="dialog" aria-modal="true">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeModal('modal-ip')"></div>
    <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md">

        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center">
                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9"/>
                    </svg>
                </div>
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Agregar Dirección IP</h3>
            </div>
            <button onclick="closeModal('modal-ip')"
                    class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400
                           hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Body --}}
        <form id="form-ip" method="POST" action="{{ route('admin.servers.ips.store', $server) }}"
              onsubmit="return validateIpModal()">
            @csrf
            <div class="px-6 py-5 space-y-4">

                {{-- IP + Puerto --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="ip-address-input" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Dirección IP <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="ip-address-input" name="ip_address"
                               placeholder="192.168.1.10"
                               oninput="clearIpError()"
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white font-mono text-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="ip-port-input" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Puerto
                            <span class="font-normal text-xs text-gray-400">(opcional)</span>
                        </label>
                        <input type="number" id="ip-port-input" name="port"
                               placeholder="80, 443…" min="1" max="65535"
                               oninput="clearIpError()"
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white font-mono text-sm focus:ring-blue-500 focus:border-blue-500">
                        <p class="mt-1 text-xs text-gray-400">Permite registrar la misma IP con distintos puertos.</p>
                    </div>
                </div>

                {{-- Tipo + Interfaz --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="ip-type-input" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Tipo</label>
                        <select id="ip-type-input" name="type"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="private">Privada</option>
                            <option value="public">Pública</option>
                        </select>
                    </div>
                    <div>
                        <label for="ip-iface-input" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Interfaz
                            <span class="font-normal text-xs text-gray-400">(opcional)</span>
                        </label>
                        <input type="text" id="ip-iface-input" name="interface"
                               placeholder="eth0, ens3…"
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white font-mono text-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                {{-- Principal --}}
                <label class="flex items-center gap-2.5 cursor-pointer select-none">
                    <input type="checkbox" id="ip-primary-input" name="is_primary" value="1"
                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 w-4 h-4">
                    <div>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">IP Principal</span>
                        <p class="text-xs text-gray-400">Se usará como IP de referencia del servidor.</p>
                    </div>
                </label>

                {{-- Error --}}
                <div id="ip-modal-error" class="hidden items-start gap-2 px-3 py-2.5 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
                    <svg class="w-4 h-4 text-red-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p id="ip-modal-error-text" class="text-sm text-red-700 dark:text-red-400"></p>
                </div>

                {{-- Errores de Laravel (si vuelve con error de servidor) --}}
                @if($errors->hasAny(['ip_address','port','type','interface']))
                <div class="px-3 py-2.5 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-sm text-red-700 dark:text-red-400 space-y-0.5">
                    @foreach(['ip_address','port','type','interface'] as $f)
                        @error($f)<p>{{ $message }}</p>@enderror
                    @endforeach
                </div>
                @endif

            </div>

            {{-- Footer --}}
            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                <button type="button" onclick="closeModal('modal-ip')"
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300
                               bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                    Cancelar
                </button>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium
                               text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Agregar IP
                </button>
            </div>
        </form>
    </div>
</div>
@endcan

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
              method="POST"
              onsubmit="return cntFormSubmit(this)">
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
              method="POST"
              onsubmit="return validateDbForm(event)">
            @csrf
            <span id="db-method"></span>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Nombre / Alias</label>
                        <input type="text" name="name" id="db-name"
                               placeholder="PostgreSQL Producción"
                               maxlength="100"
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                      dark:text-white focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Motor <span class="text-red-500">*</span>
                        </label>
                        <select name="engine" id="db-engine" required
                                onchange="dbEngineChanged(this.value)"
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
                               maxlength="50"
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                      dark:text-white font-mono focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                               onblur="validateDbVersion(this)">
                        <p id="db-version-error" class="hidden mt-1 text-xs text-red-500"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Host</label>
                        <input type="text" name="host" id="db-host"
                               placeholder="192.168.254.5 o localhost"
                               maxlength="150"
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                      dark:text-white font-mono focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                               onblur="validateDbHost(this)">
                        <p id="db-host-error" class="hidden mt-1 text-xs text-red-500"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Puerto</label>
                        <input type="number" name="port" id="db-port" min="1" max="65535"
                               placeholder="5432"
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                      dark:text-white font-mono focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                               onblur="validateDbPort(this)">
                        <p id="db-port-hint" class="mt-1 text-xs text-gray-400 dark:text-gray-500"></p>
                        <p id="db-port-error" class="hidden mt-1 text-xs text-red-500"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Usuario Admin</label>
                        <input type="text" name="admin_user" id="db-admin-user"
                               placeholder="postgres" autocomplete="off"
                               maxlength="100"
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
// ── Modal de IPs ─────────────────────────────────────────────────────
const _existingIps = @json($server->ips->map(fn($i) => ['ip' => $i->ip_address, 'port' => $i->port]));

function validateIpModal() {
    const ip  = document.getElementById('ip-address-input').value.trim();
    const port = document.getElementById('ip-port-input').value;
    const portVal = port ? parseInt(port) : null;

    if (!ip) return showIpError('Ingresa una dirección IP.');

    const ipv4 = /^(\d{1,3}\.){3}\d{1,3}$/.test(ip);
    const ipv6 = /^[0-9a-fA-F:]{2,39}$/.test(ip);
    if (!ipv4 && !ipv6) return showIpError('Formato de IP inválido (IPv4 o IPv6).');

    const dup = _existingIps.some(e =>
        e.ip === ip &&
        (e.port === portVal || (e.port == null && portVal == null))
    );
    if (dup) {
        return showIpError(portVal
            ? 'La IP ' + ip + ':' + portVal + ' ya está registrada en este servidor.'
            : 'La IP ' + ip + ' ya está registrada. Puedes agregar la misma IP con un puerto distinto.'
        );
    }
    return true;
}

function showIpError(msg) {
    const box  = document.getElementById('ip-modal-error');
    const text = document.getElementById('ip-modal-error-text');
    text.textContent = msg;
    box.classList.remove('hidden');
    box.classList.add('flex');
    document.getElementById('ip-address-input').classList.add('border-red-400', 'focus:ring-red-400');
    return false;
}

function clearIpError() {
    const box = document.getElementById('ip-modal-error');
    box.classList.add('hidden');
    box.classList.remove('flex');
    document.getElementById('ip-address-input').classList.remove('border-red-400', 'focus:ring-red-400');
}

@if($errors->hasAny(['ip_address','port','type','interface']))
document.addEventListener('DOMContentLoaded', () => openModal('modal-ip'));
@endif

// ── Confirmación de eliminación ───────────────────────────────────────
function dtConfirmDelete(formId, entityName) {
    const t = document.documentElement.classList.contains('dark')
        ? { background: '#1e293b', color: '#f1f5f9' }
        : { background: '#ffffff', color: '#111827' };
    Swal.fire({
        title: 'Confirmar eliminación',
        text: '¿Eliminar "' + entityName + '"? Esta acción no se puede deshacer.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        focusCancel: true,
        reverseButtons: true,
        background: t.background,
        color: t.color,
    }).then(r => {
        if (r.isConfirmed) document.getElementById(formId).submit();
    });
}

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

// ── Persona autocomplete ──────────────────────────────────────────────
const personaSearchUrl = "{{ route('admin.personas.search') }}";
let searchTimers = {};

function initPersonaSearch(prefix) {
    const searchInput = document.getElementById(prefix + '-search-input');
    const hiddenInput = document.getElementById(prefix + '-persona_id');
    const dropdown    = document.getElementById(prefix + '-dropdown');
    const selected    = document.getElementById(prefix + '-selected');
    const selName     = document.getElementById(prefix + '-selected-name');

    searchInput.addEventListener('input', function () {
        const q = this.value.trim();
        clearTimeout(searchTimers[prefix]);
        dropdown.classList.add('hidden');
        dropdown.innerHTML = '';

        if (q.length < 4) return;

        dropdown.innerHTML = '<p class="px-4 py-3 text-xs text-gray-400 dark:text-gray-500">Buscando...</p>';
        dropdown.classList.remove('hidden');

        searchTimers[prefix] = setTimeout(async () => {
            try {
                const res  = await fetch(personaSearchUrl + '?q=' + encodeURIComponent(q));
                const data = await res.json();
                dropdown.innerHTML = '';

                if (!data.length) {
                    dropdown.innerHTML = '<p class="px-4 py-3 text-xs text-gray-400 dark:text-gray-500">Sin resultados</p>';
                    return;
                }

                data.forEach(p => {
                    const label = `${p.apellido_paterno} ${p.apellido_materno ?? ''}, ${p.nombres} &mdash; <span class="font-mono">${p.dni}</span>`;
                    const btn   = document.createElement('button');
                    btn.type    = 'button';
                    btn.className = 'w-full text-left px-4 py-2.5 text-sm text-gray-800 dark:text-gray-200 ' +
                                    'hover:bg-emerald-50 dark:hover:bg-emerald-900/20 ' +
                                    'hover:text-emerald-700 dark:hover:text-emerald-300 transition-colors';
                    btn.innerHTML = label;
                    btn.addEventListener('click', () => {
                        hiddenInput.value   = p.id;
                        selName.textContent = `${p.apellido_paterno} ${p.apellido_materno ?? ''}, ${p.nombres} (${p.dni})`;
                        searchInput.value   = '';
                        dropdown.classList.add('hidden');
                        dropdown.innerHTML  = '';
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
        if (!document.getElementById(prefix + '-search-wrap').contains(e.target)) {
            dropdown.classList.add('hidden');
        }
    });
}

function clearPersonaSearch(prefix) {
    document.getElementById(prefix + '-persona_id').value = '';
    document.getElementById(prefix + '-selected-name').textContent = '';
    const sel = document.getElementById(prefix + '-selected');
    sel.classList.add('hidden');
    sel.classList.remove('flex');
    document.getElementById(prefix + '-search-input').value = '';
}

function resetPersonaSearch(prefix) {
    clearPersonaSearch(prefix);
    document.getElementById(prefix + '-dropdown').classList.add('hidden');
    document.getElementById(prefix + '-dropdown').innerHTML = '';
}

// Inicializar autocompletado del modal de responsables
initPersonaSearch('resp');

// ── Responsables ─────────────────────────────────────────────────────
const respStoreUrl  = "{{ route('admin.servers.responsibles.store', $server) }}";
const respUpdateBase = "{{ url('admin/servers/' . $server->id . '/responsibles') }}/";

function editResponsible(id, data) {
    document.getElementById('resp-modal-title').textContent  = 'Editar Responsable';
    document.getElementById('resp-submit-label').textContent = 'Guardar';
    document.getElementById('resp-form').action = respUpdateBase + id;
    document.getElementById('resp-method').innerHTML = '<input type="hidden" name="_method" value="PUT">';

    // Mostrar persona ya seleccionada
    document.getElementById('resp-persona_id').value = data.persona_id ?? '';
    if (data.persona_id && data.persona) {
        const selName = document.getElementById('resp-selected-name');
        const sel     = document.getElementById('resp-selected');
        selName.textContent = `${data.persona.apellido_paterno} ${data.persona.apellido_materno ?? ''}, ${data.persona.nombres}`;
        sel.classList.remove('hidden');
        sel.classList.add('flex');
    }

    document.getElementById('resp-level').value          = data.level          ?? 'soporte';
    document.getElementById('resp-assigned_at').value    = data.assigned_at    ?? '';
    document.getElementById('resp-is_active').checked    = data.is_active      == 1;

    resetRespModal();
    document.getElementById('resp-submit-label').textContent = 'Guardar';
    openModal('modal-responsible');
}

document.getElementById('modal-responsible').addEventListener('click', function(e) {
    if (e.target === this) closeModal('modal-responsible');
});

function respFormSubmit(form) {
    const btn = form.querySelector('button[type="submit"]');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg><span>Guardando…</span>';
    }
    return true;
}

function resetRespModal() {
    const btn = document.querySelector('#resp-form button[type="submit"]');
    if (btn) { btn.disabled = false; btn.innerHTML = '<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg><span id="resp-submit-label">Asignar</span>'; }
}

// Reset modal al abrir para agregar
document.querySelector('[onclick="openModal(\'modal-responsible\')"]')?.addEventListener('click', function() {
    document.getElementById('resp-modal-title').textContent  = 'Asignar Responsable';
    document.getElementById('resp-form').action = respStoreUrl;
    document.getElementById('resp-method').innerHTML = '';
    document.getElementById('resp-form').reset();
    resetPersonaSearch('resp');
    resetRespModal();
    document.getElementById('resp-submit-label').textContent = 'Asignar';
    document.getElementById('resp-assigned_at').value = new Date().toISOString().slice(0, 10);
    document.getElementById('resp-is_active').checked = true;
});

// ── Dar de Baja / Reactivar ───────────────────────────────────────────
const deactivateBase = "{{ url('admin/servers/' . $server->id . '/responsibles') }}/";

function openDeactivate(id, nombre) {
    document.getElementById('deactivate-name').textContent = nombre;
    document.getElementById('deactivate-form').action = deactivateBase + id + '/deactivate';
    document.getElementById('deactivate-date').value = new Date().toISOString().slice(0, 10);
    document.getElementById('deactivate-notes').value = '';
    openModal('modal-deactivate');
}

function openReactivate(id, nombre) {
    document.getElementById('reactivate-name').textContent = nombre;
    document.getElementById('reactivate-form').action = deactivateBase + id + '/reactivate';
    document.getElementById('reactivate-date').value = new Date().toISOString().slice(0, 10);
    openModal('modal-reactivate');
}

// ── Historial toggle ──────────────────────────────────────────────────
function toggleHistory() {
    const panel   = document.getElementById('resp-history');
    const chevron = document.getElementById('history-chevron');
    const hidden  = panel.classList.toggle('hidden');
    chevron.style.transform = hidden ? '' : 'rotate(180deg)';
}

// ── Documentos de Responsables ───────────────────────────────────────
const docUploadBase = "{{ url('admin/servers/' . $server->id . '/responsibles') }}/";

function openDocUpload(responsibleId) {
    document.getElementById('doc-upload-form').action = docUploadBase + responsibleId + '/documents';
    document.getElementById('doc-file-input').value      = '';
    document.getElementById('doc-description').value     = '';
    document.getElementById('doc-document_type').value   = '';
    document.getElementById('doc-document_number').value = '';
    document.getElementById('doc-document_date').value   = '';
    document.getElementById('doc-document_notes').value  = '';
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

function cntFormSubmit(form) {
    const btn = form.querySelector('button[type="submit"]');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<svg class="w-4 h-4 animate-spin inline-block mr-1" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg>Guardando…';
    }
    return true;
}

function resetCntModal() {
    const btn = document.querySelector('#cnt-form button[type="submit"]');
    if (btn) { btn.disabled = false; btn.textContent = 'Guardar'; }
}

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
    resetCntModal();
    openModal('modal-container');
}

document.getElementById('modal-container').querySelector('button[onclick="closeModal(\'modal-container\')"]')
    ?.closest('.relative')
    ?.querySelector('.absolute')
    ?.addEventListener('click', () => {
        document.getElementById('cnt-modal-title').textContent = 'Agregar Contenedor';
        document.getElementById('cnt-form').action = cntStoreUrl;
        document.getElementById('cnt-method').innerHTML = '';
        document.getElementById('cnt-form').reset();
        resetCntModal();
    });

// ── Motores de BD — validaciones ─────────────────────────────────────
const DB_DEFAULT_PORTS = {
    postgresql: 5432,
    mysql:      3306,
    mariadb:    3306,
    oracle:     1521,
    sqlserver:  1433,
    sqlite:     null,
    mongodb:    27017,
    other:      null,
};
const DB_DEFAULT_USERS = {
    postgresql: 'postgres',
    mysql:      'root',
    mariadb:    'root',
    oracle:     'system',
    sqlserver:  'sa',
    sqlite:     null,
    mongodb:    null,
    other:      null,
};

function dbEngineChanged(engine) {
    const portInput  = document.getElementById('db-port');
    const portHint   = document.getElementById('db-port-hint');
    const userInput  = document.getElementById('db-admin-user');
    const defaultPort = DB_DEFAULT_PORTS[engine];
    const defaultUser = DB_DEFAULT_USERS[engine];

    // Solo auto-completar si el campo está vacío
    if (!portInput.value && defaultPort) {
        portInput.value = defaultPort;
    }
    portHint.textContent = defaultPort
        ? 'Puerto por defecto: ' + defaultPort
        : 'Este motor no usa puerto TCP.';

    if (!userInput.value && defaultUser) {
        userInput.value = defaultUser;
    }
    clearDbFieldError('db-port');
}

function validateDbHost(input) {
    const val = input.value.trim();
    if (!val) return true; // opcional
    // IPv4
    const ipv4 = /^(\d{1,3}\.){3}\d{1,3}$/;
    // IPv6 simplificado
    const ipv6 = /^[0-9a-fA-F:]+$/;
    // hostname / dominio
    const hostname = /^[a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?(\.[a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?)*$/;
    const valid = val === 'localhost' || ipv4.test(val) || ipv6.test(val) || hostname.test(val);
    setDbFieldError('db-host', input, valid ? null : 'Ingresa una IP válida (ej. 192.168.1.1) o un hostname (ej. db.unamad.edu.pe).');
    return valid;
}

function validateDbVersion(input) {
    const val = input.value.trim();
    if (!val) return true; // opcional
    const valid = /^\d+(\.\d+){0,3}([a-zA-Z0-9\-_\.]*)?$/.test(val);
    setDbFieldError('db-version', input, valid ? null : 'Formato inválido. Usa p.ej. 16, 16.2 o 8.0.32.');
    return valid;
}

function validateDbPort(input) {
    const val = parseInt(input.value, 10);
    if (!input.value) return true; // opcional
    const valid = !isNaN(val) && val >= 1 && val <= 65535;
    setDbFieldError('db-port', input, valid ? null : 'El puerto debe estar entre 1 y 65535.');
    return valid;
}

function setDbFieldError(errorId, input, message) {
    const el = document.getElementById(errorId + '-error');
    if (!el) return;
    if (message) {
        el.textContent = message;
        el.classList.remove('hidden');
        input.classList.add('border-red-400');
        input.classList.remove('border-gray-300', 'dark:border-gray-600');
    } else {
        el.classList.add('hidden');
        input.classList.remove('border-red-400');
        input.classList.add('border-gray-300', 'dark:border-gray-600');
    }
}

function clearDbFieldError(field) {
    const input = document.getElementById(field);
    const el    = document.getElementById(field + '-error');
    if (input) { input.classList.remove('border-red-400'); input.classList.add('border-gray-300', 'dark:border-gray-600'); }
    if (el)    { el.classList.add('hidden'); }
}

function validateDbForm(e) {
    const hostOk    = validateDbHost(document.getElementById('db-host'));
    const versionOk = validateDbVersion(document.getElementById('db-version'));
    const portOk    = validateDbPort(document.getElementById('db-port'));
    if (!hostOk || !versionOk || !portOk) {
        e.preventDefault();
        return false;
    }
    // Protección contra doble clic
    const btn = e.target.querySelector('button[type="submit"]');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<svg class="w-4 h-4 animate-spin inline-block mr-1" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg>Guardando…';
    }
    return true;
}

// Inicializar hint al cargar (motor por defecto = postgresql)
document.addEventListener('DOMContentLoaded', () => dbEngineChanged('postgresql'));

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
    // Actualizar hint de puerto y limpiar errores previos
    const defaultPort = DB_DEFAULT_PORTS[data.engine] ?? null;
    document.getElementById('db-port-hint').textContent = defaultPort
        ? 'Puerto por defecto: ' + defaultPort
        : 'Este motor no usa puerto TCP.';
    ['db-host', 'db-version', 'db-port'].forEach(clearDbFieldError);
    openModal('modal-dbserver');
}

// Reset modal BD al cerrarse
function resetDbModal() {
    document.getElementById('db-modal-title').textContent = 'Agregar Motor de BD';
    document.getElementById('db-form').action = dbStoreUrl;
    document.getElementById('db-method').innerHTML = '';
    document.getElementById('db-admin-pass').placeholder = '••••••••';
    document.getElementById('db-form').reset();
    // Restaurar botón submit por si quedó bloqueado
    const btn = document.querySelector('#db-form button[type="submit"]');
    if (btn) { btn.disabled = false; btn.textContent = 'Guardar'; }
    ['db-host', 'db-version', 'db-port'].forEach(clearDbFieldError);
    dbEngineChanged('postgresql');
}
</script>
@endpush
