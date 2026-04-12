@extends('layouts.app')

@section('title', $sslCertificate->name)

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.ssl-certificates.index') }}"
               class="inline-flex items-center justify-center w-10 h-10 rounded-lg
                      bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300
                      hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                @php
                    $isExpired = $sslCertificate->isExpired();
                    $isSoon    = $sslCertificate->isExpiringSoon();
                    $daysLeft  = $sslCertificate->daysUntilExpiry();
                @endphp
                <div class="flex items-center gap-3 flex-wrap">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $sslCertificate->name }}</h1>
                    @if($isExpired)
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium
                                 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400">
                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>Vencido
                    </span>
                    @elseif($isSoon)
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium
                                 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400">
                        <span class="w-1.5 h-1.5 rounded-full bg-yellow-500 animate-pulse"></span>Por vencer
                    </span>
                    @elseif($sslCertificate->valid_until)
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium
                                 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Vigente
                    </span>
                    @endif
                </div>
                @if($sslCertificate->common_name)
                <p class="mt-1 text-sm font-mono text-gray-500 dark:text-gray-400">{{ $sslCertificate->common_name }}</p>
                @endif
            </div>
        </div>
        <a href="{{ route('admin.ssl-certificates.edit', $sslCertificate) }}"
           class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white
                  bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
            </svg>
            Editar
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

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Detalles --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
            <div class="px-5 py-4 bg-gradient-to-r from-gray-50 to-white dark:from-gray-700/50 dark:to-gray-800
                        border-b border-gray-200 dark:border-gray-700">
                <h4 class="text-sm font-semibold text-gray-900 dark:text-white">Detalles del Certificado</h4>
            </div>
            <div class="p-5 space-y-4">
                @foreach([
                    ['label' => 'Emisor (CA)',    'value' => $sslCertificate->issuer],
                    ['label' => 'Common Name',    'value' => $sslCertificate->common_name, 'mono' => true],
                ] as $row)
                @if($row['value'])
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ $row['label'] }}</p>
                    <p class="text-sm {{ ($row['mono'] ?? false) ? 'font-mono' : '' }} text-gray-900 dark:text-gray-200 mt-0.5">{{ $row['value'] }}</p>
                </div>
                @endif
                @endforeach

                <div class="grid grid-cols-2 gap-4">
                    @if($sslCertificate->valid_from)
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Válido desde</p>
                        <p class="text-sm text-gray-900 dark:text-gray-200 mt-0.5">{{ $sslCertificate->valid_from->format('d/m/Y') }}</p>
                    </div>
                    @endif
                    @if($sslCertificate->valid_until)
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Vence el</p>
                        @php $color = $isExpired ? 'red' : ($isSoon ? 'yellow' : 'emerald'); @endphp
                        <p class="text-sm font-semibold text-{{ $color }}-600 dark:text-{{ $color }}-400 mt-0.5">
                            {{ $sslCertificate->valid_until->format('d/m/Y') }}
                        </p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                            @if($isExpired) Vencido hace {{ abs($daysLeft) }} días
                            @elseif($isSoon) Vence en {{ $daysLeft }} días
                            @else {{ $sslCertificate->valid_until->diffForHumans() }}
                            @endif
                        </p>
                    </div>
                    @endif
                </div>

                @if($sslCertificate->notes)
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Notas</p>
                    <p class="text-sm text-gray-700 dark:text-gray-300 mt-0.5 whitespace-pre-wrap">{{ $sslCertificate->notes }}</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Archivos --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
            <div class="px-5 py-4 bg-gradient-to-r from-gray-50 to-white dark:from-gray-700/50 dark:to-gray-800
                        border-b border-gray-200 dark:border-gray-700">
                <h4 class="text-sm font-semibold text-gray-900 dark:text-white">Archivos Almacenados</h4>
            </div>
            <div class="p-5 space-y-3">
                @php
                    $files = [
                        ['path' => $sslCertificate->pfx_file_path,   'label' => 'Archivo PFX',       'route' => route('admin.ssl-certificates.download.pfx',   $sslCertificate), 'color' => 'violet'],
                        ['path' => $sslCertificate->cert_file_path,  'label' => 'Certificado',       'route' => route('admin.ssl-certificates.download.cert',  $sslCertificate), 'color' => 'blue'],
                        ['path' => $sslCertificate->key_file_path,   'label' => 'Llave privada',     'route' => route('admin.ssl-certificates.download.key',   $sslCertificate), 'color' => 'amber'],
                        ['path' => $sslCertificate->chain_file_path, 'label' => 'Cadena intermedia', 'route' => route('admin.ssl-certificates.download.chain', $sslCertificate), 'color' => 'purple'],
                    ];
                    $hasAnyFile = collect($files)->filter(fn($f) => $f['path'])->isNotEmpty();
                @endphp

                @if(!$hasAnyFile)
                <p class="text-sm text-gray-400 dark:text-gray-500 italic text-center py-4">No hay archivos cargados.</p>
                @else
                @foreach($files as $file)
                @if($file['path'])
                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <div class="flex items-center gap-2 min-w-0">
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-mono font-bold
                                     bg-{{ $file['color'] }}-100 dark:bg-{{ $file['color'] }}-900/30
                                     text-{{ $file['color'] }}-700 dark:text-{{ $file['color'] }}-300 flex-shrink-0">
                            {{ strtoupper(pathinfo($file['path'], PATHINFO_EXTENSION)) }}
                        </span>
                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $file['label'] }}</span>
                    </div>
                    <a href="{{ $file['route'] }}"
                       class="inline-flex items-center gap-1.5 text-xs font-medium text-blue-600 dark:text-blue-400
                              hover:text-blue-700 dark:hover:text-blue-300 transition-colors flex-shrink-0">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Descargar
                    </a>
                </div>
                @endif
                @endforeach
                @endif
            </div>
        </div>
    </div>

    {{-- Sistemas que usan este certificado --}}
    @if($sslCertificate->infrastructures->isNotEmpty())
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
        <div class="px-5 py-4 bg-gradient-to-r from-gray-50 to-white dark:from-gray-700/50 dark:to-gray-800
                    border-b border-gray-200 dark:border-gray-700">
            <h4 class="text-sm font-semibold text-gray-900 dark:text-white">
                Sistemas que usan este certificado
                <span class="ml-2 px-1.5 py-0.5 text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 rounded">
                    {{ $sslCertificate->infrastructures->count() }}
                </span>
            </h4>
        </div>
        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            @foreach($sslCertificate->infrastructures as $infra)
            <div class="px-5 py-3 flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $infra->system->name }}</p>
                    @if($infra->system_url)
                    <p class="text-xs text-gray-400 dark:text-gray-500 font-mono mt-0.5">{{ $infra->system_url }}</p>
                    @endif
                </div>
                <a href="{{ route('systems.show', $infra->system) }}"
                   class="text-xs text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300">
                    Ver sistema →
                </a>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>
@endsection
