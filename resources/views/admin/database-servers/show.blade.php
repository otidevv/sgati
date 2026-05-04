@extends('layouts.app')

@section('title', $databaseServer->engine_label . ' — ' . $server->name)

@section('content')
    @php
        $levelColors = [
            'principal' => 'bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300',
            'soporte' => 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300',
            'supervision' => 'bg-violet-100 dark:bg-violet-900/40 text-violet-700 dark:text-violet-300',
            'operador' => 'bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300',
        ];
        $levelLabels = [
            'principal' => 'Principal',
            'soporte' => 'Soporte',
            'supervision' => 'Supervisión',
            'operador' => 'Operador',
        ];
        $leftBorder = [
            'principal' => 'border-blue-400 dark:border-blue-500',
            'soporte' => 'border-slate-300 dark:border-slate-500',
            'supervision' => 'border-violet-400 dark:border-violet-500',
            'operador' => 'border-amber-400 dark:border-amber-500',
        ];
        $avatarColors = [
            'principal' => 'bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300',
            'soporte' => 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300',
            'supervision' => 'bg-violet-100 dark:bg-violet-900/50 text-violet-700 dark:text-violet-300',
            'operador' => 'bg-amber-100 dark:bg-amber-900/50 text-amber-700 dark:text-amber-300',
        ];
        $docLabels = [
            'resolucion_directoral' => 'R.D.',
            'resolucion_jefatural' => 'R.J.',
            'memorando' => 'Memo.',
            'oficio' => 'Oficio',
            'contrato' => 'Contrato',
            'acta' => 'Acta',
            'otro' => 'Doc.',
        ];
        $engineColors = [
            'postgresql' => [
                'bg' => 'bg-blue-100 dark:bg-blue-900/40',
                'text' => 'text-blue-700 dark:text-blue-300',
                'dot' => 'bg-blue-500',
            ],
            'mysql' => [
                'bg' => 'bg-orange-100 dark:bg-orange-900/40',
                'text' => 'text-orange-700 dark:text-orange-300',
                'dot' => 'bg-orange-500',
            ],
            'mariadb' => [
                'bg' => 'bg-amber-100 dark:bg-amber-900/40',
                'text' => 'text-amber-700 dark:text-amber-300',
                'dot' => 'bg-amber-500',
            ],
            'oracle' => [
                'bg' => 'bg-red-100 dark:bg-red-900/40',
                'text' => 'text-red-700 dark:text-red-300',
                'dot' => 'bg-red-500',
            ],
            'sqlserver' => [
                'bg' => 'bg-sky-100 dark:bg-sky-900/40',
                'text' => 'text-sky-700 dark:text-sky-300',
                'dot' => 'bg-sky-500',
            ],
            'sqlite' => [
                'bg' => 'bg-teal-100 dark:bg-teal-900/40',
                'text' => 'text-teal-700 dark:text-teal-300',
                'dot' => 'bg-teal-500',
            ],
            'mongodb' => [
                'bg' => 'bg-green-100 dark:bg-green-900/40',
                'text' => 'text-green-700 dark:text-green-300',
                'dot' => 'bg-green-500',
            ],
            'other' => [
                'bg' => 'bg-gray-100 dark:bg-gray-700',
                'text' => 'text-gray-600 dark:text-gray-300',
                'dot' => 'bg-gray-400',
            ],
        ];
        $ec = $engineColors[$databaseServer->engine] ?? $engineColors['other'];

        $envColors = [
            'produccion' => 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400',
            'testing' => 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400',
            'development' => 'bg-sky-100 dark:bg-sky-900/30 text-sky-700 dark:text-sky-400',
            'staging' => 'bg-violet-100 dark:bg-violet-900/30 text-violet-700 dark:text-violet-400',
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div>
                    {{-- Breadcrumb --}}
                    <p class="text-xs text-gray-400 dark:text-gray-500 mb-1 flex items-center gap-1">
                        <a href="{{ route('admin.servers.index') }}"
                            class="hover:text-gray-600 dark:hover:text-gray-300 transition-colors">Servidores</a>
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                        <a href="{{ route('admin.servers.show', $server) }}"
                            class="hover:text-gray-600 dark:hover:text-gray-300 transition-colors">{{ $server->name }}</a>
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                        Motores de BD
                    </p>
                    <div class="flex items-center gap-3">
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ $databaseServer->name ?: $databaseServer->engine_label }}
                        </h1>
                        @if ($databaseServer->is_active)
                            <span
                                class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium
                                 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>Activo
                            </span>
                        @else
                            <span
                                class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>
                    Editar
                </button>
            </div>
        </div>

        {{-- ── Stats rápidas ── --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div
                class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-4 text-center">
                <p class="text-2xl font-bold text-violet-600 dark:text-violet-400">{{ $databaseServer->databases->count() }}
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Bases de datos</p>
            </div>
            <div
                class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-4 text-center">
                <p class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">
                    {{ $databaseServer->databases->pluck('system_id')->filter()->unique()->count() }}
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Sistemas vinculados</p>
            </div>
            <div
                class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-4 text-center">
                <p class="text-xl font-bold text-gray-700 dark:text-gray-300 font-mono">
                    {{ $databaseServer->port ?? '—' }}
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Puerto</p>
            </div>
            <div
                class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-4 text-center">
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
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-gray-100 dark:border-gray-700 flex items-center gap-2">
                        <svg class="w-4 h-4 text-violet-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582 4-8 4m16 5c0 2.21-3.582 4-8 4S4 14.21 4 12" />
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
                                <span
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold {{ $ec['bg'] }} {{ $ec['text'] }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $ec['dot'] }}"></span>
                                    {{ ucfirst($databaseServer->engine) }}
                                </span>
                            </dd>
                        </div>
                        <div class="flex items-center justify-between px-5 py-3">
                            <dt class="text-xs text-gray-500 dark:text-gray-400">Versión</dt>
                            <dd class="text-sm font-mono text-gray-800 dark:text-gray-200">
                                {{ $databaseServer->version ?: '—' }}</dd>
                        </div>
                        <div class="flex items-center justify-between px-5 py-3">
                            <dt class="text-xs text-gray-500 dark:text-gray-400">Estado</dt>
                            <dd>
                                @if ($databaseServer->is_active)
                                    <span
                                        class="inline-flex items-center gap-1 text-xs text-emerald-600 dark:text-emerald-400 font-medium">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Activo
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400 font-medium">
                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>Inactivo
                                    </span>
                                @endif
                            </dd>
                        </div>
                    </dl>
                </div>

                {{-- Conexión --}}
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-gray-100 dark:border-gray-700 flex items-center gap-2">
                        <svg class="w-4 h-4 text-sky-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.142 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0" />
                        </svg>
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Conexión</h3>
                    </div>
                    <dl class="divide-y divide-gray-100 dark:divide-gray-700/60">
                        <div class="flex items-center justify-between px-5 py-3">
                            <dt class="text-xs text-gray-500 dark:text-gray-400">Host</dt>
                            <dd class="text-sm font-mono text-gray-800 dark:text-gray-200">
                                {{ $databaseServer->host ?: '—' }}</dd>
                        </div>
                        <div class="flex items-center justify-between px-5 py-3">
                            <dt class="text-xs text-gray-500 dark:text-gray-400">Puerto</dt>
                            <dd class="text-sm font-mono text-gray-800 dark:text-gray-200">
                                {{ $databaseServer->port ?: '—' }}</dd>
                        </div>
                        <div class="flex items-center justify-between px-5 py-3">
                            <dt class="text-xs text-gray-500 dark:text-gray-400">Cadena</dt>
                            <dd
                                class="text-xs font-mono bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-2 py-1 rounded-md">
                                {{ $databaseServer->connection_string }}
                            </dd>
                        </div>
                        <div class="flex items-center justify-between px-5 py-3">
                            <dt class="text-xs text-gray-500 dark:text-gray-400">Autenticación</dt>
                            <dd>
                                @php
                                    $authMeta = [
                                        'credentials' => ['Usuario y contraseña',          'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300'],
                                        'windows'     => ['Windows (SSPI / AD)',            'bg-purple-50 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300'],
                                        'kerberos'    => ['Kerberos / LDAP',                'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300'],
                                        'iam'         => ['IAM / Cloud',                    'bg-orange-50 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300'],
                                        'trusted'     => ['Confianza local',                'bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-300'],
                                    ];
                                    $at = $databaseServer->auth_type ?? 'credentials';
                                    [$atLabel, $atClass] = $authMeta[$at] ?? ['Desconocido', 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300'];
                                @endphp
                                <span class="text-xs font-medium px-2 py-0.5 rounded-full {{ $atClass }}">{{ $atLabel }}</span>
                            </dd>
                        </div>
                        <div class="flex items-center justify-between px-5 py-3">
                            <dt class="text-xs text-gray-500 dark:text-gray-400">Usuario admin</dt>
                            <dd class="text-sm font-mono text-gray-800 dark:text-gray-200">
                                {{ $databaseServer->admin_user ?: '—' }}</dd>
                        </div>
                        <div class="flex items-center justify-between px-5 py-3">
                            <dt class="text-xs text-gray-500 dark:text-gray-400">Contraseña</dt>
                            <dd class="flex items-center gap-2">
                                @if ($databaseServer->admin_password)
                                    <span id="pass-mask"
                                        class="text-sm font-mono text-gray-800 dark:text-gray-200 tracking-widest">••••••••</span>
                                    <span id="pass-plain"
                                        class="hidden text-sm font-mono text-gray-800 dark:text-gray-200">{{ $databaseServer->admin_password }}</span>
                                    <button type="button" onclick="togglePassword()"
                                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
                                        title="Mostrar/ocultar">
                                        <svg id="eye-icon" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
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
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-gray-100 dark:border-gray-700 flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01" />
                        </svg>
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Servidor físico</h3>
                    </div>
                    <div class="px-5 py-4">
                        <a href="{{ route('admin.servers.show', $server) }}" class="flex items-center gap-3 group">
                            <div
                                class="w-9 h-9 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01" />
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <p
                                    class="text-sm font-semibold text-gray-800 dark:text-gray-200 group-hover:text-violet-600 dark:group-hover:text-violet-400 transition-colors truncate">
                                    {{ $server->name }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 font-mono truncate">
                                    {{ $server->ip_address }}</p>
                            </div>
                            <svg class="w-4 h-4 text-gray-300 dark:text-gray-600 group-hover:text-violet-400 flex-shrink-0 ml-auto transition-colors"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>

                {{-- Notas --}}
                @if ($databaseServer->notes)
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                        <div class="px-5 py-3.5 border-b border-gray-100 dark:border-gray-700 flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Notas</h3>
                        </div>
                        <p class="px-5 py-4 text-sm text-gray-600 dark:text-gray-400 whitespace-pre-wrap leading-relaxed">
                            {{ $databaseServer->notes }}</p>
                    </div>
                @endif
            </div>

            {{-- ── Columna derecha: bases de datos ── --}}
            <div class="lg:col-span-2 space-y-5">
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                    <div
                        class="px-5 py-3.5 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-violet-500" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582 4-8 4" />
                            </svg>
                            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Bases de Datos</h3>
                            @if ($databaseServer->databases->count())
                                <span
                                    class="text-xs text-gray-400 dark:text-gray-500">({{ $databaseServer->databases->count() }})</span>
                            @endif
                        </div>
                    </div>

                    @if ($databaseServer->databases->isEmpty())
                        <div class="flex flex-col items-center justify-center py-12 text-center">
                            <div
                                class="w-12 h-12 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mb-3">
                                <svg class="w-6 h-6 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582 4-8 4" />
                                </svg>
                            </div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Sin bases de datos registradas
                            </p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Las BDs se registran desde la ficha
                                de cada sistema</p>
                        </div>
                    @else
                        <div class="divide-y divide-gray-100 dark:divide-gray-700/60">
                            @foreach ($databaseServer->databases as $db)
                                @php
                                    $envVal =
                                        $db->environment instanceof \App\Enums\Environment
                                            ? $db->environment->value
                                            : (string) $db->environment;
                                    $envCss =
                                        $envColors[$envVal] ??
                                        'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300';
                                    $envLabel = match ($envVal) {
                                        'produccion' => 'Producción',
                                        'testing' => 'Testing',
                                        'development' => 'Desarrollo',
                                        'staging' => 'Staging',
                                        default => ucfirst($envVal),
                                    };
                                @endphp
                                <div class="px-5 py-4 hover:bg-gray-50/60 dark:hover:bg-gray-700/30 transition-colors">
                                    <div class="flex items-start justify-between gap-4">
                                        <div class="flex items-start gap-3 min-w-0">
                                            <div
                                                class="w-8 h-8 rounded-lg {{ $ec['bg'] }} flex items-center justify-center flex-shrink-0 mt-0.5">
                                                <svg class="w-4 h-4 {{ $ec['text'] }}" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582 4-8 4" />
                                                </svg>
                                            </div>
                                            <div class="min-w-0">
                                                <p
                                                    class="text-sm font-semibold font-mono text-gray-800 dark:text-gray-200 truncate">
                                                    {{ $db->db_name }}
                                                </p>
                                                <div class="flex items-center gap-2 mt-1 flex-wrap">
                                                    @if ($db->schema_name)
                                                        <span class="text-xs font-mono text-gray-500 dark:text-gray-400">
                                                            schema: {{ $db->schema_name }}
                                                        </span>
                                                    @endif
                                                    @if ($db->db_user)
                                                        <span
                                                            class="text-xs font-mono text-gray-500 dark:text-gray-400 flex items-center gap-1">
                                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24"
                                                                stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                            </svg>
                                                            {{ $db->db_user }}
                                                        </span>
                                                    @endif
                                                    @if ($envVal)
                                                        <span
                                                            class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold uppercase tracking-wide {{ $envCss }}">
                                                            {{ $envLabel }}
                                                        </span>
                                                    @endif
                                                </div>
                                                @if ($db->notes)
                                                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1 italic">
                                                        {{ $db->notes }}</p>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- Sistema vinculado --}}
                                        @if ($db->system)
                                            <div class="flex-shrink-0 text-right">
                                                <span
                                                    class="text-[10px] text-gray-400 dark:text-gray-500 uppercase tracking-wider">Sistema</span>
                                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mt-0.5">
                                                    {{ $db->system->acronym ?: $db->system->name }}
                                                </p>
                                                @if ($db->system->acronym)
                                                    <p
                                                        class="text-xs text-gray-400 dark:text-gray-500 truncate max-w-[120px]">
                                                        {{ $db->system->name }}</p>
                                                @endif
                                            </div>
                                        @else
                                            <div class="flex-shrink-0">
                                                <span class="text-xs text-gray-300 dark:text-gray-600 italic">Sin
                                                    sistema</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- ── Responsables ── --}}
                @php
                    $activeResponsibles = $databaseServer->responsibles->where('is_active', true);
                    $historicalResponsibles = $databaseServer->responsibles
                        ->where('is_active', false)
                        ->sortByDesc('unassigned_at');
                @endphp
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                    <div
                        class="px-5 py-3.5 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Responsables</h3>
                            @if ($databaseServer->responsibles->count())
                                <span class="text-xs text-gray-400 dark:text-gray-500">({{ $activeResponsibles->count() }}
                                    activos)</span>
                            @endif
                        </div>
                        <button onclick="openModal('modal-db-responsible')"
                            class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium
                           text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/30
                           rounded-lg hover:bg-emerald-100 dark:hover:bg-emerald-900/50 transition-colors">
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            Agregar
                        </button>
                    </div>

                    {{-- Sin ningún responsable --}}
                    @if ($activeResponsibles->isEmpty() && $historicalResponsibles->isEmpty())
                        <div class="flex flex-col items-center justify-center py-10 text-center">
                            <div
                                class="w-12 h-12 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mb-3">
                                <svg class="w-6 h-6 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Sin responsables asignados</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Usa el botón "Agregar" para asignar
                                uno</p>
                        </div>
                    @else
                        {{-- Activos --}}
                        @if ($activeResponsibles->isEmpty())
                            <div
                                class="px-5 py-4 flex items-center gap-2 text-sm text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                                </svg>
                                Sin responsables activos actualmente
                            </div>
                        @else
                            <div class="divide-y divide-gray-100 dark:divide-gray-700/60">
                                @foreach ($activeResponsibles as $resp)
                                    @php
                                        $initials = strtoupper(
                                            substr($resp->persona->apellido_paterno, 0, 1) .
                                                substr($resp->persona->apellido_materno ?? '', 0, 1),
                                        );
                                        $borderClass = $leftBorder[$resp->level] ?? 'border-gray-300';
                                        $avatarClass = $avatarColors[$resp->level] ?? 'bg-gray-100 text-gray-600';
                                    @endphp
                                    <div
                                        class="px-4 py-3 border-l-[3px] {{ $borderClass }} hover:bg-gray-50/60 dark:hover:bg-gray-700/30 transition-colors group/row">
                                        <div class="flex items-start gap-3">
                                            <div
                                                class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0 text-xs font-bold mt-0.5 {{ $avatarClass }}">
                                                {{ $initials }}
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-1.5 min-w-0">
                                                    <span
                                                        class="text-sm font-semibold text-gray-800 dark:text-gray-100 leading-snug truncate flex-1 min-w-0">
                                                        {{ $resp->persona->apellido_paterno }}
                                                        {{ $resp->persona->apellido_materno }},
                                                        <span
                                                            class="font-normal text-gray-600 dark:text-gray-300">{{ $resp->persona->nombres }}</span>
                                                    </span>
                                                    <div
                                                        class="flex items-center gap-0.5 flex-shrink-0 opacity-0 group-hover/row:opacity-100 transition-opacity">
                                                        <button type="button"
                                                                onclick="downloadResponsibleActa('{{ route('admin.servers.database-servers.responsibles.pdf-data', [$server, $databaseServer, $resp]) }}', this)"
                                                                title="Generar acta de asignación PDF"
                                                                class="w-6 h-6 flex items-center justify-center rounded-md text-gray-400 dark:text-gray-500
                                                                       hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors disabled:opacity-50">
                                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                            </svg>
                                                        </button>
                                                        <button onclick="openDbDocUpload({{ $resp->id }})"
                                                            title="Adjuntar documento"
                                                            class="w-6 h-6 flex items-center justify-center rounded-md text-gray-400 dark:text-gray-500
                                                                   hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-colors">
                                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                                            </svg>
                                                        </button>
                                                        <button onclick="editDbResponsible({{ $resp->id }}, {{ $resp->toJson() }})"
                                                            title="Editar"
                                                            class="w-6 h-6 flex items-center justify-center rounded-md text-gray-400 dark:text-gray-500
                                                                   hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors">
                                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                                            </svg>
                                                        </button>
                                                        <button onclick="openDbDeactivate({{ $resp->id }}, '{{ addslashes($resp->persona->apellido_paterno . ' ' . $resp->persona->nombres) }}')"
                                                            title="Dar de baja"
                                                            class="w-6 h-6 flex items-center justify-center rounded-md text-gray-400 dark:text-gray-500
                                                                   hover:text-orange-600 dark:hover:text-orange-400 hover:bg-orange-50 dark:hover:bg-orange-900/20 transition-colors">
                                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="flex items-center gap-2 mt-1 flex-wrap">
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold uppercase tracking-wide {{ $levelColors[$resp->level] ?? '' }}">
                                                        {{ $levelLabels[$resp->level] ?? $resp->level }}
                                                    </span>
                                                    <span class="inline-flex items-center gap-1 text-[10px] text-emerald-600 dark:text-emerald-400">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Activo
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
                                                        $docExt    = strtolower(pathinfo($doc->original_name, PATHINFO_EXTENSION));
                                                        $docMeta   = collect([$docLabels[$doc->document_type] ?? null, $doc->document_number, $doc->document_date?->format('d/m/Y')])->filter()->implode(' · ');
                                                        $chipLabel = $doc->description ?: $doc->original_name;
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
                                                                    previewUrl:  "{{ route('admin.servers.database-servers.responsibles.documents.preview', [$server, $databaseServer, $resp, $doc]) }}",
                                                                    downloadUrl: "{{ route('admin.servers.database-servers.responsibles.documents.download', [$server, $databaseServer, $resp, $doc]) }}",
                                                                    ext:         {{ json_encode($docExt) }}
                                                                })'
                                                                title="{{ $docMeta ? $docMeta . ' — ' . $doc->original_name : $doc->original_name }}"
                                                                class="truncate max-w-[160px] hover:underline cursor-pointer">
                                                            {{ $chipLabel }}
                                                        </button>
                                                        <form action="{{ route('admin.servers.database-servers.responsibles.documents.destroy', [$server, $databaseServer, $resp, $doc]) }}"
                                                              method="POST" class="inline"
                                                              onsubmit="sgDeleteForm(this,'¿Eliminar este documento?');return false">
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

                        {{-- Historial --}}
                        @if ($historicalResponsibles->isNotEmpty())
                            <div class="border-t border-gray-100 dark:border-gray-700/60">
                                <button type="button" onclick="toggleDbHistory()"
                                    class="w-full flex items-center justify-between px-5 py-2.5 text-xs font-semibold
                           text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/30
                           uppercase tracking-wider transition-colors">
                                    <span class="flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Historial de responsables
                                        <span
                                            class="px-1.5 py-0.5 rounded-full bg-gray-200 dark:bg-gray-600 text-gray-600 dark:text-gray-300 normal-case tracking-normal font-semibold">
                                            {{ $historicalResponsibles->count() }}
                                        </span>
                                    </span>
                                    <svg id="db-history-chevron" class="w-3.5 h-3.5 transition-transform duration-200"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                <div id="db-resp-history"
                                    class="hidden divide-y divide-gray-100 dark:divide-gray-700/60 bg-gray-50/50 dark:bg-gray-800/30">
                                    @foreach ($historicalResponsibles as $resp)
                                        @php
                                            $initials = strtoupper(
                                                substr($resp->persona->apellido_paterno, 0, 1) .
                                                    substr($resp->persona->apellido_materno ?? '', 0, 1),
                                            );
                                            $dias = $resp->assigned_at->diffInDays($resp->unassigned_at ?? now());
                                        @endphp
                                        <div
                                            class="px-4 py-3 border-l-[3px] border-gray-300 dark:border-gray-600 opacity-75 hover:opacity-100 hover:bg-gray-100/60 dark:hover:bg-gray-700/30 transition-all group/hist">
                                            <div class="flex items-start gap-3">
                                                <div
                                                    class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0 text-xs font-bold mt-0.5 bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400">
                                                    {{ $initials }}
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-center gap-1.5 min-w-0">
                                                        <span
                                                            class="text-sm font-semibold text-gray-600 dark:text-gray-400 leading-snug truncate flex-1 min-w-0">
                                                            {{ $resp->persona->apellido_paterno }}
                                                            {{ $resp->persona->apellido_materno }},
                                                            <span class="font-normal">{{ $resp->persona->nombres }}</span>
                                                        </span>
                                                        <div
                                                            class="flex items-center gap-0.5 flex-shrink-0 opacity-0 group-hover/hist:opacity-100 transition-opacity">
                                                            <button
                                                                onclick="openDbReactivate({{ $resp->id }}, '{{ addslashes($resp->persona->apellido_paterno . ' ' . $resp->persona->nombres) }}')"
                                                                title="Reactivar"
                                                                class="w-6 h-6 flex items-center justify-center rounded-md text-gray-400 dark:text-gray-500
                                                   hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition-colors">
                                                                <svg class="w-3.5 h-3.5" fill="none"
                                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                                </svg>
                                                            </button>
                                                            <form
                                                                action="{{ route('admin.servers.database-servers.responsibles.destroy', [$server, $databaseServer, $resp]) }}"
                                                                method="POST" id="del-db-hist-{{ $resp->id }}"
                                                                class="inline">
                                                                @csrf @method('DELETE')
                                                                <button type="button" title="Eliminar del historial"
                                                                    onclick="dtConfirmDelete('del-db-hist-{{ $resp->id }}', '{{ addslashes($resp->persona->apellido_paterno . ' ' . $resp->persona->nombres) }}')"
                                                                    class="w-6 h-6 flex items-center justify-center rounded-md text-gray-400 dark:text-gray-500
                                                       hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                                                    <svg class="w-3.5 h-3.5" fill="none"
                                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                                        <path stroke-linecap="round"
                                                                            stroke-linejoin="round" stroke-width="2"
                                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                                    </svg>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                    <div class="flex items-center gap-2 mt-1 flex-wrap">
                                                        <span
                                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold uppercase tracking-wide {{ $levelColors[$resp->level] ?? '' }} opacity-60">
                                                            {{ $levelLabels[$resp->level] ?? $resp->level }}
                                                        </span>
                                                        <span
                                                            class="inline-flex items-center gap-1 text-[11px] text-gray-400 dark:text-gray-500 font-medium">
                                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24"
                                                                stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                            </svg>
                                                            {{ $resp->assigned_at->format('d/m/Y') }}
                                                            <svg class="w-3 h-3 text-gray-300 dark:text-gray-600"
                                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                                            </svg>
                                                            {{ $resp->unassigned_at?->format('d/m/Y') ?? 'N/A' }}
                                                            <span
                                                                class="text-gray-300 dark:text-gray-600">({{ $dias }}
                                                                {{ $dias === 1 ? 'día' : 'días' }})</span>
                                                        </span>
                                                        @if ($resp->document_notes)
                                                            <span
                                                                class="text-[11px] text-gray-400 dark:text-gray-500 italic">{{ $resp->document_notes }}</span>
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
     MODAL: Editar Motor
