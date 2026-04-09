@extends('layouts.app')

@section('title', $databaseServer->engine_label . ' — ' . $server->name)

@section('content')
@php
    $engineColors = [
        'postgresql' => ['bg' => 'bg-blue-100 dark:bg-blue-900/40',   'text' => 'text-blue-700 dark:text-blue-300',   'dot' => 'bg-blue-500'],
        'mysql'      => ['bg' => 'bg-orange-100 dark:bg-orange-900/40','text' => 'text-orange-700 dark:text-orange-300','dot' => 'bg-orange-500'],
        'mariadb'    => ['bg' => 'bg-amber-100 dark:bg-amber-900/40',  'text' => 'text-amber-700 dark:text-amber-300',  'dot' => 'bg-amber-500'],
        'oracle'     => ['bg' => 'bg-red-100 dark:bg-red-900/40',      'text' => 'text-red-700 dark:text-red-300',      'dot' => 'bg-red-500'],
        'sqlserver'  => ['bg' => 'bg-sky-100 dark:bg-sky-900/40',      'text' => 'text-sky-700 dark:text-sky-300',      'dot' => 'bg-sky-500'],
        'sqlite'     => ['bg' => 'bg-teal-100 dark:bg-teal-900/40',    'text' => 'text-teal-700 dark:text-teal-300',    'dot' => 'bg-teal-500'],
        'mongodb'    => ['bg' => 'bg-green-100 dark:bg-green-900/40',  'text' => 'text-green-700 dark:text-green-300',  'dot' => 'bg-green-500'],
        'other'      => ['bg' => 'bg-gray-100 dark:bg-gray-700',       'text' => 'text-gray-600 dark:text-gray-300',    'dot' => 'bg-gray-400'],
    ];
    $ec = $engineColors[$databaseServer->engine] ?? $engineColors['other'];

    $envColors = [
        'produccion'  => 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400',
        'testing'     => 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400',
        'development' => 'bg-sky-100 dark:bg-sky-900/30 text-sky-700 dark:text-sky-400',
        'staging'     => 'bg-violet-100 dark:bg-violet-900/30 text-violet-700 dark:text-violet-400',
    ];
@endphp

