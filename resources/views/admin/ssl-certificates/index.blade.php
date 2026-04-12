@extends('layouts.app')

@section('title', 'Certificados SSL')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Certificados SSL</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Certificados digitales gestionados por la OTI</p>
        </div>
        <a href="{{ route('admin.ssl-certificates.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white
                  text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nuevo Certificado
        </a>
    </div>

    @if(session('success'))
    <div class="flex items-center gap-3 px-4 py-3 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-700 rounded-lg text-sm text-emerald-700 dark:text-emerald-300">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- Tarjetas resumen --}}
    @php
        $total    = $certificates->count();
        $expired  = $certificates->filter(fn($c) => $c->isExpired())->count();
        $soon     = $certificates->filter(fn($c) => !$c->isExpired() && $c->isExpiringSoon())->count();
        $valid    = $total - $expired - $soon;
    @endphp
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        @foreach([
            ['label' => 'Total',        'value' => $total,   'color' => 'text-gray-900 dark:text-white'],
            ['label' => 'Vigentes',     'value' => $valid,   'color' => 'text-emerald-600 dark:text-emerald-400'],
            ['label' => 'Por vencer',   'value' => $soon,    'color' => 'text-yellow-600 dark:text-yellow-400'],
            ['label' => 'Vencidos',     'value' => $expired, 'color' => 'text-red-600 dark:text-red-400'],
        ] as $card)
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 px-4 py-3">
            <p class="text-[10px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">{{ $card['label'] }}</p>
            <p class="mt-1 text-2xl font-bold {{ $card['color'] }}">{{ $card['value'] }}</p>
        </div>
        @endforeach
    </div>

    @if($certificates->isEmpty())
    <div class="text-center py-16 bg-gray-50 dark:bg-gray-800 rounded-xl border-2 border-dashed border-gray-200 dark:border-gray-700">
        <div class="p-3 bg-gray-100 dark:bg-gray-700 rounded-full w-fit mx-auto mb-4">
            <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
        </div>
        <h4 class="text-base font-semibold text-gray-700 dark:text-gray-300 mb-1">Sin certificados registrados</h4>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Registra el primer certificado SSL de la OTI.</p>
        <a href="{{ route('admin.ssl-certificates.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-blue-600 dark:text-blue-400
                  bg-blue-50 dark:bg-blue-900/30 hover:bg-blue-100 dark:hover:bg-blue-900/50 rounded-lg transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Registrar certificado
        </a>
    </div>
    @else
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        @foreach($certificates as $cert)
        @php
            $daysLeft   = $cert->daysUntilExpiry();
            $isExpired  = $cert->isExpired();
            $isSoon     = $cert->isExpiringSoon();
            $statusColor = $isExpired ? 'red' : ($isSoon ? 'yellow' : 'emerald');
        @endphp
        <div class="bg-white dark:bg-gray-800 rounded-xl border
                    {{ $isExpired ? 'border-red-200 dark:border-red-800' : ($isSoon ? 'border-yellow-200 dark:border-yellow-800' : 'border-gray-200 dark:border-gray-700') }}
                    shadow-sm overflow-hidden">
            <div class="px-5 py-4 flex items-start justify-between gap-3">
                <div class="flex items-start gap-3 min-w-0">
                    <div class="p-2 bg-{{ $statusColor }}-50 dark:bg-{{ $statusColor }}-900/30 rounded-lg flex-shrink-0 mt-0.5">
                        <svg class="w-5 h-5 text-{{ $statusColor }}-600 dark:text-{{ $statusColor }}-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <a href="{{ route('admin.ssl-certificates.show', $cert) }}"
                           class="font-semibold text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 truncate block">
                            {{ $cert->name }}
                        </a>
                        @if($cert->common_name)
                        <p class="text-xs font-mono text-gray-500 dark:text-gray-400 mt-0.5">{{ $cert->common_name }}</p>
                        @endif
                        @if($cert->issuer)
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ $cert->issuer }}</p>
                        @endif
                    </div>
                </div>
                <div class="flex-shrink-0 text-right">
                    @if($cert->valid_until)
                    <p class="text-sm font-semibold text-{{ $statusColor }}-600 dark:text-{{ $statusColor }}-400">
                        {{ $cert->valid_until->format('d/m/Y') }}
                    </p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                        @if($isExpired) Vencido hace {{ abs($daysLeft) }}d
                        @elseif($isSoon) Vence en {{ $daysLeft }}d
                        @else {{ $cert->valid_until->diffForHumans() }}
                        @endif
                    </p>
                    @else
                    <p class="text-xs text-gray-400 dark:text-gray-500">Sin fecha</p>
                    @endif
                </div>
            </div>
            <div class="px-5 pb-4 flex items-center justify-between">
                <div class="flex items-center gap-3 text-xs text-gray-500 dark:text-gray-400">
                    {{-- Archivos disponibles --}}
                    @foreach([['cert_file_path','CRT','blue'], ['key_file_path','KEY','amber'], ['chain_file_path','CHAIN','purple']] as [$field,$label,$color])
                    @if($cert->$field)
                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-mono font-semibold
                                 bg-{{ $color }}-50 dark:bg-{{ $color }}-900/30 text-{{ $color }}-700 dark:text-{{ $color }}-300">
                        {{ $label }}
                    </span>
                    @endif
                    @endforeach
                    @if($cert->infrastructures_count > 0)
                    <span class="text-gray-400">· {{ $cert->infrastructures_count }} sistema(s)</span>
                    @endif
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.ssl-certificates.edit', $cert) }}"
                       class="text-xs text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                        Editar
                    </a>
                    <a href="{{ route('admin.ssl-certificates.show', $cert) }}"
                       class="text-xs text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition-colors">
                        Ver detalle →
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

</div>
@endsection