════════════════════════════════════════════════ --}}
    <div id="modal-edit-db" class="hidden fixed inset-0 z-50 items-center justify-center p-4" role="dialog"
        aria-modal="true">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeModal('modal-edit-db')"></div>
        <div
            class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Editar Motor de BD</h3>
                <button onclick="closeModal('modal-edit-db')"
                    class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400
                           hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form action="{{ route('admin.servers.database-servers.update', [$server, $databaseServer]) }}"
                method="POST">
                @csrf @method('PUT')

                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Alias /
                                Nombre</label>
                            <input type="text" name="name" value="{{ old('name', $databaseServer->name) }}"
                                placeholder="PostgreSQL Producción"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                      dark:text-white focus:ring-violet-500 focus:border-violet-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Motor <span
                                    class="text-red-500">*</span></label>
                            <select name="engine" required
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                       dark:text-white focus:ring-violet-500 focus:border-violet-500 sm:text-sm">
                                @foreach (['postgresql', 'mysql', 'mariadb', 'oracle', 'sqlserver', 'sqlite', 'mongodb', 'other'] as $eng)
                                    <option value="{{ $eng }}" @selected(old('engine', $databaseServer->engine) === $eng)>
                                        {{ ucfirst($eng) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Versión</label>
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
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                Tipo de autenticación <span class="text-red-500">*</span>
                            </label>
                            <select name="auth_type" id="edit-auth-type"
                                    onchange="editAuthTypeChanged(this.value)"
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                           dark:text-white focus:ring-violet-500 focus:border-violet-500 sm:text-sm">
                                @foreach([
                                    'credentials' => 'Usuario y contraseña',
                                    'windows'     => 'Autenticación de Windows (SSPI / Active Directory)',
                                    'kerberos'    => 'Kerberos / LDAP',
                                    'iam'         => 'IAM / Cloud (AWS RDS, Azure AD, GCP)',
                                    'trusted'     => 'Confianza local (sin credenciales)',
                                ] as $v => $l)
                                    <option value="{{ $v }}" @selected(old('auth_type', $databaseServer->auth_type ?? 'credentials') === $v)>{{ $l }}</option>
                                @endforeach
                            </select>
                            <p id="edit-auth-hint" class="mt-1 text-xs text-gray-400 dark:text-gray-500"></p>
                        </div>
                        <div id="edit-user-wrap">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Usuario
                                admin</label>
                            <input type="text" name="admin_user"
                                value="{{ old('admin_user', $databaseServer->admin_user) }}" placeholder="postgres"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                      dark:text-white focus:ring-violet-500 focus:border-violet-500 sm:text-sm">
                        </div>
                        <div id="edit-pass-wrap">
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Contraseña</label>
                            <input type="password" name="admin_password" placeholder="Dejar vacío para no cambiar"
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

                <div
                    class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                    <button type="button" onclick="closeModal('modal-edit-db')"
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700
                               border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2 text-sm font-medium text-white
                               bg-violet-600 rounded-lg hover:bg-violet-700 transition-colors shadow-sm">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Guardar cambios
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════
     MODAL: Asignar / Editar Responsable (BD)
════════════════════════════════════════════════ --}}
    <div id="modal-db-responsible" class="hidden fixed inset-0 z-50 items-center justify-center p-4" role="dialog"
        aria-modal="true">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeModal('modal-db-responsible')"></div>
        <div
            class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 id="db-resp-modal-title" class="text-base font-semibold text-gray-900 dark:text-white">Asignar
                    Responsable</h3>
                <button onclick="closeModal('modal-db-responsible')"
                    class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400
                           hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form id="db-resp-form"
                action="{{ route('admin.servers.database-servers.responsibles.store', [$server, $databaseServer]) }}"
                method="POST">
                @csrf
                <span id="db-resp-method"></span>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Persona <span class="text-red-500">*</span>
                        </label>
                        <div class="relative" id="db-resp-search-wrap">
                            <div class="relative">
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                <input type="text" id="db-resp-search-input" autocomplete="off"
                                       placeholder="Buscar por DNI o apellido/nombre..."
                                       class="w-full pl-9 pr-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600
                                              dark:bg-gray-700 dark:text-white text-sm
                                              focus:ring-emerald-500 focus:border-emerald-500">
                            </div>
                            <input type="hidden" name="persona_id" id="db-resp-persona_id" required>
                            {{-- Dropdown resultados --}}
                            <div id="db-resp-dropdown"
                                 class="hidden absolute z-50 w-full mt-1 bg-white dark:bg-gray-800
                                        border border-gray-200 dark:border-gray-600 rounded-lg shadow-xl
                                        max-h-48 overflow-y-auto text-sm">
                            </div>
                            {{-- Persona seleccionada --}}
                            <div id="db-resp-selected"
                                 class="hidden mt-2 items-center gap-2 px-3 py-2 rounded-lg
                                        bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-700/40">
                                <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                <span id="db-resp-selected-name" class="flex-1 text-sm font-medium text-emerald-700 dark:text-emerald-300 truncate"></span>
                                <button type="button" onclick="clearPersonaSearch('db-resp')"
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
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                Nivel <span class="text-red-500">*</span>
                            </label>
                            <select name="level" id="db-resp-level" required
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                       dark:text-white focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                                <option value="principal">Responsable Principal</option>
                                <option value="soporte">Soporte Técnico</option>
                                <option value="supervision">Supervisión</option>
                                <option value="operador">Operador</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                Fecha de asignación <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="assigned_at" id="db-resp-assigned_at" required
                                value="{{ now()->format('Y-m-d') }}"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                      dark:text-white focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                        </div>
                    </div>
                </div>
                <div
                    class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                    <button type="button" onclick="closeModal('modal-db-responsible')"
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700
                               border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 transition-colors">
                        Cancelar
                    </button>
                    <button id="db-resp-submit-btn" type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2 text-sm font-medium text-white
                               bg-emerald-600 rounded-lg hover:bg-emerald-700 transition-colors shadow-sm">
                        <svg id="db-resp-submit-icon" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <svg id="db-resp-submit-spinner" class="hidden w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/>
                        </svg>
                        <span id="db-resp-submit-label">Asignar</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════
     MODAL: Dar de Baja (BD)
