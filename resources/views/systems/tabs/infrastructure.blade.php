@php $infra = $system->infrastructure; @endphp

<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between border-b border-gray-200 pb-4">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-blue-50 rounded-lg">
                <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/>
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Infraestructura del Sistema</h3>
                <p class="text-sm text-gray-500">Configuración de servidor y despliegue</p>
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

    @if(!$infra || (!$infra->server_name && !$infra->system_url && !$infra->server_ip))
    {{-- Empty State --}}
    <div class="text-center py-16 bg-gray-50 rounded-xl border-2 border-dashed border-gray-200">
        <div class="p-3 bg-gray-100 rounded-full w-fit mx-auto mb-4">
            <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/>
            </svg>
        </div>
        <h4 class="text-base font-semibold text-gray-700 mb-1">Sin información de infraestructura</h4>
        <p class="text-sm text-gray-500 mb-4">No hay datos de infraestructura registrados para este sistema.</p>
        @can('infrastructure.edit')
        <a href="{{ route('systems.infrastructure.edit', $system) }}" 
           class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors">
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
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 bg-gradient-to-r from-gray-50 to-white border-b border-gray-200">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M17 12v4m-7-4v4m-7-4v4M5 8h14a2 2 0 012 2v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4a2 2 0 012-2z"/>
                    </svg>
                    <h4 class="text-sm font-semibold text-gray-900">Información del Servidor</h4>
                </div>
            </div>
            <div class="p-5 space-y-4">
                @foreach([
                    ['Nombre del Servidor', $infra->server_name, 'server'],
                    ['Sistema Operativo', $infra->server_os, 'desktop'],
                    ['IP Interna', $infra->server_ip, 'ip'],
                    ['IP Pública', $infra->public_ip, 'globe'],
                    ['Puerto', $infra->port, 'port'],
                    ['Web Server', $infra->web_server, 'cloud'],
                ] as [$label, $value, $icon])
                @if($value)
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0 mt-0.5">
                        @if($icon === 'server')
                            <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2"/>
                            </svg>
                        @elseif($icon === 'desktop')
                            <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        @elseif($icon === 'ip')
                            <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                            </svg>
                        @elseif($icon === 'globe')
                            <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                            </svg>
                        @elseif($icon === 'port')
                            <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                            </svg>
                        @elseif($icon === 'cloud')
                            <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/>
                            </svg>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">{{ $label }}</p>
                        <p class="text-sm font-mono text-gray-900 mt-0.5 break-all">{{ $value }}</p>
                    </div>
                </div>
                @endif
                @endforeach
            </div>
        </div>

        {{-- Web, SSL & Environment --}}
        <div class="space-y-6">
            {{-- Web Access --}}
            @if($infra->system_url)
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 bg-gradient-to-r from-blue-50 to-white border-b border-gray-200">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                        </svg>
                        <h4 class="text-sm font-semibold text-gray-900">Acceso Web</h4>
                    </div>
                </div>
                <div class="p-5">
                    <a href="{{ $infra->system_url }}" target="_blank"
                       class="inline-flex items-center gap-2 text-sm text-blue-600 hover:text-blue-700 hover:underline break-all">
                        {{ $infra->system_url }}
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                    </a>
                </div>
            </div>
            @endif

            {{-- SSL Configuration --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 bg-gradient-to-r from-gray-50 to-white border-b border-gray-200">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        <h4 class="text-sm font-semibold text-gray-900">Certificado SSL</h4>
                    </div>
                </div>
                <div class="p-5 space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Estado</span>
                        @if($infra->ssl_enabled)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold text-green-700 bg-green-100 rounded-full">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Habilitado
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold text-gray-600 bg-gray-100 rounded-full">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                </svg>
                                No habilitado
                            </span>
                        @endif
                    </div>
                    @if($infra->ssl_enabled && $infra->ssl_expiry)
                    @php
                        $daysLeft = now()->diffInDays($infra->ssl_expiry, false);
                        $isExpired = $daysLeft < 0;
                        $isExpiringSoon = $daysLeft >= 0 && $daysLeft < 30;
                        $statusColor = $isExpired ? 'red' : ($isExpiringSoon ? 'yellow' : 'green');
                    @endphp
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Vencimiento</span>
                        <div class="text-right">
                            <p class="text-sm font-semibold text-{{ $statusColor }}-600">
                                {{ $infra->ssl_expiry->format('d/m/Y') }}
                            </p>
                            <p class="text-xs text-gray-500 mt-0.5">
                                @if($isExpired)
                                    Vencido hace {{ abs($daysLeft) }} días
                                @elseif($isExpiringSoon)
                                    Vence en {{ $daysLeft }} días
                                @else
                                    {{ $infra->ssl_expiry->diffForHumans() }}
                                @endif
                            </p>
                        </div>
                    </div>
                    @if($isExpiringSoon || $isExpired)
                    <div class="flex items-start gap-2 p-3 bg-{{ $statusColor }}-50 border border-{{ $statusColor }}-200 rounded-lg">
                        <svg class="w-4 h-4 text-{{ $statusColor }}-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <p class="text-xs text-{{ $statusColor }}-700">
                            @if($isExpired)
                                El certificado SSL ha vencido. Se requiere renovación inmediata.
                            @else
                                El certificado SSL vencerá pronto. Planifique su renovación.
                            @endif
                        </p>
                    </div>
                    @endif
                    @endif
                </div>
            </div>

            {{-- Environment --}}
            @if($infra->environment)
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 bg-gradient-to-r from-gray-50 to-white border-b border-gray-200">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                        </svg>
                        <h4 class="text-sm font-semibold text-gray-900">Ambiente</h4>
                    </div>
                </div>
                <div class="p-5">
                    @php
                        $envColors = [
                            'production' => 'bg-purple-100 text-purple-700',
                            'staging' => 'bg-blue-100 text-blue-700',
                            'testing' => 'bg-yellow-100 text-yellow-700',
                            'development' => 'bg-green-100 text-green-700',
                        ];
                        $envKey = $infra->environment->value ?? 'unknown';
                        $badgeColor = $envColors[$envKey] ?? 'bg-gray-100 text-gray-700';
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
    <div class="bg-amber-50 border border-amber-200 rounded-xl overflow-hidden">
        <div class="px-5 py-3 bg-amber-100 border-b border-amber-200">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                <h4 class="text-sm font-semibold text-amber-900">Notas Adicionales</h4>
            </div>
        </div>
        <div class="p-5">
            <p class="text-sm text-amber-900 whitespace-pre-wrap">{{ $infra->notes }}</p>
        </div>
    </div>
    @endif
    @endif
</div>
