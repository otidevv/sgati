@php $infra = $system->infrastructure; @endphp

<div class="space-y-5">
    <div class="flex items-center justify-between">
        <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Infraestructura del Sistema</h3>
        @can('infrastructure.edit')
        <a href="{{ route('systems.infrastructure.edit', $system) }}"
           class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
            </svg>
            Editar infraestructura
        </a>
        @endcan
    </div>

    @if(!$infra || (!$infra->server_name && !$infra->system_url && !$infra->server_ip))
    <div class="text-center py-12 text-gray-400">
        <svg class="mx-auto w-10 h-10 text-gray-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/>
        </svg>
        <p class="text-sm">No hay datos de infraestructura registrados.</p>
        @can('infrastructure.edit')
        <a href="{{ route('systems.infrastructure.edit', $system) }}" class="mt-2 inline-block text-sm text-blue-600 hover:underline">Registrar infraestructura →</a>
        @endcan
    </div>
    @else
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        {{-- Servidor --}}
        <div class="bg-gray-50 rounded-xl border border-gray-200 p-4 space-y-3">
            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Servidor</h4>
            @foreach([
                ['Nombre',     $infra->server_name],
                ['Sistema OS', $infra->server_os],
                ['IP Interna', $infra->server_ip],
                ['IP Pública', $infra->public_ip],
                ['Puerto',     $infra->port],
                ['Web Server', $infra->web_server],
            ] as [$label, $value])
            @if($value)
            <div class="flex gap-3">
                <span class="text-xs text-gray-400 w-24 flex-shrink-0 pt-0.5">{{ $label }}</span>
                <span class="text-sm text-gray-800 font-mono">{{ $value }}</span>
            </div>
            @endif
            @endforeach
        </div>

        {{-- Web & SSL --}}
        <div class="bg-gray-50 rounded-xl border border-gray-200 p-4 space-y-3">
            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Web & SSL</h4>
            @if($infra->system_url)
            <div class="flex gap-3">
                <span class="text-xs text-gray-400 w-24 flex-shrink-0 pt-0.5">URL</span>
                <a href="{{ $infra->system_url }}" target="_blank"
                   class="text-sm text-blue-600 hover:underline break-all">{{ $infra->system_url }}</a>
            </div>
            @endif
            <div class="flex gap-3">
                <span class="text-xs text-gray-400 w-24 flex-shrink-0 pt-0.5">SSL</span>
                @if($infra->ssl_enabled)
                    <span class="inline-flex items-center gap-1.5 text-sm text-green-700">
                        <svg class="w-4 h-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        Habilitado
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 text-sm text-gray-500">
                        <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        No habilitado
                    </span>
                @endif
            </div>
            @if($infra->ssl_enabled && $infra->ssl_expiry)
            <div class="flex gap-3">
                <span class="text-xs text-gray-400 w-24 flex-shrink-0 pt-0.5">Vence</span>
                @php
                $daysLeft = now()->diffInDays($infra->ssl_expiry, false);
                $expiryClass = $daysLeft < 0 ? 'text-red-600' : ($daysLeft < 30 ? 'text-yellow-600' : 'text-green-700');
                @endphp
                <span class="text-sm {{ $expiryClass }} font-medium">
                    {{ $infra->ssl_expiry->format('d/m/Y') }}
                    <span class="font-normal text-xs text-gray-500 ml-1">({{ $infra->ssl_expiry->diffForHumans() }})</span>
                </span>
            </div>
            @endif
            @if($infra->environment)
            <div class="flex gap-3">
                <span class="text-xs text-gray-400 w-24 flex-shrink-0 pt-0.5">Ambiente</span>
                <span class="text-sm text-gray-800">{{ $infra->environment->label() }}</span>
            </div>
            @endif
        </div>
    </div>

    @if($infra->notes)
    <div class="p-4 bg-amber-50 border border-amber-200 rounded-lg">
        <p class="text-xs font-medium text-amber-700 mb-1">Notas</p>
        <p class="text-sm text-amber-800">{{ $infra->notes }}</p>
    </div>
    @endif
    @endif
</div>