════════════════════════════════════════════════ --}}
    <div id="modal-db-deactivate" class="hidden fixed inset-0 z-50 items-center justify-center p-4" role="dialog"
        aria-modal="true">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeModal('modal-db-deactivate')"></div>
        <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                    </svg>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">Dar de Baja Responsable</h3>
                </div>
                <button onclick="closeModal('modal-db-deactivate')"
                    class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400
                           hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form id="db-deactivate-form" method="POST">
                @csrf
                <input type="hidden" name="_method" value="PATCH">
                <div class="p-6 space-y-4">
                    <div
                        class="flex items-start gap-3 p-3 rounded-lg bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-700/40">
                        <svg class="w-4 h-4 text-orange-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-sm text-orange-700 dark:text-orange-300">
                            <strong id="db-deactivate-name" class="font-semibold"></strong> pasará al historial
                            conservando su período de gestión.
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Fecha de baja <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="unassigned_at" id="db-deactivate-date" required
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                  dark:text-white focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Motivo <span class="text-gray-400 font-normal">(opcional)</span>
                        </label>
                        <textarea name="deactivate_notes" id="db-deactivate-notes" rows="2"
                            placeholder="Ej: Renuncia voluntaria, Fin de contrato..."
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                     dark:text-white focus:ring-orange-500 focus:border-orange-500 sm:text-sm resize-none"></textarea>
                    </div>
                </div>
                <div
                    class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                    <button type="button" onclick="closeModal('modal-db-deactivate')"
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700
                               border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 transition-colors">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2 text-sm font-medium text-white
                               bg-orange-600 rounded-lg hover:bg-orange-700 transition-colors shadow-sm">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                        </svg>
                        Dar de Baja
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════
     MODAL: Reactivar (BD)
