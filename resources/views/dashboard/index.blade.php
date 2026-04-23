@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
@php
    $hour  = now()->hour;
    $greeting = $hour < 12 ? 'Buenos días' : ($hour < 18 ? 'Buenas tardes' : 'Buenas noches');
    $firstName = explode(' ', auth()->user()->name)[0];
    $total = max($stats['total'], 1);

    $months = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
    $days   = ['domingo','lunes','martes','miércoles','jueves','viernes','sábado'];
    $dateStr = $days[now()->dayOfWeek] . ', ' . now()->day . ' de ' . $months[now()->month - 1] . ' de ' . now()->year;
@endphp

<div class="space-y-6">

    {{-- ── Header ───────────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                {{ $greeting }}, {{ $firstName }}
            </h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Sistema de Gestión de Activos de TI — UNAMAD
            </p>
        </div>
        <div class="inline-flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400
                    bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700
                    px-4 py-2 rounded-lg shadow-sm self-start sm:self-auto">
            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <span class="capitalize">{{ $dateStr }}</span>
        </div>
    </div>

    {{-- ── Stat Cards Principales ────────────────────────────────────── --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">

        {{-- Total --}}
        <a href="{{ route('systems.index') }}"
           class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700
                  shadow-sm p-5 hover:shadow-md transition-shadow group">
            <div class="flex items-center justify-between mb-3">
                <div class="p-2 bg-blue-50 dark:bg-blue-900/30 rounded-lg">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <svg class="w-4 h-4 text-gray-300 dark:text-gray-600 group-hover:text-blue-400 dark:group-hover:text-blue-400 transition-colors"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </div>
            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['total'] }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Total Sistemas</p>
        </a>

        {{-- Activos --}}
        <a href="{{ route('systems.index') }}?status=active"
           class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700
                  shadow-sm p-5 hover:shadow-md transition-shadow group">
            <div class="flex items-center justify-between mb-3">
                <div class="p-2 bg-emerald-50 dark:bg-emerald-900/30 rounded-lg">
                    <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="text-xs font-semibold text-emerald-600 dark:text-emerald-400
                             bg-emerald-50 dark:bg-emerald-900/30 px-2 py-0.5 rounded-full">
                    {{ round($stats['active'] / $total * 100) }}%
                </span>
            </div>
            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['active'] }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Activos</p>
            <div class="mt-2 h-1 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                <div class="h-1 bg-emerald-500 rounded-full"
                     style="width: {{ round($stats['active'] / $total * 100) }}%"></div>
            </div>
        </a>

        {{-- En Desarrollo --}}
        <a href="{{ route('systems.index') }}?status=development"
           class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700
                  shadow-sm p-5 hover:shadow-md transition-shadow group">
            <div class="flex items-center justify-between mb-3">
                <div class="p-2 bg-indigo-50 dark:bg-indigo-900/30 rounded-lg">
                    <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                    </svg>
                </div>
                <span class="text-xs font-semibold text-indigo-600 dark:text-indigo-400
                             bg-indigo-50 dark:bg-indigo-900/30 px-2 py-0.5 rounded-full">
                    {{ round($stats['development'] / $total * 100) }}%
                </span>
            </div>
            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['development'] }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">En Desarrollo</p>
            <div class="mt-2 h-1 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                <div class="h-1 bg-indigo-500 rounded-full"
                     style="width: {{ round($stats['development'] / $total * 100) }}%"></div>
            </div>
        </a>

        {{-- Mantenimiento --}}
        <a href="{{ route('systems.index') }}?status=maintenance"
           class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700
                  shadow-sm p-5 hover:shadow-md transition-shadow group">
            <div class="flex items-center justify-between mb-3">
                <div class="p-2 bg-amber-50 dark:bg-amber-900/30 rounded-lg">
                    <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <span class="text-xs font-semibold text-amber-600 dark:text-amber-400
                             bg-amber-50 dark:bg-amber-900/30 px-2 py-0.5 rounded-full">
                    {{ round($stats['maintenance'] / $total * 100) }}%
                </span>
            </div>
            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['maintenance'] }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Mantenimiento</p>
            <div class="mt-2 h-1 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                <div class="h-1 bg-amber-500 rounded-full"
                     style="width: {{ round($stats['maintenance'] / $total * 100) }}%"></div>
            </div>
        </a>

        {{-- Inactivos --}}
        <a href="{{ route('systems.index') }}?status=inactive"
           class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700
                  shadow-sm p-5 hover:shadow-md transition-shadow group">
            <div class="flex items-center justify-between mb-3">
                <div class="p-2 bg-red-50 dark:bg-red-900/30 rounded-lg">
                    <svg class="w-5 h-5 text-red-500 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                    </svg>
                </div>
                <span class="text-xs font-semibold text-red-600 dark:text-red-400
                             bg-red-50 dark:bg-red-900/30 px-2 py-0.5 rounded-full">
                    {{ round($stats['inactive'] / $total * 100) }}%
                </span>
            </div>
            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['inactive'] }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Inactivos</p>
            <div class="mt-2 h-1 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                <div class="h-1 bg-red-500 rounded-full"
                     style="width: {{ round($stats['inactive'] / $total * 100) }}%"></div>
            </div>
        </a>
    </div>

    {{-- ── Secundarios: Infraestructura ─────────────────────────────── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

        {{-- Servidores --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700
                    shadow-sm p-4 flex items-center gap-4">
            <div class="p-3 bg-slate-100 dark:bg-slate-700 rounded-lg shrink-0">
                <svg class="w-5 h-5 text-slate-600 dark:text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-2xl font-bold text-gray-900 dark:text-white leading-none">
                    {{ $serverStats['active'] }}<span class="text-sm font-normal text-gray-400 dark:text-gray-500">/{{ $serverStats['total'] }}</span>
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Servidores activos</p>
            </div>
        </div>

        {{-- Repositorios --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700
                    shadow-sm p-4 flex items-center gap-4">
            <div class="p-3 bg-violet-100 dark:bg-violet-900/30 rounded-lg shrink-0">
                <svg class="w-5 h-5 text-violet-600 dark:text-violet-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-2xl font-bold text-gray-900 dark:text-white leading-none">{{ $repoCount }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Repositorios activos</p>
            </div>
        </div>

        {{-- Bases de datos --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700
                    shadow-sm p-4 flex items-center gap-4">
            <div class="p-3 bg-cyan-100 dark:bg-cyan-900/30 rounded-lg shrink-0">
                <svg class="w-5 h-5 text-cyan-600 dark:text-cyan-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-2xl font-bold text-gray-900 dark:text-white leading-none">{{ $dbCount }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Bases de datos</p>
            </div>
        </div>

        {{-- Áreas --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700
                    shadow-sm p-4 flex items-center gap-4">
            <div class="p-3 bg-orange-100 dark:bg-orange-900/30 rounded-lg shrink-0">
                <svg class="w-5 h-5 text-orange-600 dark:text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-2xl font-bold text-gray-900 dark:text-white leading-none">{{ $areaStats->count() }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Áreas con sistemas</p>
            </div>
        </div>
    </div>

    {{-- ── Contenido Principal ───────────────────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Sistemas Recientes --}}
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700
                    shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Sistemas Recientes</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                        Últimos {{ $recentSystems->count() }} actualizados
                    </p>
                </div>
                <a href="{{ route('systems.index') }}"
                   class="inline-flex items-center gap-1 text-xs font-medium text-blue-600 dark:text-blue-400
                          hover:text-blue-700 dark:hover:text-blue-300 transition-colors">
                    Ver todos
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-gray-700/50">
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wider">Sistema</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wider hidden sm:table-cell">Área</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wider">Estado</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wider hidden md:table-cell">Actualizado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                        @forelse($recentSystems as $system)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors cursor-pointer"
                            onclick="window.location='{{ route('systems.show', $system) }}'">
                            <td class="px-6 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600
                                                flex items-center justify-center text-white font-bold text-xs shrink-0">
                                        {{ strtoupper(substr($system->acronym ?? $system->name, 0, 2)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                            {{ $system->name }}
                                        </p>
                                        @if($system->acronym)
                                        <p class="text-xs text-gray-400 dark:text-gray-500">{{ $system->acronym }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3.5 hidden sm:table-cell">
                                <span class="text-xs text-gray-600 dark:text-gray-400">
                                    {{ $system->area->acronym ?? $system->area->name ?? '—' }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5">
                                <x-status-badge :status="$system->status" />
                            </td>
                            <td class="px-4 py-3.5 hidden md:table-cell">
                                <span class="text-xs text-gray-400 dark:text-gray-500">
                                    {{ $system->updated_at->diffForHumans() }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center">
                                <svg class="mx-auto h-10 w-10 text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                <p class="mt-2 text-sm text-gray-400 dark:text-gray-500">No hay sistemas registrados</p>
                                @can('systems.create')
                                <a href="{{ route('systems.create') }}"
                                   class="mt-2 inline-block text-sm text-blue-600 dark:text-blue-400 hover:underline">
                                    Crear primer sistema →
                                </a>
                                @endcan
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Panel de Alertas --}}
        <div class="space-y-4">

            {{-- SSL Vencido --}}
            @if($sslExpired->count())
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-red-200 dark:border-red-800 shadow-sm overflow-hidden">
                <div class="px-5 py-3 bg-red-50 dark:bg-red-900/20 border-b border-red-100 dark:border-red-800
                            flex items-center gap-2">
                    <span class="relative flex h-2 w-2 shrink-0">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
                    </span>
                    <h4 class="text-xs font-semibold text-red-700 dark:text-red-300 uppercase tracking-wide">SSL Vencido</h4>
                    <span class="ml-auto flex items-center justify-center w-5 h-5 text-xs font-bold
                                 text-white bg-red-500 rounded-full shrink-0">
                        {{ $sslExpired->count() }}
                    </span>
                </div>
                <div class="p-4 space-y-1">
                    @foreach($sslExpired->take(3) as $infra)
                    <div class="flex items-center justify-between py-1.5
                                border-b border-gray-50 dark:border-gray-700/50 last:border-0">
                        <div class="min-w-0">
                            <p class="text-xs font-medium text-gray-900 dark:text-white truncate">
                                {{ $infra->system->name }}
                            </p>
                            <p class="text-xs text-red-500 dark:text-red-400">
                                Venció {{ $infra->effectiveSslExpiry()?->diffForHumans() ?? '—' }}
                            </p>
                        </div>
                        <svg class="w-4 h-4 text-red-400 shrink-0 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    @endforeach
                    @if($sslExpired->count() > 3)
                    <p class="text-xs text-gray-400 dark:text-gray-500 text-center pt-1">
                        y {{ $sslExpired->count() - 3 }} más…
                    </p>
                    @endif
                </div>
            </div>
            @endif

            {{-- SSL Próximo a Vencer --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700
                        shadow-sm overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100 dark:border-gray-700 flex items-center gap-2">
                    <svg class="w-4 h-4 text-amber-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    <h4 class="text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">
                        Alertas SSL <span class="normal-case font-normal text-gray-400 dark:text-gray-500">(30 días)</span>
                    </h4>
                    @if($sslExpiring->count())
                    <span class="ml-auto flex items-center justify-center w-5 h-5 text-xs font-bold
                                 text-white bg-amber-500 rounded-full shrink-0">
                        {{ $sslExpiring->count() }}
                    </span>
                    @endif
                </div>
                <div class="p-4">
                    @forelse($sslExpiring as $infra)
                    @php $expiry = $infra->effectiveSslExpiry(); $days = $expiry ? now()->diffInDays($expiry, false) : null; @endphp
                    <div class="flex items-center justify-between py-1.5
                                border-b border-gray-50 dark:border-gray-700/50 last:border-0">
                        <div class="min-w-0">
                            <p class="text-xs font-medium text-gray-900 dark:text-white truncate">
                                {{ $infra->system->name }}
                            </p>
                            <p class="text-xs text-amber-600 dark:text-amber-400">
                                {{ $expiry?->diffForHumans() ?? '—' }}
                            </p>
                        </div>
                        @if($days !== null)
                        <span class="text-xs font-bold shrink-0 ml-2
                                     {{ $days <= 7 ? 'text-red-600 dark:text-red-400' : 'text-amber-600 dark:text-amber-400' }}">
                            {{ $days }}d
                        </span>
                        @endif
                    </div>
                    @empty
                    <div class="text-center py-5">
                        <svg class="mx-auto h-8 w-8 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="mt-1.5 text-xs text-gray-400 dark:text-gray-500">Sin alertas SSL próximas</p>
                    </div>
                    @endforelse
                </div>
            </div>

            {{-- Sistemas activos sin infraestructura --}}
            @if($systemsWithoutInfra > 0)
            <div class="bg-orange-50 dark:bg-orange-900/20 rounded-xl border border-orange-200 dark:border-orange-800
                        p-4 flex items-start gap-3">
                <svg class="w-5 h-5 text-orange-500 dark:text-orange-400 shrink-0 mt-0.5"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <p class="text-xs font-semibold text-orange-800 dark:text-orange-200">
                        {{ $systemsWithoutInfra }} {{ $systemsWithoutInfra === 1 ? 'sistema activo' : 'sistemas activos' }}
                        sin infraestructura
                    </p>
                    <p class="text-xs text-orange-600 dark:text-orange-400 mt-0.5">
                        Sin servidor ni URL registrada
                    </p>
                </div>
            </div>
            @endif

            {{-- Todo OK --}}
            @if($sslExpired->count() === 0 && $sslExpiring->count() === 0 && $systemsWithoutInfra === 0)
            <div class="bg-emerald-50 dark:bg-emerald-900/20 rounded-xl border border-emerald-200 dark:border-emerald-800
                        p-5 text-center">
                <svg class="mx-auto h-8 w-8 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                <p class="mt-2 text-xs font-semibold text-emerald-700 dark:text-emerald-300">Sin alertas activas</p>
                <p class="text-xs text-emerald-600 dark:text-emerald-400 mt-0.5">Todo en orden</p>
            </div>
            @endif
        </div>
    </div>

    {{-- ── Fila inferior ─────────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Distribución por Área --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700
                    shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Distribución por Área</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Sistemas por unidad organizativa</p>
            </div>
            <div class="p-6 space-y-4">
                @forelse($areaStats as $area)
                @php $pct = round($area->systems_count / $total * 100); @endphp
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <div class="flex items-center gap-2 min-w-0">
                            @if($area->acronym)
                            <span class="text-xs font-mono font-bold text-gray-400 dark:text-gray-500 shrink-0 w-10 text-right">
                                {{ $area->acronym }}
                            </span>
                            @endif
                            <span class="text-xs font-medium text-gray-700 dark:text-gray-300 truncate">
                                {{ $area->name }}
                            </span>
                        </div>
                        <div class="flex items-center gap-2 shrink-0 ml-3">
                            <span class="text-xs font-bold text-gray-900 dark:text-white">
                                {{ $area->systems_count }}
                            </span>
                            <span class="text-xs text-gray-400 dark:text-gray-500 w-8 text-right">
                                {{ $pct }}%
                            </span>
                        </div>
                    </div>
                    <div class="h-1.5 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                        <div class="h-1.5 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-full"
                             style="width: {{ $pct }}%"></div>
                    </div>
                </div>
                @empty
                <p class="text-sm text-gray-400 dark:text-gray-500 text-center py-6">Sin áreas registradas</p>
                @endforelse
            </div>
        </div>

        {{-- Últimas Versiones --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700
                    shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Últimas Versiones</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Despliegues recientes</p>
            </div>
            <div class="divide-y divide-gray-50 dark:divide-gray-700/50">
                @forelse($recentVersions as $version)
                @php
                    $envColors = [
                        'production'  => 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300',
                        'staging'     => 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300',
                        'development' => 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300',
                    ];
                    $envKey   = $version->environment?->value ?? 'production';
                    $envColor = $envColors[$envKey] ?? 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400';
                @endphp
                <div class="px-6 py-3.5 flex items-start gap-3">
                    <span class="inline-flex items-center px-2 py-0.5 text-[10px] font-bold rounded
                                 {{ $envColor }} shrink-0 mt-0.5">
                        v{{ $version->version }}
                    </span>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-medium text-gray-900 dark:text-white truncate">
                            {{ $version->system->name }}
                        </p>
                        @if($version->changes)
                        <p class="text-xs text-gray-400 dark:text-gray-500 truncate mt-0.5">
                            {{ \Illuminate\Support\Str::limit($version->changes, 55) }}
                        </p>
                        @endif
                    </div>
                    <div class="text-right shrink-0">
                        <p class="text-xs text-gray-400 dark:text-gray-500">
                            {{ $version->release_date->format('d/m/Y') }}
                        </p>
                        @if($version->git_branch)
                        <p class="text-[10px] font-mono text-gray-300 dark:text-gray-600 mt-0.5">
                            {{ $version->git_branch }}
                        </p>
                        @endif
                    </div>
                </div>
                @empty
                <div class="px-6 py-10 text-center">
                    <svg class="mx-auto h-8 w-8 text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                    <p class="mt-2 text-xs text-gray-400 dark:text-gray-500">Sin versiones registradas</p>
                </div>
                @endforelse
            </div>
        </div>

    </div>
</div>
@endsection
