@extends('layouts.app')

@section('title', 'Configuración')

@section('content')
<div class="space-y-6" x-data="{ section: null }">

    {{-- ══════════════════════════════════════════════
         VISTA: HUB (sin sección activa)
    ══════════════════════════════════════════════ --}}
    <div x-show="section === null" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">

        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Configuración</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Selecciona una sección para ver su configuración detallada</p>
        </div>

        {{-- Cards de secciones --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

            {{-- General --}}
            <button @click="section = 'general'"
                    class="group text-left bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-5 hover:border-blue-400 dark:hover:border-blue-500 hover:shadow-md transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 dark:focus:ring-offset-gray-900">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-11 h-11 rounded-xl bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center group-hover:bg-blue-100 dark:group-hover:bg-blue-900/50 transition-colors">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Información General</h3>
                            <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 group-hover:text-blue-500 dark:group-hover:text-blue-400 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Nombre, entorno, zona horaria, versiones de Laravel y PHP</p>
                        <div class="mt-3 flex items-center gap-2">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $settings['app_env'] === 'production' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300' }}">
                                {{ ucfirst($settings['app_env']) }}
                            </span>
                            @if($settings['app_debug'])
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300">Debug ON</span>
                            @endif
                        </div>
                    </div>
                </div>
            </button>

            {{-- Base de Datos --}}
            <button @click="section = 'database'"
                    class="group text-left bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-5 hover:border-indigo-400 dark:hover:border-indigo-500 hover:shadow-md transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1 dark:focus:ring-offset-gray-900">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-11 h-11 rounded-xl bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center group-hover:bg-indigo-100 dark:group-hover:bg-indigo-900/50 transition-colors">
                        <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Base de Datos</h3>
                            <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 group-hover:text-indigo-500 dark:group-hover:text-indigo-400 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Driver, host, nombre de la base de datos y versión</p>
                        <div class="mt-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300 uppercase">
                                {{ $settings['db_connection'] }}
                            </span>
                        </div>
                    </div>
                </div>
            </button>

            {{-- Correo --}}
            <button @click="section = 'mail'"
                    class="group text-left bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-5 hover:border-violet-400 dark:hover:border-violet-500 hover:shadow-md transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-violet-500 focus:ring-offset-1 dark:focus:ring-offset-gray-900">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-11 h-11 rounded-xl bg-violet-50 dark:bg-violet-900/30 flex items-center justify-center group-hover:bg-violet-100 dark:group-hover:bg-violet-900/50 transition-colors">
                        <svg class="w-5 h-5 text-violet-600 dark:text-violet-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Correo Electrónico</h3>
                            <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 group-hover:text-violet-500 dark:group-hover:text-violet-400 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Servidor SMTP, remitente y configuración de envío</p>
                        <div class="mt-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-violet-100 text-violet-700 dark:bg-violet-900/40 dark:text-violet-300 uppercase">
                                {{ $settings['mail_mailer'] }}
                            </span>
                        </div>
                    </div>
                </div>
            </button>

            {{-- Sesión --}}
            <button @click="section = 'session'"
                    class="group text-left bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-5 hover:border-amber-400 dark:hover:border-amber-500 hover:shadow-md transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-1 dark:focus:ring-offset-gray-900">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-11 h-11 rounded-xl bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center group-hover:bg-amber-100 dark:group-hover:bg-amber-900/50 transition-colors">
                        <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Sesión & Seguridad</h3>
                            <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 group-hover:text-amber-500 dark:group-hover:text-amber-400 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Driver de sesión, duración y cookies seguras</p>
                        <div class="mt-3 flex items-center gap-2">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300 uppercase">
                                {{ $settings['session_driver'] }}
                            </span>
                            <span class="text-[10px] text-gray-500 dark:text-gray-400">{{ $settings['session_lifetime'] }} min</span>
                        </div>
                    </div>
                </div>
            </button>

            {{-- Servidor --}}
            <button @click="section = 'server'"
                    class="group text-left bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-5 hover:border-emerald-400 dark:hover:border-emerald-500 hover:shadow-md transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-1 dark:focus:ring-offset-gray-900">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-11 h-11 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center group-hover:bg-emerald-100 dark:group-hover:bg-emerald-900/50 transition-colors">
                        <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Servidor & PHP</h3>
                            <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 group-hover:text-emerald-500 dark:group-hover:text-emerald-400 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">SO, arquitectura, límites PHP y extensiones requeridas</p>
                        <div class="mt-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300">
                                PHP {{ $phpVersion }}
                            </span>
                        </div>
                    </div>
                </div>
            </button>

            {{-- Acción: Limpiar Cache --}}
            <form method="POST" action="{{ route('admin.settings.cache') }}" @submit.prevent="$el.submit()">
                @csrf
                <button type="submit"
                        class="group w-full text-left bg-white dark:bg-gray-800 border border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-5 hover:border-orange-400 dark:hover:border-orange-500 hover:shadow-md transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-1 dark:focus:ring-offset-gray-900">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0 w-11 h-11 rounded-xl bg-orange-50 dark:bg-orange-900/30 flex items-center justify-center group-hover:bg-orange-100 dark:group-hover:bg-orange-900/50 transition-colors">
                            <svg class="w-5 h-5 text-orange-600 dark:text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Limpiar Cache</h3>
                                <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 group-hover:text-orange-500 dark:group-hover:text-orange-400 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Ejecuta cache:clear, config:clear, view:clear y route:clear</p>
                            <div class="mt-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-orange-100 text-orange-700 dark:bg-orange-900/40 dark:text-orange-300">Acción inmediata</span>
                            </div>
                        </div>
                    </div>
                </button>
            </form>

        </div>
    </div>

    {{-- ══════════════════════════════════════════════
         VISTA: DETALLE DE SECCIÓN
    ══════════════════════════════════════════════ --}}
    <div x-show="section !== null" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">

        {{-- Header del detalle --}}
        <div class="flex items-center gap-4 mb-6">
            <button @click="section = null"
                    class="flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    <span x-show="section === 'general'">Información General</span>
                    <span x-show="section === 'database'">Base de Datos</span>
                    <span x-show="section === 'mail'">Correo Electrónico</span>
                    <span x-show="section === 'session'">Sesión & Seguridad</span>
                    <span x-show="section === 'server'">Servidor & PHP</span>
                </h1>
                <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">
                    Configuración · <button @click="section = null" class="text-blue-600 dark:text-blue-400 hover:underline">volver</button>
                </p>
            </div>
        </div>

        {{-- Panel de contenido --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="p-6">

                {{-- ── General ── --}}
                <div x-show="section === 'general'" x-cloak>
                    <div class="space-y-6">
                        <div>
                            <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-4">Aplicación</p>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <x-setting-row label="Nombre" :value="$settings['app_name']"/>
                                <x-setting-row label="URL Base" :value="$settings['app_url']"/>
                                <x-setting-row label="Entorno">
                                    @if($settings['app_env'] === 'production')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">Producción</span>
                                    @elseif($settings['app_env'] === 'local')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300">Local</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">{{ $settings['app_env'] }}</span>
                                    @endif
                                </x-setting-row>
                                <x-setting-row label="Modo Debug">
                                    @if($settings['app_debug'])
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300">Activo</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">Inactivo</span>
                                    @endif
                                </x-setting-row>
                                <x-setting-row label="Zona Horaria" :value="$settings['app_timezone']"/>
                                <x-setting-row label="Idioma" :value="strtoupper($settings['app_locale'])"/>
                            </div>
                        </div>
                        <div class="border-t border-gray-100 dark:border-gray-700 pt-6">
                            <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-4">Versiones</p>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <x-setting-row label="Laravel" :value="'v' . $laravelVersion"/>
                                <x-setting-row label="PHP" :value="'v' . $phpVersion"/>
                                <x-setting-row label="Servidor Web" :value="$_SERVER['SERVER_SOFTWARE'] ?? 'Apache/Nginx'"/>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── Base de Datos ── --}}
                <div x-show="section === 'database'" x-cloak>
                    <div class="space-y-6">
                        <div>
                            <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-4">Conexión</p>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <x-setting-row label="Driver" :value="strtoupper($settings['db_connection'])"/>
                                <x-setting-row label="Host" :value="config('database.connections.' . $settings['db_connection'] . '.host', '127.0.0.1')"/>
                                <x-setting-row label="Puerto" :value="config('database.connections.' . $settings['db_connection'] . '.port', '3306')"/>
                                <x-setting-row label="Base de Datos" :value="config('database.connections.' . $settings['db_connection'] . '.database', '—')"/>
                                <x-setting-row label="Usuario" :value="config('database.connections.' . $settings['db_connection'] . '.username', '—')"/>
                                <x-setting-row label="Versión" :value="$dbVersion"/>
                            </div>
                        </div>
                        <div class="border-t border-gray-100 dark:border-gray-700 pt-6">
                            <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-4">Cache</p>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <x-setting-row label="Driver de Cache" :value="strtoupper($settings['cache_driver'])"/>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── Correo ── --}}
                <div x-show="section === 'mail'" x-cloak>
                    <form method="POST" action="{{ route('admin.settings.mail') }}" class="space-y-6">
                        @csrf

                        {{-- Estado actual --}}
                        @if($settings['mail_configured'])
                            <div class="flex items-start gap-3 p-4 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-lg">
                                <svg class="w-5 h-5 text-emerald-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="text-sm text-emerald-700 dark:text-emerald-300">
                                    Configuración guardada en la base de datos. Los valores del <code class="font-mono bg-emerald-100 dark:bg-emerald-900/40 px-1 rounded">.env</code> quedan anulados por estos.
                                </p>
                            </div>
                        @else
                            <div class="flex items-start gap-3 p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg">
                                <svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                                </svg>
                                <p class="text-sm text-amber-700 dark:text-amber-300">
                                    Usando valores del <code class="font-mono bg-amber-100 dark:bg-amber-900/40 px-1 rounded">.env</code>. Completa el formulario para sobrescribirlos desde la base de datos.
                                </p>
                            </div>
                        @endif

                        {{-- Guía de configuración --}}
                        <div x-data="{ open: false, active: null }" class="rounded-lg border border-blue-200 dark:border-blue-800/60 bg-blue-50/50 dark:bg-blue-900/10 overflow-hidden">

                            {{-- Header colapsable --}}
                            <button type="button" @click="open = !open"
                                    class="w-full flex items-center justify-between px-4 py-3 text-left hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span class="text-sm font-semibold text-blue-700 dark:text-blue-300">Guía rápida de configuración SMTP</span>
                                    <span class="text-xs text-blue-500 dark:text-blue-400">(click para autocompletar)</span>
                                </div>
                                <svg class="w-4 h-4 text-blue-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            {{-- Contenido --}}
                            <div x-show="open" x-collapse class="border-t border-blue-200 dark:border-blue-800/60">
                                <div class="p-4 space-y-3">

                                    {{-- Cards de proveedores --}}
                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">

                                        {{-- Gmail --}}
                                        <div :class="active === 'gmail' ? 'ring-2 ring-blue-500 border-blue-400' : 'border-gray-200 dark:border-gray-600'"
                                             class="rounded-lg border bg-white dark:bg-gray-800 p-3 cursor-pointer hover:border-blue-400 dark:hover:border-blue-500 transition-all"
                                             @click="
                                                active = 'gmail';
                                                document.querySelector('[name=mail_mailer]').value = 'smtp';
                                                document.querySelector('[name=mail_host]').value = 'smtp.gmail.com';
                                                document.querySelector('[name=mail_port]').value = '587';
                                                document.querySelector('[name=mail_encryption]').value = 'tls';
                                             ">
                                            <div class="flex items-center gap-2 mb-2">
                                                <svg class="w-5 h-5 flex-shrink-0" viewBox="0 0 24 24" fill="none">
                                                    <path d="M22 6c0-1.1-.9-2-2-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6z" fill="#EA4335" opacity=".2"/>
                                                    <path d="M22 6l-10 7L2 6" stroke="#EA4335" stroke-width="1.5" stroke-linecap="round"/>
                                                </svg>
                                                <span class="text-xs font-semibold text-gray-700 dark:text-gray-200">Gmail</span>
                                                <span :class="active === 'gmail' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300' : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400'"
                                                      class="ml-auto text-[10px] font-semibold px-1.5 py-0.5 rounded-full transition-colors"
                                                      x-text="active === 'gmail' ? '✓ Seleccionado' : 'Seleccionar'">
                                                </span>
                                            </div>
                                            <pre class="text-[10px] leading-relaxed font-mono text-gray-500 dark:text-gray-400 whitespace-pre-wrap break-all">MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_ENCRYPTION=tls
MAIL_USERNAME=tucorreo@gmail.com
MAIL_PASSWORD=zzzzxxxxxzxxxvmgq  ← sin espacios</pre>
                                            <div class="mt-2 space-y-1.5">
                                                <p class="text-[10px] text-amber-600 dark:text-amber-400 flex items-start gap-1">
                                                    <svg class="w-3 h-3 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                                                    </svg>
                                                    Usa una <strong>Contraseña de aplicación</strong> de Google, no tu contraseña habitual. Requiere tener verificación en 2 pasos activada en tu cuenta.
                                                </p>
                                                <p class="text-[10px] text-blue-600 dark:text-blue-400 flex items-start gap-1">
                                                    <svg class="w-3 h-3 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    Google te la muestra como <code class="bg-blue-100 dark:bg-blue-900/40 px-1 rounded font-mono">zzzzx xxxxx zxxx vmgq</code> (con espacios) — pégala <strong>sin espacios</strong>: <code class="bg-blue-100 dark:bg-blue-900/40 px-1 rounded font-mono">zzzzxxxxxzxxxvmgq</code>
                                                </p>
                                            </div>
                                        </div>

                                        {{-- Outlook / Office 365 --}}
                                        <div :class="active === 'outlook' ? 'ring-2 ring-blue-500 border-blue-400' : 'border-gray-200 dark:border-gray-600'"
                                             class="rounded-lg border bg-white dark:bg-gray-800 p-3 cursor-pointer hover:border-blue-400 dark:hover:border-blue-500 transition-all"
                                             @click="
                                                active = 'outlook';
                                                document.querySelector('[name=mail_mailer]').value = 'smtp';
                                                document.querySelector('[name=mail_host]').value = 'smtp.office365.com';
                                                document.querySelector('[name=mail_port]').value = '587';
                                                document.querySelector('[name=mail_encryption]').value = 'starttls';
                                             ">
                                            <div class="flex items-center gap-2 mb-2">
                                                <svg class="w-5 h-5 flex-shrink-0" viewBox="0 0 24 24" fill="none">
                                                    <rect x="2" y="4" width="20" height="16" rx="2" fill="#0078D4" opacity=".2"/>
                                                    <path d="M22 6l-10 7L2 6" stroke="#0078D4" stroke-width="1.5" stroke-linecap="round"/>
                                                </svg>
                                                <span class="text-xs font-semibold text-gray-700 dark:text-gray-200">Outlook / Office 365</span>
                                                <span :class="active === 'outlook' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300' : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400'"
                                                      class="ml-auto text-[10px] font-semibold px-1.5 py-0.5 rounded-full transition-colors"
                                                      x-text="active === 'outlook' ? '✓ Seleccionado' : 'Seleccionar'">
                                                </span>
                                            </div>
                                            <pre class="text-[10px] leading-relaxed font-mono text-gray-500 dark:text-gray-400 whitespace-pre-wrap break-all">MAIL_HOST=smtp.office365.com
MAIL_PORT=587
MAIL_ENCRYPTION=starttls
MAIL_USERNAME=tucorreo@dominio.com
MAIL_PASSWORD="tu_contraseña"</pre>
                                            <p class="mt-2 text-[10px] text-blue-600 dark:text-blue-400 flex items-start gap-1">
                                                <svg class="w-3 h-3 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                Usa tu correo y contraseña de Microsoft 365. Si tienes MFA activado, genera una contraseña de aplicación desde el portal de seguridad.
                                            </p>
                                        </div>

                                        {{-- SMTP Personalizado --}}
                                        <div :class="active === 'custom' ? 'ring-2 ring-blue-500 border-blue-400' : 'border-gray-200 dark:border-gray-600'"
                                             class="rounded-lg border bg-white dark:bg-gray-800 p-3 cursor-pointer hover:border-blue-400 dark:hover:border-blue-500 transition-all"
                                             @click="
                                                active = 'custom';
                                                document.querySelector('[name=mail_mailer]').value = 'smtp';
                                                document.querySelector('[name=mail_host]').value = '';
                                                document.querySelector('[name=mail_port]').value = '587';
                                                document.querySelector('[name=mail_encryption]').value = 'tls';
                                             ">
                                            <div class="flex items-center gap-2 mb-2">
                                                <svg class="w-5 h-5 text-gray-500 dark:text-gray-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/>
                                                </svg>
                                                <span class="text-xs font-semibold text-gray-700 dark:text-gray-200">SMTP Personalizado</span>
                                                <span :class="active === 'custom' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300' : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400'"
                                                      class="ml-auto text-[10px] font-semibold px-1.5 py-0.5 rounded-full transition-colors"
                                                      x-text="active === 'custom' ? '✓ Seleccionado' : 'Seleccionar'">
                                                </span>
                                            </div>
                                            <pre class="text-[10px] leading-relaxed font-mono text-gray-500 dark:text-gray-400 whitespace-pre-wrap break-all">MAIL_HOST=tu.servidor.smtp
MAIL_PORT=587
MAIL_ENCRYPTION=tls
MAIL_USERNAME=usuario@dominio.com
MAIL_PASSWORD="tu_contraseña_smtp"</pre>
                                            <p class="mt-2 text-[10px] text-gray-500 dark:text-gray-400 flex items-start gap-1">
                                                <svg class="w-3 h-3 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                Ingresa el host de tu proveedor de hosting o servidor de correo institucional.
                                            </p>
                                        </div>

                                    </div>

                                    {{-- Referencia de puertos --}}
                                    <div class="flex flex-wrap gap-3 pt-1">
                                        <p class="text-[11px] text-gray-400 dark:text-gray-500 font-semibold uppercase tracking-wider self-center">Puertos comunes:</p>
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-[11px] font-mono text-gray-600 dark:text-gray-300">
                                            <strong>587</strong> — TLS / STARTTLS <span class="text-emerald-500">(recomendado)</span>
                                        </span>
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-[11px] font-mono text-gray-600 dark:text-gray-300">
                                            <strong>465</strong> — SSL
                                        </span>
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-[11px] font-mono text-gray-600 dark:text-gray-300">
                                            <strong>25</strong> — Sin cifrado <span class="text-red-400">(no recomendado)</span>
                                        </span>
                                    </div>

                                </div>
                            </div>
                        </div>

                        {{-- Fila 1: Mailer + Encriptación --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Driver</label>
                                <select name="mail_mailer"
                                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    @foreach(['smtp' => 'SMTP', 'sendmail' => 'Sendmail', 'mailgun' => 'Mailgun', 'ses' => 'Amazon SES', 'log' => 'Log (testing)'] as $val => $label)
                                        <option value="{{ $val }}" {{ $settings['mail_mailer'] === $val ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('mail_mailer')<p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Encriptación</label>
                                <select name="mail_encryption"
                                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="tls"      {{ $settings['mail_encryption'] === 'tls'      ? 'selected' : '' }}>TLS</option>
                                    <option value="ssl"      {{ $settings['mail_encryption'] === 'ssl'      ? 'selected' : '' }}>SSL</option>
                                    <option value="starttls" {{ $settings['mail_encryption'] === 'starttls' ? 'selected' : '' }}>STARTTLS</option>
                                    <option value=""         {{ $settings['mail_encryption'] === ''         ? 'selected' : '' }}>Ninguna</option>
                                </select>
                                @error('mail_encryption')<p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        {{-- Fila 2: Host + Puerto --}}
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Host SMTP</label>
                                <input type="text" name="mail_host" value="{{ old('mail_host', $settings['mail_host']) }}"
                                       placeholder="smtp.gmail.com"
                                       class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm px-3 py-2 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @error('mail_host')<p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Puerto</label>
                                <input type="number" name="mail_port" value="{{ old('mail_port', $settings['mail_port']) }}"
                                       placeholder="587"
                                       class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm px-3 py-2 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @error('mail_port')<p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        {{-- Fila 3: Usuario + Contraseña --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Usuario SMTP</label>
                                <input type="text" name="mail_username" value="{{ old('mail_username', $settings['mail_username']) }}"
                                       placeholder="usuario@dominio.com"
                                       class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm px-3 py-2 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @error('mail_username')<p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                            </div>
                            <div x-data="{ show: false }">
                                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">
                                    Contraseña SMTP
                                    @if($settings['mail_configured'])
                                        <span class="normal-case font-normal text-gray-400 dark:text-gray-500">(dejar vacío para no cambiar)</span>
                                    @endif
                                </label>
                                <div class="relative">
                                    <input :type="show ? 'text' : 'password'" name="mail_password" value=""
                                           placeholder="••••••••"
                                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm px-3 py-2 pr-10 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <button type="button" @click="show = !show"
                                            class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                        <svg x-show="!show" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        <svg x-show="show" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                        </svg>
                                    </button>
                                </div>
                                @error('mail_password')<p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        {{-- Fila 4: Remitente --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Email Remitente</label>
                                <input type="email" name="mail_from" value="{{ old('mail_from', $settings['mail_from']) }}"
                                       placeholder="noreply@midominio.com"
                                       class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm px-3 py-2 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @error('mail_from')<p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Nombre Remitente</label>
                                <input type="text" name="mail_from_name" value="{{ old('mail_from_name', $settings['mail_from_name']) }}"
                                       placeholder="SGATI Sistema"
                                       class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm px-3 py-2 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @error('mail_from_name')<p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        {{-- Acciones --}}
                        <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-100 dark:border-gray-700">
                            <button type="submit"
                                    class="inline-flex items-center gap-2 px-5 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 dark:focus:ring-offset-gray-800">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Guardar Configuración
                            </button>
                        </div>

                    </form>
                </div>

                {{-- ── Sesión & Seguridad ── --}}
                <div x-show="section === 'session'" x-cloak>
                    <form method="POST" action="{{ route('admin.settings.security') }}" class="space-y-8">
                        @csrf

                        {{-- Banner de estado --}}
                        @if($settings['security_configured'])
                            <div class="flex items-start gap-3 p-4 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-lg">
                                <svg class="w-5 h-5 text-emerald-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="text-sm text-emerald-700 dark:text-emerald-300">Configuración de seguridad personalizada activa. Los cambios aplican en la siguiente solicitud.</p>
                            </div>
                        @endif

                        {{-- ── Sesión ── --}}
                        <div>
                            <div class="flex items-center gap-2 mb-4">
                                <div class="w-7 h-7 rounded-lg bg-amber-100 dark:bg-amber-900/40 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-3.5 h-3.5 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">Duración de Sesión</p>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">
                                        Duración total de sesión (minutos)
                                    </label>
                                    <input type="number" name="session_lifetime"
                                           value="{{ old('session_lifetime', $settings['session_lifetime']) }}"
                                           min="5" max="1440"
                                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                                    <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">Mín. 5 — Máx. 1440 (24h). Por defecto: 120 min.</p>
                                    @error('session_lifetime')<p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">
                                        Tiempo de inactividad (minutos)
                                    </label>
                                    <input type="number" name="session_inactivity"
                                           value="{{ old('session_inactivity', $settings['session_inactivity']) }}"
                                           min="0" max="480"
                                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                                    <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">0 = desactivado. Si el usuario no interactúa, se cierra la sesión automáticamente.</p>
                                    @error('session_inactivity')<p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                                </div>
                            </div>

                            {{-- Cierre al cerrar navegador --}}
                            <div class="mt-4">
                                <label class="flex items-start gap-3 cursor-pointer select-none group">
                                    <div class="relative mt-0.5">
                                        <input type="hidden" name="session_expire_on_close" value="0">
                                        <input type="checkbox" name="session_expire_on_close" value="1"
                                               {{ $settings['session_expire_on_close'] ? 'checked' : '' }}
                                               class="sr-only peer">
                                        <div class="w-10 h-6 rounded-full border-2 border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 peer-checked:bg-amber-500 peer-checked:border-amber-500 transition-colors"></div>
                                        <div class="absolute top-1 left-1 w-4 h-4 rounded-full bg-white shadow transition-transform peer-checked:translate-x-4"></div>
                                    </div>
                                    <div>
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Cerrar sesión al cerrar el navegador</span>
                                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Elimina la cookie de sesión cuando el usuario cierra el navegador, sin importar el tiempo configurado.</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="border-t border-gray-100 dark:border-gray-700"></div>

                        {{-- ── Control de acceso ── --}}
                        <div>
                            <div class="flex items-center gap-2 mb-4">
                                <div class="w-7 h-7 rounded-lg bg-red-100 dark:bg-red-900/40 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-3.5 h-3.5 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                </div>
                                <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">Control de Intentos de Login</p>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">
                                        Máx. intentos fallidos antes de bloqueo
                                    </label>
                                    <input type="number" name="max_login_attempts"
                                           value="{{ old('max_login_attempts', $settings['max_login_attempts']) }}"
                                           min="1" max="20"
                                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                                    <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">Por defecto: 5 intentos.</p>
                                    @error('max_login_attempts')<p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">
                                        Duración del bloqueo (minutos)
                                    </label>
                                    <input type="number" name="lockout_duration"
                                           value="{{ old('lockout_duration', $settings['lockout_duration']) }}"
                                           min="1" max="60"
                                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                                    <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">Tiempo que el usuario permanece bloqueado tras agotar los intentos.</p>
                                    @error('lockout_duration')<p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                                </div>
                            </div>

                            {{-- Resumen visual --}}
                            <div class="mt-5 p-4 rounded-lg bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600">
                                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Resumen de configuración activa</p>
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-center">
                                    <div>
                                        <p class="text-2xl font-bold text-amber-600 dark:text-amber-400">{{ $settings['session_lifetime'] }}</p>
                                        <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">min. sesión</p>
                                    </div>
                                    <div>
                                        <p class="text-2xl font-bold text-amber-600 dark:text-amber-400">
                                            {{ $settings['session_inactivity'] > 0 ? $settings['session_inactivity'] : '—' }}
                                        </p>
                                        <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">min. inactividad</p>
                                    </div>
                                    <div>
                                        <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $settings['max_login_attempts'] }}</p>
                                        <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">intentos máx.</p>
                                    </div>
                                    <div>
                                        <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $settings['lockout_duration'] }}</p>
                                        <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">min. bloqueo</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="border-t border-gray-100 dark:border-gray-700"></div>

                        {{-- ── Autenticación Avanzada ── --}}
                        <div>
                            <div class="flex items-center gap-2 mb-4">
                                <div class="w-7 h-7 rounded-lg bg-violet-100 dark:bg-violet-900/40 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-3.5 h-3.5 text-violet-600 dark:text-violet-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                    </svg>
                                </div>
                                <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">Autenticación Avanzada</p>
                            </div>

                            <div class="space-y-3">

                                {{-- Toggle: Recuperación de contraseña --}}
                                <label class="flex items-start gap-3 cursor-pointer select-none group p-3 rounded-lg border border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/40 transition-colors">
                                    <div class="relative mt-0.5 flex-shrink-0">
                                        <input type="hidden" name="password_reset_enabled" value="0">
                                        <input type="checkbox" name="password_reset_enabled" value="1"
                                               {{ $settings['password_reset_enabled'] ? 'checked' : '' }}
                                               class="sr-only peer">
                                        <div class="w-10 h-6 rounded-full border-2 border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 peer-checked:bg-violet-500 peer-checked:border-violet-500 transition-colors"></div>
                                        <div class="absolute top-1 left-1 w-4 h-4 rounded-full bg-white shadow transition-transform peer-checked:translate-x-4"></div>
                                    </div>
                                    <div class="flex-1">
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Recuperación de contraseña</span>
                                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                                            Permite a los usuarios restablecer su contraseña mediante un enlace enviado al correo. Si se desactiva, la opción "¿Olvidaste tu contraseña?" no estará disponible.
                                        </p>
                                    </div>
                                    <span class="flex-shrink-0 mt-0.5">
                                        @if($settings['password_reset_enabled'])
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-violet-100 text-violet-700 dark:bg-violet-900/40 dark:text-violet-300">Activo</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400">Inactivo</span>
                                        @endif
                                    </span>
                                </label>

                                {{-- Toggle: 2FA por email --}}
                                <label class="flex items-start gap-3 cursor-pointer select-none group p-3 rounded-lg border border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/40 transition-colors">
                                    <div class="relative mt-0.5 flex-shrink-0">
                                        <input type="hidden" name="two_factor_enabled" value="0">
                                        <input type="checkbox" name="two_factor_enabled" value="1"
                                               {{ $settings['two_factor_enabled'] ? 'checked' : '' }}
                                               class="sr-only peer">
                                        <div class="w-10 h-6 rounded-full border-2 border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 peer-checked:bg-violet-500 peer-checked:border-violet-500 transition-colors"></div>
                                        <div class="absolute top-1 left-1 w-4 h-4 rounded-full bg-white shadow transition-transform peer-checked:translate-x-4"></div>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Verificación en 2 pasos (2FA por email)</span>
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300">Requiere SMTP</span>
                                        </div>
                                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                                            Al iniciar sesión, el usuario recibirá un código de 6 dígitos en su correo que deberá ingresar para completar el acceso. El código expira en 10 minutos.
                                        </p>
                                    </div>
                                    <span class="flex-shrink-0 mt-0.5">
                                        @if($settings['two_factor_enabled'])
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-violet-100 text-violet-700 dark:bg-violet-900/40 dark:text-violet-300">Activo</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400">Inactivo</span>
                                        @endif
                                    </span>
                                </label>

                                {{-- Toggle: Sesión única --}}
                                <label class="flex items-start gap-3 cursor-pointer select-none group p-3 rounded-lg border border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/40 transition-colors">
                                    <div class="relative mt-0.5 flex-shrink-0">
                                        <input type="hidden" name="single_session_enabled" value="0">
                                        <input type="checkbox" name="single_session_enabled" value="1"
                                               {{ $settings['single_session_enabled'] ? 'checked' : '' }}
                                               class="sr-only peer">
                                        <div class="w-10 h-6 rounded-full border-2 border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 peer-checked:bg-violet-500 peer-checked:border-violet-500 transition-colors"></div>
                                        <div class="absolute top-1 left-1 w-4 h-4 rounded-full bg-white shadow transition-transform peer-checked:translate-x-4"></div>
                                    </div>
                                    <div class="flex-1">
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Sesión única por usuario</span>
                                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                                            Un usuario solo puede tener una sesión activa a la vez. Si inicia sesión desde otro dispositivo o navegador, la sesión anterior se cierra automáticamente.
                                        </p>
                                    </div>
                                    <span class="flex-shrink-0 mt-0.5">
                                        @if($settings['single_session_enabled'])
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-violet-100 text-violet-700 dark:bg-violet-900/40 dark:text-violet-300">Activo</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400">Inactivo</span>
                                        @endif
                                    </span>
                                </label>

                            </div>
                        </div>

                        {{-- Acción --}}
                        <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-100 dark:border-gray-700">
                            <button type="submit"
                                    class="inline-flex items-center gap-2 px-5 py-2 bg-amber-500 text-white text-sm font-medium rounded-lg hover:bg-amber-600 transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-1 dark:focus:ring-offset-gray-800">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Guardar Seguridad
                            </button>
                        </div>
                    </form>
                </div>

                {{-- ── Servidor ── --}}
                <div x-show="section === 'server'" x-cloak>
                    <div class="space-y-6">
                        <div>
                            <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-4">Entorno</p>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <x-setting-row label="Sistema Operativo" :value="PHP_OS_FAMILY . ' (' . PHP_OS . ')'"/>
                                <x-setting-row label="Arquitectura" :value="PHP_INT_SIZE === 8 ? '64-bit' : '32-bit'"/>
                                <x-setting-row label="Memoria Límite PHP" :value="ini_get('memory_limit')"/>
                                <x-setting-row label="Tiempo Máx. Ejecución" :value="ini_get('max_execution_time') . 's'"/>
                                <x-setting-row label="Tamaño Máx. Upload" :value="ini_get('upload_max_filesize')"/>
                                <x-setting-row label="POST Máx." :value="ini_get('post_max_size')"/>
                            </div>
                        </div>
                        <div class="border-t border-gray-100 dark:border-gray-700 pt-6">
                            <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-4">Extensiones PHP</p>
                            <div class="flex flex-wrap gap-2">
                                @foreach(['pdo','pdo_mysql','mbstring','openssl','tokenizer','xml','ctype','json','fileinfo','bcmath','curl'] as $ext)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium {{ extension_loaded($ext) ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300' : 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300' }}">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        @if(extension_loaded($ext))
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                        @else
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                        @endif
                                    </svg>
                                    {{ $ext }}
                                </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>
@endsection