════════════════════════════════════════════════ --}}
    <div id="modal-db-reactivate" class="hidden fixed inset-0 z-50 items-center justify-center p-4" role="dialog"
        aria-modal="true">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeModal('modal-db-reactivate')"></div>
        <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-sm">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">Reactivar Responsable</h3>
                </div>
                <button onclick="closeModal('modal-db-reactivate')"
                    class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400
                           hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form id="db-reactivate-form" method="POST">
                @csrf
                <input type="hidden" name="_method" value="PATCH">
                <div class="p-6 space-y-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        <strong id="db-reactivate-name" class="text-gray-800 dark:text-gray-200 font-semibold"></strong>
                        volverá a figurar como responsable activo.
                    </p>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Fecha de reactivación <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="assigned_at" id="db-reactivate-date" required
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                  dark:text-white focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                    </div>
                </div>
                <div
                    class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                    <button type="button" onclick="closeModal('modal-db-reactivate')"
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700
                               border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 transition-colors">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2 text-sm font-medium text-white
                               bg-emerald-600 rounded-lg hover:bg-emerald-700 transition-colors shadow-sm">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Reactivar
                    </button>
                </div>
            </form>
        </div>
    </div>

{{-- ════════════════════════════════════════════════
     MODAL: Adjuntar Documento a Responsable (BD)
════════════════════════════════════════════════ --}}
<div id="modal-db-doc-upload"
     class="hidden fixed inset-0 z-50 items-center justify-center p-4"
     role="dialog" aria-modal="true">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeModal('modal-db-doc-upload')"></div>
    <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                </svg>
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Adjuntar Documento</h3>
            </div>
            <button onclick="closeModal('modal-db-doc-upload')"
                    class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400
                           hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form id="db-doc-upload-form" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="p-6 space-y-4">
                <div x-data="{ dragging: false }"
                     @dragover.prevent="dragging = true"
                     @dragleave.prevent="dragging = false"
                     @drop.prevent="dragging = false; $refs.dbFileInput.files = $event.dataTransfer.files; updateDbFileName($event.dataTransfer.files[0]?.name)"
                     :class="dragging ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20' : 'border-gray-300 dark:border-gray-600 hover:border-indigo-400 dark:hover:border-indigo-500'"
                     class="border-2 border-dashed rounded-xl p-6 text-center transition-colors cursor-pointer"
                     onclick="document.getElementById('db-doc-file-input').click()">
                    <svg class="w-10 h-10 mx-auto text-gray-300 dark:text-gray-600 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    <p id="db-doc-file-label" class="text-sm text-gray-500 dark:text-gray-400">
                        Arrastra el archivo aquí o <span class="text-indigo-600 dark:text-indigo-400 font-medium">haz clic para seleccionar</span>
                    </p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">PDF, Word, Excel, imagen · Máx. 10 MB</p>
                    <input id="db-doc-file-input" x-ref="dbFileInput" type="file" name="file"
                           accept=".pdf,.doc,.docx,.xls,.xlsx,.png,.jpg,.jpeg"
                           class="hidden" required
                           onchange="updateDbFileName(this.files[0]?.name)">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Descripción <span class="text-gray-400 font-normal">(opcional)</span>
                    </label>
                    <input type="text" name="description" id="db-doc-description"
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
                            <select name="document_type" id="db-doc-document_type"
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
                            <input type="text" name="document_number" id="db-doc-document_number"
                                   placeholder="R.D. N°042-2024-OTI"
                                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                          dark:text-white focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Fecha del documento</label>
                            <input type="date" name="document_date" id="db-doc-document_date"
                                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                          dark:text-white focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Observaciones</label>
                            <input type="text" name="document_notes" id="db-doc-document_notes"
                                   placeholder="Notas adicionales..."
                                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                          dark:text-white focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                    </div>
                </div>

            </div>
            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 rounded-b-2xl">
                <button type="button" onclick="closeModal('modal-db-doc-upload')"
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700
                               border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 transition-colors">
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