<div class="space-y-6">

    {{-- ── Encabezado ── --}}
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.servers.show', $server) }}"
               class="inline-flex items-center justify-center w-10 h-10 rounded-lg
                      bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300
                      hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                {{-- Breadcrumb --}}
                <p class="text-xs text-gray-400 dark:text-gray-500 mb-1 flex items-center gap-1">
                    <a href="{{ route('admin.servers.index') }}" class="hover:text-gray-600 dark:hover:text-gray-300 transition-colors">Servidores</a>
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                    <a href="{{ route('admin.servers.show', $server) }}" class="hover:text-gray-600 dark:hover:text-gray-300 transition-colors">{{ $server->name }}</a>
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                    Motores de BD
                </p>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ $databaseServer->name ?: $databaseServer->engine_label }}
                    </h1>
                    @if($databaseServer->is_active)
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
            </div>
        </div>

        {{-- Acciones --}}
        <div class="flex items-center gap-2 flex-shrink-0">
            <button onclick="openEditModal()"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium
                           text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/30
                           border border-blue-200 dark:border-blue-700/50 rounded-lg
                           hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                </svg>
                Editar
            </button>
        </div>
    </div>

    {{-- ── Stats rápidas ── --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-violet-600 dark:text-violet-400">{{ $databaseServer->databases->count() }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Bases de datos</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">
                {{ $databaseServer->databases->pluck('system_id')->filter()->unique()->count() }}
            </p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Sistemas vinculados</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-4 text-center">
            <p class="text-xl font-bold text-gray-700 dark:text-gray-300 font-mono">
                {{ $databaseServer->port ?? '—' }}
            </p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Puerto</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-4 text-center">
            <p class="text-xl font-bold text-gray-700 dark:text-gray-300 font-mono truncate">
                {{ $databaseServer->version ?? '—' }}
            </p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Versión</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ── Columna izquierda: info del motor ── --}}
        <div class="space-y-5">

            {{-- Identificación --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="px-5 py-3.5 border-b border-gray-100 dark:border-gray-700 flex items-center gap-2">
                    <svg class="w-4 h-4 text-violet-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582 4-8 4m16 5c0 2.21-3.582 4-8 4S4 14.21 4 12"/>
                    </svg>
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Motor</h3>
                </div>
                <dl class="divide-y divide-gray-100 dark:divide-gray-700/60">
                    <div class="flex items-center justify-between px-5 py-3">
                        <dt class="text-xs text-gray-500 dark:text-gray-400">Alias</dt>
                        <dd class="text-sm font-medium text-gray-800 dark:text-gray-200">
                            {{ $databaseServer->name ?: '—' }}
                        </dd>
                    </div>
                    <div class="flex items-center justify-between px-5 py-3">
                        <dt class="text-xs text-gray-500 dark:text-gray-400">Motor</dt>
                        <dd>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold {{ $ec['bg'] }} {{ $ec['text'] }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $ec['dot'] }}"></span>
                                {{ ucfirst($databaseServer->engine) }}
                            </span>
                        </dd>
                    </div>
                    <div class="flex items-center justify-between px-5 py-3">
                        <dt class="text-xs text-gray-500 dark:text-gray-400">Versión</dt>
                        <dd class="text-sm font-mono text-gray-800 dark:text-gray-200">{{ $databaseServer->version ?: '—' }}</dd>
                    </div>
                    <div class="flex items-center justify-between px-5 py-3">
                        <dt class="text-xs text-gray-500 dark:text-gray-400">Estado</dt>
                        <dd>
                            @if($databaseServer->is_active)
                            <span class="inline-flex items-center gap-1 text-xs text-emerald-600 dark:text-emerald-400 font-medium">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Activo
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400 font-medium">
                                <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>Inactivo
                            </span>
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>

            {{-- Conexión --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="px-5 py-3.5 border-b border-gray-100 dark:border-gray-700 flex items-center gap-2">
                    <svg class="w-4 h-4 text-sky-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.142 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/>
                    </svg>
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Conexión</h3>
                </div>
                <dl class="divide-y divide-gray-100 dark:divide-gray-700/60">
                    <div class="flex items-center justify-between px-5 py-3">
                        <dt class="text-xs text-gray-500 dark:text-gray-400">Host</dt>
                        <dd class="text-sm font-mono text-gray-800 dark:text-gray-200">{{ $databaseServer->host ?: '—' }}</dd>
                    </div>
                    <div class="flex items-center justify-between px-5 py-3">
                        <dt class="text-xs text-gray-500 dark:text-gray-400">Puerto</dt>
                        <dd class="text-sm font-mono text-gray-800 dark:text-gray-200">{{ $databaseServer->port ?: '—' }}</dd>
                    </div>
                    <div class="flex items-center justify-between px-5 py-3">
                        <dt class="text-xs text-gray-500 dark:text-gray-400">Cadena</dt>
                        <dd class="text-xs font-mono bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-2 py-1 rounded-md">
                            {{ $databaseServer->connection_string }}
                        </dd>
                    </div>
                    <div class="flex items-center justify-between px-5 py-3">
                        <dt class="text-xs text-gray-500 dark:text-gray-400">Usuario admin</dt>
                        <dd class="text-sm font-mono text-gray-800 dark:text-gray-200">{{ $databaseServer->admin_user ?: '—' }}</dd>
                    </div>
                    <div class="flex items-center justify-between px-5 py-3">
                        <dt class="text-xs text-gray-500 dark:text-gray-400">Contraseña</dt>
                        <dd class="flex items-center gap-2">
                            @if($databaseServer->admin_password)
                            <span id="pass-mask" class="text-sm font-mono text-gray-800 dark:text-gray-200 tracking-widest">••••••••</span>
                            <span id="pass-plain" class="hidden text-sm font-mono text-gray-800 dark:text-gray-200">{{ $databaseServer->admin_password }}</span>
                            <button type="button" onclick="togglePassword()"
                                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
                                    title="Mostrar/ocultar">
                                <svg id="eye-icon" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                            @else
                            <span class="text-sm text-gray-400 dark:text-gray-500">No registrada</span>
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>

            {{-- Servidor físico --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="px-5 py-3.5 border-b border-gray-100 dark:border-gray-700 flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/>
                    </svg>
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Servidor físico</h3>
                </div>
                <div class="px-5 py-4">
                    <a href="{{ route('admin.servers.show', $server) }}"
                       class="flex items-center gap-3 group">
                        <div class="w-9 h-9 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 group-hover:text-violet-600 dark:group-hover:text-violet-400 transition-colors truncate">
                                {{ $server->name }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 font-mono truncate">{{ $server->ip_address }}</p>
                        </div>
                        <svg class="w-4 h-4 text-gray-300 dark:text-gray-600 group-hover:text-violet-400 flex-shrink-0 ml-auto transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            </div>

            {{-- Notas --}}
            @if($databaseServer->notes)
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="px-5 py-3.5 border-b border-gray-100 dark:border-gray-700 flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Notas</h3>
                </div>
                <p class="px-5 py-4 text-sm text-gray-600 dark:text-gray-400 whitespace-pre-wrap leading-relaxed">{{ $databaseServer->notes }}</p>
            </div>
            @endif
        </div>

        {{-- ── Columna derecha: bases de datos ── --}}
        <div class="lg:col-span-2 space-y-5">
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="px-5 py-3.5 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-violet-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582 4-8 4"/>
                        </svg>
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Bases de Datos</h3>
                        @if($databaseServer->databases->count())
                        <span class="text-xs text-gray-400 dark:text-gray-500">({{ $databaseServer->databases->count() }})</span>
                        @endif
                    </div>
                </div>

                @if($databaseServer->databases->isEmpty())
                <div class="flex flex-col items-center justify-center py-12 text-center">
                    <div class="w-12 h-12 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mb-3">
                        <svg class="w-6 h-6 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582 4-8 4"/>
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Sin bases de datos registradas</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Las BDs se registran desde la ficha de cada sistema</p>
                </div>
                @else
                <div class="divide-y divide-gray-100 dark:divide-gray-700/60">
                    @foreach($databaseServer->databases as $db)
                    @php
                        $envVal  = $db->environment instanceof \App\Enums\Environment ? $db->environment->value : (string) $db->environment;
                        $envCss  = $envColors[$envVal] ?? 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300';
                        $envLabel = match($envVal) {
                            'produccion'  => 'Producción',
                            'testing'     => 'Testing',
                            'development' => 'Desarrollo',
                            'staging'     => 'Staging',
                            default       => ucfirst($envVal),
                        };
                    @endphp
                    <div class="px-5 py-4 hover:bg-gray-50/60 dark:hover:bg-gray-700/30 transition-colors">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex items-start gap-3 min-w-0">
                                <div class="w-8 h-8 rounded-lg {{ $ec['bg'] }} flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-4 h-4 {{ $ec['text'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582 4-8 4"/>
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold font-mono text-gray-800 dark:text-gray-200 truncate">
                                        {{ $db->db_name }}
                                    </p>
                                    <div class="flex items-center gap-2 mt-1 flex-wrap">
                                        @if($db->schema_name)
                                        <span class="text-xs font-mono text-gray-500 dark:text-gray-400">
                                            schema: {{ $db->schema_name }}
                                        </span>
                                        @endif
                                        @if($db->db_user)
                                        <span class="text-xs font-mono text-gray-500 dark:text-gray-400 flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                            {{ $db->db_user }}
                                        </span>
                                        @endif
                                        @if($envVal)
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold uppercase tracking-wide {{ $envCss }}">
                                            {{ $envLabel }}
                                        </span>
                                        @endif
                                    </div>
                                    @if($db->notes)
                                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1 italic">{{ $db->notes }}</p>
                                    @endif
                                </div>
                            </div>

                            {{-- Sistema vinculado --}}
                            @if($db->system)
                            <div class="flex-shrink-0 text-right">
                                <span class="text-[10px] text-gray-400 dark:text-gray-500 uppercase tracking-wider">Sistema</span>
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mt-0.5">
                                    {{ $db->system->acronym ?: $db->system->name }}
                                </p>
                                @if($db->system->acronym)
                                <p class="text-xs text-gray-400 dark:text-gray-500 truncate max-w-[120px]">{{ $db->system->name }}</p>
                                @endif
                            </div>
                            @else
                            <div class="flex-shrink-0">
                                <span class="text-xs text-gray-300 dark:text-gray-600 italic">Sin sistema</span>
                            </div>
                            @endif
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
     MODAL: Editar Motor
════════════════════════════════════════════════ --}}
<div id="modal-edit-db"
     class="hidden fixed inset-0 z-50 items-center justify-center p-4"
     role="dialog" aria-modal="true">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeModal('modal-edit-db')"></div>
    <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Editar Motor de BD</h3>
            <button onclick="closeModal('modal-edit-db')"
                    class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400
                           hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form action="{{ route('admin.servers.database-servers.update', [$server, $databaseServer]) }}"
              method="POST">
            @csrf @method('PUT')

            <div class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Alias / Nombre</label>
                        <input type="text" name="name" value="{{ old('name', $databaseServer->name) }}"
                               placeholder="PostgreSQL Producción"
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                      dark:text-white focus:ring-violet-500 focus:border-violet-500 sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Motor <span class="text-red-500">*</span></label>
                        <select name="engine" required
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                       dark:text-white focus:ring-violet-500 focus:border-violet-500 sm:text-sm">
                            @foreach(['postgresql','mysql','mariadb','oracle','sqlserver','sqlite','mongodb','other'] as $eng)
                            <option value="{{ $eng }}" @selected(old('engine', $databaseServer->engine) === $eng)>
                                {{ ucfirst($eng) }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Versión</label>
                        <input type="text" name="version" value="{{ old('version', $databaseServer->version) }}"
                               placeholder="16.2"
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                      dark:text-white focus:ring-violet-500 focus:border-violet-500 sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Host</label>
                        <input type="text" name="host" value="{{ old('host', $databaseServer->host) }}"
                               placeholder="192.168.1.10"
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                      dark:text-white focus:ring-violet-500 focus:border-violet-500 sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Puerto</label>
                        <input type="number" name="port" value="{{ old('port', $databaseServer->port) }}"
                               placeholder="5432" min="1" max="65535"
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                      dark:text-white focus:ring-violet-500 focus:border-violet-500 sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Usuario admin</label>
                        <input type="text" name="admin_user" value="{{ old('admin_user', $databaseServer->admin_user) }}"
                               placeholder="postgres"
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                      dark:text-white focus:ring-violet-500 focus:border-violet-500 sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Contraseña</label>
                        <input type="password" name="admin_password"
                               placeholder="Dejar vacío para no cambiar"
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                      dark:text-white focus:ring-violet-500 focus:border-violet-500 sm:text-sm">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Notas</label>
                        <textarea name="notes" rows="3"
                                  class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                         dark:text-white focus:ring-violet-500 focus:border-violet-500 sm:text-sm resize-none">{{ old('notes', $databaseServer->notes) }}</textarea>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                <button type="button" onclick="closeModal('modal-edit-db')"
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700
                               border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                    Cancelar
                </button>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2 text-sm font-medium text-white
                               bg-violet-600 rounded-lg hover:bg-violet-700 transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Guardar cambios
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
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
function openEditModal() {
    openModal('modal-edit-db');
}
function togglePassword() {
    const mask  = document.getElementById('pass-mask');
    const plain = document.getElementById('pass-plain');
    const icon  = document.getElementById('eye-icon');
    const show  = mask.classList.toggle('hidden');
    plain.classList.toggle('hidden', !show);
    icon.innerHTML = show
        ? `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>`
        : `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>`;
}
</script>
@endpush
