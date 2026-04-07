@extends('layouts.app')
@section('title', 'Reportes')

@section('content')
<div class="space-y-8">

    {{-- ── Header ──────────────────────────────────────────────────── --}}
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Reportes</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Panel de análisis del inventario de TI — OTI UNAMAD</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('reports.excel') }}"
               class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Exportar Excel
            </a>
            <a href="{{ route('reports.pdf') }}"
               class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                Exportar PDF
            </a>
        </div>
    </div>

    {{-- ── KPIs principales ─────────────────────────────────────────── --}}
    @php
    $total       = $systems->count();
    $active      = $byStatus->get('active',      collect())->count();
    $development = $byStatus->get('development', collect())->count();
    $maintenance = $byStatus->get('maintenance', collect())->count();
    $inactive    = $byStatus->get('inactive',    collect())->count();
    $kpis = [
        ['label'=>'Total sistemas',   'val'=>$total,       'color'=>'blue',   'icon'=>'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
        ['label'=>'Activos',          'val'=>$active,      'color'=>'green',  'icon'=>'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
        ['label'=>'En desarrollo',    'val'=>$development, 'color'=>'indigo', 'icon'=>'M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4'],
        ['label'=>'Mantenimiento',    'val'=>$maintenance, 'color'=>'yellow', 'icon'=>'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'],
        ['label'=>'Inactivos',        'val'=>$inactive,    'color'=>'red',    'icon'=>'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z'],
        ['label'=>'Sin servidor',     'val'=>$withoutServer->count(),     'color'=>'slate',  'icon'=>'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636'],
        ['label'=>'Sin repositorio',  'val'=>$withoutRepo->count(),       'color'=>'orange', 'icon'=>'M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4'],
        ['label'=>'Alertas SSL',      'val'=>$sslWarning->count(),        'color'=>'red',    'icon'=>'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z'],
    ];
    @endphp
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-5">
        @foreach($kpis as $kpi)
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-5">
            <div class="w-9 h-9 rounded-xl bg-{{ $kpi['color'] }}-50 dark:bg-{{ $kpi['color'] }}-900/30 flex items-center justify-center mb-4">
                <svg class="w-4 h-4 text-{{ $kpi['color'] }}-600 dark:text-{{ $kpi['color'] }}-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $kpi['icon'] }}"/>
                </svg>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $kpi['val'] }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $kpi['label'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- ── Fila: Sistemas por servidor + por área ───────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-8">

        {{-- Sistemas por servidor --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 flex items-center gap-2">
                <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/>
                </svg>
                <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Sistemas por Servidor</h2>
            </div>
            @if($systemsByServer->isEmpty())
            <p class="px-6 py-8 text-sm text-gray-400 dark:text-gray-500 text-center">Sin servidores con sistemas asignados.</p>
            @else
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach($systemsByServer as $srv)
                @php $pct = $total > 0 ? round($srv->systems->count() / $total * 100) : 0; @endphp
                <div class="px-6 py-4">
                    <div class="flex items-center justify-between mb-1.5">
                        <a href="{{ route('admin.servers.show', $srv) }}"
                           class="text-sm font-medium text-gray-800 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 hover:underline">
                            {{ $srv->name }}
                        </a>
                        <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ $srv->systems->count() }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="flex-1 bg-gray-100 dark:bg-gray-700 rounded-full h-1.5">
                            <div class="bg-blue-500 dark:bg-blue-400 h-1.5 rounded-full" style="width: {{ $pct }}%"></div>
                        </div>
                        <span class="text-xs text-gray-400 dark:text-gray-500 w-8 text-right">{{ $pct }}%</span>
                    </div>
                    <div class="mt-1.5 flex flex-wrap gap-1">
                        @foreach($srv->systems->take(5) as $sys)
                        <span class="inline-block px-1.5 py-0.5 text-xs bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded">
                            {{ $sys->acronym ?? $sys->name }}
                        </span>
                        @endforeach
                        @if($srv->systems->count() > 5)
                        <span class="text-xs text-gray-400 dark:text-gray-500">+{{ $srv->systems->count() - 5 }} más</span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Sistemas por área --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 flex items-center gap-2">
                <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Sistemas por Área</h2>
            </div>
            @php $byArea = $systems->groupBy(fn($s) => $s->area?->name ?? 'Sin área')->sortByDesc(fn($g) => $g->count()); @endphp
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach($byArea as $areaName => $areaSystems)
                @php $pct = $total > 0 ? round($areaSystems->count() / $total * 100) : 0; @endphp
                <div class="px-6 py-4">
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-sm text-gray-700 dark:text-gray-300 truncate max-w-xs">{{ $areaName }}</span>
                        <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ $areaSystems->count() }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="flex-1 bg-gray-100 dark:bg-gray-700 rounded-full h-1.5">
                            <div class="bg-indigo-500 dark:bg-indigo-400 h-1.5 rounded-full" style="width: {{ $pct }}%"></div>
                        </div>
                        <span class="text-xs text-gray-400 dark:text-gray-500 w-8 text-right">{{ $pct }}%</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ── Fila: Motores BD + Repositorios por proveedor ────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-8">

        {{-- BDs por motor --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 flex items-center gap-2">
                <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                </svg>
                <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Bases de Datos por Motor</h2>
            </div>
            @if($dbsByEngine->isEmpty())
            <p class="px-6 py-8 text-sm text-gray-400 dark:text-gray-500 text-center">Sin bases de datos registradas.</p>
            @else
            @php
            $engineColors = [
                'postgresql'=>'blue','mysql'=>'orange','mariadb'=>'teal',
                'oracle'=>'red','sqlserver'=>'slate','sqlite'=>'purple',
                'mongodb'=>'green','other'=>'gray',
            ];
            $engineLabels = [
                'postgresql'=>'PostgreSQL','mysql'=>'MySQL','mariadb'=>'MariaDB',
                'oracle'=>'Oracle','sqlserver'=>'SQL Server','sqlite'=>'SQLite',
                'mongodb'=>'MongoDB','other'=>'Otro',
            ];
            $dbTotal = $dbsByEngine->sum('total');
            @endphp
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach($dbsByEngine as $row)
                @php
                    $color = $engineColors[$row->engine] ?? 'gray';
                    $label = $engineLabels[$row->engine] ?? ucfirst($row->engine);
                    $pct   = $dbTotal > 0 ? round($row->total / $dbTotal * 100) : 0;
                @endphp
                <div class="px-6 py-4 flex items-center gap-3">
                    <span class="w-2 h-2 rounded-full bg-{{ $color }}-500 flex-shrink-0"></span>
                    <span class="text-sm text-gray-700 dark:text-gray-300 flex-1">{{ $label }}</span>
                    <div class="w-24 bg-gray-100 dark:bg-gray-700 rounded-full h-1.5">
                        <div class="bg-{{ $color }}-500 h-1.5 rounded-full" style="width: {{ $pct }}%"></div>
                    </div>
                    <span class="text-sm font-semibold text-gray-700 dark:text-gray-300 w-6 text-right">{{ $row->total }}</span>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Repositorios por proveedor --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 flex items-center gap-2">
                <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                </svg>
                <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Repositorios por Proveedor</h2>
            </div>
            @if($reposByProvider->isEmpty())
            <p class="px-6 py-8 text-sm text-gray-400 dark:text-gray-500 text-center">Sin repositorios registrados.</p>
            @else
            @php
            $provColors  = ['github'=>'slate','gitlab'=>'orange','bitbucket'=>'blue','gitea'=>'teal','other'=>'gray'];
            $provLabels  = ['github'=>'GitHub','gitlab'=>'GitLab','bitbucket'=>'Bitbucket','gitea'=>'Gitea','other'=>'Otro'];
            $repoTotal   = $reposByProvider->sum('total');
            @endphp
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach($reposByProvider as $row)
                @php
                    $color = $provColors[$row->provider] ?? 'gray';
                    $label = $provLabels[$row->provider] ?? ucfirst($row->provider);
                    $pct   = $repoTotal > 0 ? round($row->total / $repoTotal * 100) : 0;
                @endphp
                <div class="px-6 py-4 flex items-center gap-3">
                    <span class="w-2 h-2 rounded-full bg-{{ $color }}-500 flex-shrink-0"></span>
                    <span class="text-sm text-gray-700 dark:text-gray-300 flex-1">{{ $label }}</span>
                    <div class="w-24 bg-gray-100 dark:bg-gray-700 rounded-full h-1.5">
                        <div class="bg-{{ $color }}-500 h-1.5 rounded-full" style="width: {{ $pct }}%"></div>
                    </div>
                    <span class="text-sm font-semibold text-gray-700 dark:text-gray-300 w-6 text-right">{{ $row->total }}</span>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    {{-- ── Sistemas por IP pública ──────────────────────────────────── --}}
    @if($publicIpSystems->count())
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 flex items-center gap-2">
            <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
            </svg>
            <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Sistemas por IP Pública</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700 text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">IP Pública</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Servidor</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Sistemas alojados</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($publicIpSystems as $srv)
                    @foreach($srv->publicIps as $ip)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                        <td class="px-6 py-4 font-mono text-blue-700 dark:text-blue-300">{{ $ip->ip_address }}</td>
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.servers.show', $srv) }}"
                               class="text-gray-800 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 hover:underline font-medium">
                                {{ $srv->name }}
                            </a>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap gap-1">
                                @foreach($srv->systems as $sys)
                                <a href="{{ route('systems.show', $sys) }}"
                                   class="inline-block px-2 py-0.5 text-xs bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-colors">
                                    {{ $sys->acronym ?? $sys->name }}
                                </a>
                                @endforeach
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- ── Alertas SSL ──────────────────────────────────────────────── --}}
    @if($sslWarning->count())
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-red-200 dark:border-red-800 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-red-100 dark:border-red-800 bg-red-50 dark:bg-red-900/20 flex items-center gap-2">
            <svg class="w-4 h-4 text-red-500 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <h2 class="text-sm font-semibold text-red-700 dark:text-red-300">Certificados SSL por vencer o vencidos</h2>
        </div>
        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            @foreach($sslWarning as $sys)
            @php
                $daysLeft = now()->diffInDays($sys->infrastructure->ssl_expiry, false);
                $isExpired = $daysLeft < 0;
            @endphp
            <div class="px-6 py-4 flex items-center justify-between gap-4 hover:bg-gray-50 dark:hover:bg-gray-700/30">
                <div>
                    <a href="{{ route('systems.show', $sys) }}"
                       class="text-sm font-medium text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 hover:underline">
                        {{ $sys->name }}
                    </a>
                    @if($sys->infrastructure?->system_url)
                    <p class="text-xs text-gray-400 dark:text-gray-500 font-mono">{{ $sys->infrastructure->system_url }}</p>
                    @endif
                </div>
                <div class="text-right flex-shrink-0">
                    <p class="text-sm font-semibold {{ $isExpired ? 'text-red-600 dark:text-red-400' : 'text-yellow-600 dark:text-yellow-400' }}">
                        {{ $sys->infrastructure->ssl_expiry->format('d/m/Y') }}
                    </p>
                    <p class="text-xs {{ $isExpired ? 'text-red-500 dark:text-red-400' : 'text-yellow-500 dark:text-yellow-400' }}">
                        {{ $isExpired ? 'Vencido hace ' . abs($daysLeft) . ' días' : 'Vence en ' . $daysLeft . ' días' }}
                    </p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ── Sistemas sin información completa ───────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">

        {{-- Sin servidor --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Sin servidor asignado ({{ $withoutServer->count() }})</h2>
            </div>
            @if($withoutServer->isEmpty())
            <p class="px-6 py-5 text-sm text-green-600 dark:text-green-400 flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Todos los sistemas tienen servidor.
            </p>
            @else
            <ul class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach($withoutServer as $sys)
                <li class="px-6 py-3.5">
                    <a href="{{ route('systems.show', $sys) }}"
                       class="text-sm text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:underline">
                        {{ $sys->name }}
                    </a>
                    @if($sys->area)
                    <span class="text-xs text-gray-400 dark:text-gray-500 ml-1">· {{ $sys->area->name }}</span>
                    @endif
                </li>
                @endforeach
            </ul>
            @endif
        </div>

        {{-- Sin responsable --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-orange-400"></span>
                <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Sin responsable ({{ $withoutResponsible->count() }})</h2>
            </div>
            @if($withoutResponsible->isEmpty())
            <p class="px-6 py-5 text-sm text-green-600 dark:text-green-400 flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Todos tienen responsable asignado.
            </p>
            @else
            <ul class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach($withoutResponsible as $sys)
                <li class="px-6 py-3.5">
                    <a href="{{ route('systems.show', $sys) }}"
                       class="text-sm text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:underline">
                        {{ $sys->name }}
                    </a>
                    @if($sys->area)
                    <span class="text-xs text-gray-400 dark:text-gray-500 ml-1">· {{ $sys->area->name }}</span>
                    @endif
                </li>
                @endforeach
            </ul>
            @endif
        </div>

        {{-- Sin repositorio --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-purple-400"></span>
                <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Sin repositorio ({{ $withoutRepo->count() }})</h2>
            </div>
            @if($withoutRepo->isEmpty())
            <p class="px-6 py-5 text-sm text-green-600 dark:text-green-400 flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Todos tienen repositorio registrado.
            </p>
            @else
            <ul class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach($withoutRepo as $sys)
                <li class="px-6 py-3.5">
                    <a href="{{ route('systems.show', $sys) }}"
                       class="text-sm text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:underline">
                        {{ $sys->name }}
                    </a>
                    @if($sys->area)
                    <span class="text-xs text-gray-400 dark:text-gray-500 ml-1">· {{ $sys->area->name }}</span>
                    @endif
                </li>
                @endforeach
            </ul>
            @endif
        </div>
    </div>

    {{-- ── Tabla completa de sistemas ───────────────────────────────── --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Inventario Completo</h2>
            <span class="text-xs text-gray-400 dark:text-gray-500">{{ $total }} sistemas</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700 text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Sistema</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Área</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Responsable</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Servidor</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">IP Pública</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">BDs</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Repos</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($systems as $sys)
                    @php
                    $color = $sys->status->color();
                    $colorMap = [
                        'green'  => 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300',
                        'blue'   => 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300',
                        'yellow' => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300',
                        'red'    => 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300',
                    ];
                    $badgeClass  = $colorMap[$color] ?? $colorMap['blue'];
                    $server      = $sys->infrastructure?->server;
                    $publicIp    = $server?->publicIps->first()?->ip_address;
                    @endphp
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                        <td class="px-6 py-4">
                            <a href="{{ route('systems.show', $sys) }}"
                               class="font-medium text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 hover:underline">
                                {{ $sys->name }}
                            </a>
                            @if($sys->acronym)
                            <span class="ml-1 text-xs text-gray-400 dark:text-gray-500 font-mono">{{ $sys->acronym }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-gray-600 dark:text-gray-300 text-xs">{{ $sys->area?->name ?? '—' }}</td>
                        <td class="px-6 py-4 text-gray-600 dark:text-gray-300 text-xs">{{ $sys->responsible?->name ?? '—' }}</td>
                        <td class="px-6 py-4 text-xs">
                            @if($server)
                            <a href="{{ route('admin.servers.show', $server) }}"
                               class="text-blue-600 dark:text-blue-400 hover:underline font-mono">{{ $server->name }}</a>
                            @else
                            <span class="text-gray-400 dark:text-gray-500">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 font-mono text-xs text-gray-600 dark:text-gray-300">{{ $publicIp ?? '—' }}</td>
                        <td class="px-6 py-4 text-center text-gray-600 dark:text-gray-300 text-xs">{{ $sys->databases->count() ?: '—' }}</td>
                        <td class="px-6 py-4 text-center text-gray-600 dark:text-gray-300 text-xs">{{ $sys->repositories->count() ?: '—' }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $badgeClass }}">
                                {{ $sys->status->label() }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-5 py-10 text-center text-sm text-gray-400 dark:text-gray-500">
                            No hay sistemas registrados.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