{{-- Componente de previsualización (reutilizable) --}}
<x-doc-preview-modal />

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
                                hiddenInput.value  = p.id;
                                selName.textContent = `${p.apellido_paterno} ${p.apellido_materno ?? ''}, ${p.nombres} (${p.dni})`;
                                searchInput.value  = '';
                                dropdown.classList.add('hidden');
                                dropdown.innerHTML = '';
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

            // Cerrar dropdown al hacer clic fuera
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

        // Inicializar autocompletado del modal BD
        initPersonaSearch('db-resp');

        // ── Responsables BD ──────────────────────────────────────────────────
        const dbRespStoreUrl =
            "{{ route('admin.servers.database-servers.responsibles.store', [$server, $databaseServer]) }}";
        const dbRespUpdateBase =
            "{{ url('admin/servers/' . $server->id . '/database-servers/' . $databaseServer->id . '/responsibles') }}/";

        // Protección doble envío
        let dbRespSubmitting = false;
        const dbRespBtn     = document.getElementById('db-resp-submit-btn');
        const dbRespIcon    = document.getElementById('db-resp-submit-icon');
        const dbRespSpinner = document.getElementById('db-resp-submit-spinner');
        const dbRespLabel   = document.getElementById('db-resp-submit-label');

        document.getElementById('db-resp-form').addEventListener('submit', function (e) {
            if (dbRespSubmitting) { e.preventDefault(); return; }
            if (!this.checkValidity()) return;
            dbRespSubmitting = true;
            dbRespBtn.classList.add('pointer-events-none', 'opacity-75');
            dbRespIcon.classList.add('hidden');
            dbRespSpinner.classList.remove('hidden');
            dbRespLabel.textContent = 'Guardando…';
        });

        function resetDbRespBtn() {
            dbRespSubmitting = false;
            dbRespBtn.classList.remove('pointer-events-none', 'opacity-75');
            dbRespIcon.classList.remove('hidden');
            dbRespSpinner.classList.add('hidden');
        }

        function editDbResponsible(id, data) {
            document.getElementById('db-resp-modal-title').textContent = 'Editar Responsable';
            dbRespLabel.textContent = 'Guardar';
            resetDbRespBtn();
            document.getElementById('db-resp-form').action = dbRespUpdateBase + id;
            document.getElementById('db-resp-method').innerHTML = '<input type="hidden" name="_method" value="PUT">';
            // Mostrar nombre del responsable en el buscador como seleccionado
            document.getElementById('db-resp-persona_id').value = data.persona_id ?? '';
            const nombre = (data.persona?.apellido_paterno ?? '') + ' ' + (data.persona?.apellido_materno ?? '') +
                           ', ' + (data.persona?.nombres ?? '');
            if (data.persona_id) {
                const selName = document.getElementById('db-resp-selected-name');
                const sel     = document.getElementById('db-resp-selected');
                selName.textContent = nombre.trim();
                sel.classList.remove('hidden');
                sel.classList.add('flex');
            }
            document.getElementById('db-resp-level').value       = data.level       ?? 'soporte';
            document.getElementById('db-resp-assigned_at').value = data.assigned_at ?? '';
            openModal('modal-db-responsible');
        }

        document.querySelector('[onclick="openModal(\'modal-db-responsible\')"]')?.addEventListener('click', function() {
            document.getElementById('db-resp-modal-title').textContent = 'Asignar Responsable';
            dbRespLabel.textContent = 'Asignar';
            resetDbRespBtn();
            document.getElementById('db-resp-form').action = dbRespStoreUrl;
            document.getElementById('db-resp-method').innerHTML = '';
            document.getElementById('db-resp-form').reset();
            resetPersonaSearch('db-resp');
            document.getElementById('db-resp-assigned_at').value = new Date().toISOString().slice(0, 10);
        });

        function openDbDeactivate(id, nombre) {
            document.getElementById('db-deactivate-name').textContent = nombre;
            document.getElementById('db-deactivate-form').action = dbRespUpdateBase + id + '/deactivate';
            document.getElementById('db-deactivate-date').value = new Date().toISOString().slice(0, 10);
            document.getElementById('db-deactivate-notes').value = '';
            openModal('modal-db-deactivate');
        }

        function openDbReactivate(id, nombre) {
            document.getElementById('db-reactivate-name').textContent = nombre;
            document.getElementById('db-reactivate-form').action = dbRespUpdateBase + id + '/reactivate';
            document.getElementById('db-reactivate-date').value = new Date().toISOString().slice(0, 10);
            openModal('modal-db-reactivate');
        }

        function toggleDbHistory() {
            const panel = document.getElementById('db-resp-history');
            const chevron = document.getElementById('db-history-chevron');
            const hidden = panel.classList.toggle('hidden');
            chevron.style.transform = hidden ? '' : 'rotate(180deg)';
        }

        // ── Documentos de Responsables BD ────────────────────────────────────
        const dbDocUploadBase = "{{ url('admin/servers/' . $server->id . '/database-servers/' . $databaseServer->id . '/responsibles') }}/";

        function openDbDocUpload(responsibleId) {
            document.getElementById('db-doc-upload-form').action        = dbDocUploadBase + responsibleId + '/documents';
            document.getElementById('db-doc-file-input').value          = '';
            document.getElementById('db-doc-description').value         = '';
            document.getElementById('db-doc-document_type').value       = '';
            document.getElementById('db-doc-document_number').value     = '';
            document.getElementById('db-doc-document_date').value       = '';
            document.getElementById('db-doc-document_notes').value      = '';
            document.getElementById('db-doc-file-label').innerHTML =
                'Arrastra el archivo aquí o <span class="text-indigo-600 dark:text-indigo-400 font-medium">haz clic para seleccionar</span>';
            openModal('modal-db-doc-upload');
        }

        function updateDbFileName(name) {
            if (!name) return;
            document.getElementById('db-doc-file-label').innerHTML =
                '<span class="font-medium text-gray-700 dark:text-gray-300">' + name + '</span>';
        }

        // ── Tipo de autenticación (formulario de edición) ─────────────────────
        const EDIT_AUTH_HINTS = {
            credentials: 'Autenticación estándar con usuario y contraseña.',
            windows:     'Usa la cuenta de Windows / Active Directory. No se almacenan credenciales.',
            kerberos:    'Kerberos o LDAP. Ingresa el principal (ej. usuario@DOMINIO). No se guarda contraseña.',
            iam:         'Credenciales delegadas a IAM (AWS RDS, Azure AD, GCP). No se guarda contraseña.',
            trusted:     'Conexión de confianza local (peer/socket). No requiere usuario ni contraseña.',
        };

        function editAuthTypeChanged(type) {
            const userWrap = document.getElementById('edit-user-wrap');
            const passWrap = document.getElementById('edit-pass-wrap');
            const hint     = document.getElementById('edit-auth-hint');
            userWrap.classList.toggle('hidden', !['credentials', 'kerberos', 'iam'].includes(type));
            passWrap.classList.toggle('hidden', type !== 'credentials');
            if (hint) hint.textContent = EDIT_AUTH_HINTS[type] ?? '';
        }

        document.addEventListener('DOMContentLoaded', () => {
            const sel = document.getElementById('edit-auth-type');
            if (sel) editAuthTypeChanged(sel.value);
        });

        // ── Contraseña ────────────────────────────────────────────────────────
        function togglePassword() {
            const mask = document.getElementById('pass-mask');
            const plain = document.getElementById('pass-plain');
            const icon = document.getElementById('eye-icon');
            const show = mask.classList.toggle('hidden');
            plain.classList.toggle('hidden', !show);
            icon.innerHTML = show ?
                `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>` :
                `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>`;
        }
    </script>
@endpush
