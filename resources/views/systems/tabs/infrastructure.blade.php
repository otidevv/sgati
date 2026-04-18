@php $infra = $system->infrastructure?->load('serverIp', 'exposedIps'); @endphp

<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 pb-4">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-blue-50 dark:bg-blue-900/30 rounded-lg">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/>
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Infraestructura del Sistema</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Configuración de servidor y despliegue</p>
            </div>
        </div>
        @can('infrastructure.edit')
        <a href="{{ route('systems.infrastructure.edit', $system) }}"
           class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
            </svg>
            Editar
        </a>
        @endcan
    </div>

    @if(!$infra || (!$infra->server_id && !$infra->system_url))
    {{-- Empty State --}}
    <div class="text-center py-16 bg-gray-50 dark:bg-gray-800 rounded-xl border-2 border-dashed border-gray-200 dark:border-gray-700">
        <div class="p-3 bg-gray-100 dark:bg-gray-700 rounded-full w-fit mx-auto mb-4">
            <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/>
            </svg>
        </div>
        <h4 class="text-base font-semibold text-gray-700 dark:text-gray-300 mb-1">Sin información de infraestructura</h4>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">No hay datos de infraestructura registrados para este sistema.</p>
        @can('infrastructure.edit')
        <a href="{{ route('systems.infrastructure.edit', $system) }}"
           class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/30 hover:bg-blue-100 dark:hover:bg-blue-900/50 rounded-lg transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Registrar infraestructura
        </a>
        @endcan
    </div>
    @else
    {{-- Infrastructure Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Server Information --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
            <div class="px-5 py-4 bg-gradient-to-r from-gray-50 to-white dark:from-gray-700/50 dark:to-gray-800 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M17 12v4m-7-4v4m-7-4v4M5 8h14a2 2 0 012 2v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4a2 2 0 012-2z"/>
                    </svg>
                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white">Información del Servidor</h4>
                </div>
            </div>
            <div class="p-5 space-y-4">
                @if($infra->server)
                @php $srv = $infra->server; @endphp
                {{-- Server name --}}
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0 mt-0.5">
                        <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Servidor</p>
                        <a href="{{ route('admin.servers.show', $srv) }}"
                           class="text-sm font-semibold text-blue-600 dark:text-blue-400 hover:underline">{{ $srv->name }}</a>
                        @if($srv->function)
                        <span class="ml-2 inline-flex items-center px-1.5 py-0.5 text-xs font-medium rounded bg-{{ $srv->function->color() }}-100 dark:bg-{{ $srv->function->color() }}-900/30 text-{{ $srv->function->color() }}-700 dark:text-{{ $srv->function->color() }}-300">
                            {{ $srv->function->label() }}
                        </span>
                        @endif
                    </div>
                </div>
                {{-- OS --}}
                @if($srv->operating_system)
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0 mt-0.5">
                        <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Sistema Operativo</p>
                        <p class="text-sm text-gray-900 dark:text-gray-200 mt-0.5">{{ $srv->operating_system }}</p>
                    </div>
                </div>
                @endif
                {{-- IPs --}}
                @if($srv->ips->count())
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0 mt-0.5">
                        <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Direcciones IP</p>
                        <div class="mt-1 flex flex-wrap gap-1.5">
                            @foreach($srv->ips as $ip)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-mono
                                {{ $ip->type === 'public' ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300' : 'bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $ip->type === 'public' ? 'bg-blue-500' : 'bg-slate-400' }}"></span>
                                {{ $ip->ip_address }}
                                @if($ip->is_primary)<span class="opacity-60">•</span>@endif
                            </span>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
                {{-- Web root --}}
                @if($srv->web_root)
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0 mt-0.5">
                        <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Ruta Web</p>
                        <p class="text-sm font-mono text-gray-900 dark:text-gray-200 mt-0.5 break-all">{{ $srv->web_root }}</p>
                    </div>
                </div>
                @endif
                @else
                <p class="text-sm text-gray-400 dark:text-gray-500 italic">Sin servidor asignado.</p>
                @endif

                {{-- Web server software --}}
                @if($infra->web_server)
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0 mt-0.5">
                        <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Web Server</p>
                        <p class="text-sm text-gray-900 dark:text-gray-200 mt-0.5">{{ $infra->web_server }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Web, SSL & Environment --}}
        <div class="space-y-6">
            {{-- Web Access --}}
            @php
                $exposedIps = $infra->exposedIps ?? collect();
                $hasWebInfo = $infra->system_url || $infra->public_ip || $infra->port || $exposedIps->count();
            @endphp
            @if($hasWebInfo)
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="px-5 py-4 bg-gradient-to-r from-blue-50 to-white dark:from-blue-900/20 dark:to-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                        </svg>
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white">Acceso Web</h4>
                    </div>
                </div>
                <div class="p-5 space-y-4">

                    {{-- URL --}}
                    @if($infra->system_url)
                    @php
                        $displayUrl = $infra->system_url;
                        $hrefUrl    = str_starts_with($displayUrl, 'http') ? $displayUrl : 'http://' . $displayUrl;
                    @endphp
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 mt-0.5">
                            <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">URL / Dirección</p>
                            <a href="{{ $hrefUrl }}" target="_blank"
                               class="inline-flex items-center gap-1.5 text-sm text-blue-600 dark:text-blue-400 hover:underline break-all">
                                {{ $displayUrl }}
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                    @endif

                    {{-- IPs Públicas de Exposición --}}
                    @if($exposedIps->count())
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 mt-0.5">
                            <svg class="w-4 h-4 text-emerald-500 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1.5">
                                {{ $exposedIps->count() === 1 ? 'IP Pública' : 'IPs Públicas' }}
                            </p>
                            <div class="flex flex-wrap gap-2">
                                @foreach($exposedIps as $pip)
                                @php
                                    $ipDisplay = $pip->ip_address . ($infra->port ? ':' . $infra->port : '');
                                    $href = 'http://' . $pip->ip_address . ($infra->port ? ':' . $infra->port : '');
                                @endphp
                                <a href="{{ $href }}" target="_blank"
                                   class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-xs font-mono font-medium
                                          bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300
                                          border border-emerald-200 dark:border-emerald-700
                                          hover:bg-emerald-100 dark:hover:bg-emerald-900/40 transition-colors">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0"></span>
                                    {{ $ipDisplay }}
                                    @if($pip->is_primary)
                                    <span class="text-[10px] font-semibold text-emerald-500 dark:text-emerald-400">· principal</span>
                                    @endif
                                    <svg class="w-3 h-3 opacity-60 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                    </svg>
                                </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @elseif($infra->public_ip)
                    {{-- Fallback: IP pública manual (sin exposedIps) --}}
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 mt-0.5">
                            <svg class="w-4 h-4 text-emerald-500 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">IP Pública</p>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-xs font-mono font-medium
                                         bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300
                                         border border-emerald-200 dark:border-emerald-700">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0"></span>
                                {{ $infra->public_ip }}{{ $infra->port ? ':' . $infra->port : '' }}
                            </span>
                        </div>
                    </div>
                    @endif

                    {{-- Puerto de la aplicación --}}
                    @if($infra->port)
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 mt-0.5">
                            <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Puerto de la Aplicación</p>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-mono font-semibold bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-700">
                                :{{ $infra->port }}
                            </span>
                        </div>
                    </div>
                    @endif

                </div>
            </div>
            @endif

            {{-- SSL Configuration --}}
            @php
                $sslExpiry     = $infra->effectiveSslExpiry();
                $sslCert       = $infra->sslCertificate;
                $daysLeft      = $sslExpiry ? now()->diffInDays($sslExpiry, false) : null;
                $isExpired     = $daysLeft !== null && $daysLeft < 0;
                $isExpiringSoon= $daysLeft !== null && $daysLeft >= 0 && $daysLeft < 30;
                $expiryColor   = $isExpired ? 'red' : ($isExpiringSoon ? 'yellow' : 'green');
            @endphp
            <div class="bg-white dark:bg-gray-800 rounded-xl border
                        {{ $infra->ssl_enabled && $isExpired ? 'border-red-200 dark:border-red-800' : ($infra->ssl_enabled && $isExpiringSoon ? 'border-yellow-200 dark:border-yellow-800' : 'border-gray-200 dark:border-gray-700') }}
                        shadow-sm overflow-hidden">
                <div class="px-5 py-4 bg-gradient-to-r from-gray-50 to-white dark:from-gray-700/50 dark:to-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white">Certificado SSL</h4>
                    </div>
                </div>
                <div class="p-5 space-y-4">
                    {{-- Estado --}}
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Estado</span>
                        @if($infra->ssl_enabled)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold text-green-700 dark:text-green-300 bg-green-100 dark:bg-green-900/30 rounded-full">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Habilitado
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 rounded-full">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                </svg>
                                No habilitado
                            </span>
                        @endif
                    </div>

                    @if($infra->ssl_enabled)
                    {{-- Certificado vinculado (institucional) --}}
                    @if($sslCert)
                    <div class="flex items-start justify-between gap-2">
                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mt-0.5">Certificado</span>
                        <div class="text-right">
                            @can('infrastructure.edit')
                            <a href="{{ route('admin.ssl-certificates.show', $sslCert) }}"
                               class="text-sm font-semibold text-blue-600 dark:text-blue-400 hover:underline">
                                {{ $sslCert->name }}
                            </a>
                            @else
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $sslCert->name }}</p>
                            @endcan
                            @if($sslCert->common_name)
                            <p class="text-xs font-mono text-gray-400 dark:text-gray-500 mt-0.5">{{ $sslCert->common_name }}</p>
                            @endif
                        </div>
                    </div>
                    @else
                    {{-- Certificado propio (solo fecha) --}}
                    <div class="flex items-center gap-1.5">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                     bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300">
                            Certificado propio
                        </span>
                    </div>
                    @endif

                    {{-- Vencimiento --}}
                    @if($sslExpiry)
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Vencimiento</span>
                        <div class="text-right">
                            <p class="text-sm font-semibold text-{{ $expiryColor }}-600 dark:text-{{ $expiryColor }}-400">
                                {{ $sslExpiry->format('d/m/Y') }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                @if($isExpired) Vencido hace {{ abs($daysLeft) }} días
                                @elseif($isExpiringSoon) Vence en {{ $daysLeft }} días
                                @else {{ $sslExpiry->diffForHumans() }}
                                @endif
                            </p>
                        </div>
                    </div>
                    @if($isExpiringSoon || $isExpired)
                    <div class="flex items-start gap-2 p-3 {{ $isExpired ? 'bg-red-50 dark:bg-red-900/30 border-red-200 dark:border-red-800' : 'bg-yellow-50 dark:bg-yellow-900/30 border-yellow-200 dark:border-yellow-800' }} border rounded-lg">
                        <svg class="w-4 h-4 {{ $isExpired ? 'text-red-500 dark:text-red-400' : 'text-yellow-500 dark:text-yellow-400' }} flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <p class="text-xs {{ $isExpired ? 'text-red-700 dark:text-red-300' : 'text-yellow-700 dark:text-yellow-300' }}">
                            @if($isExpired) El certificado SSL ha vencido. Se requiere renovación inmediata.
                            @else El certificado SSL vencerá pronto. Planifique su renovación.
                            @endif
                        </p>
                    </div>
                    @endif
                    @endif
                    @endif
                </div>
            </div>

            {{-- Environment --}}
            @if($infra->environment)
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="px-5 py-4 bg-gradient-to-r from-gray-50 to-white dark:from-gray-700/50 dark:to-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                        </svg>
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white">Ambiente</h4>
                    </div>
                </div>
                <div class="p-5">
                    @php
                        $envColors = [
                            'production' => 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300',
                            'staging' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300',
                            'testing' => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300',
                            'development' => 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300',
                        ];
                        $envKey = $infra->environment->value ?? 'unknown';
                        $badgeColor = $envColors[$envKey] ?? 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300';
                    @endphp
                    <span class="inline-flex items-center px-3 py-1.5 text-sm font-semibold rounded-lg {{ $badgeColor }}">
                        {{ $infra->environment->label() }}
                    </span>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Notes Section --}}
    @if($infra->notes)
    <div class="bg-amber-50 dark:bg-amber-900/30 border border-amber-200 dark:border-amber-800 rounded-xl overflow-hidden">
        <div class="px-5 py-3 bg-amber-100 dark:bg-amber-900/50 border-b border-amber-200 dark:border-amber-800">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                <h4 class="text-sm font-semibold text-amber-900 dark:text-amber-200">Notas Adicionales</h4>
            </div>
        </div>
        <div class="p-5">
            <p class="text-sm text-amber-900 dark:text-amber-200 whitespace-pre-wrap">{{ $infra->notes }}</p>
        </div>
    </div>
    @endif
    @endif
</div>
