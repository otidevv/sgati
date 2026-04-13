@extends('layouts.app')
@section('title', 'Nuevo Servicio / API — ' . $system->name)

@section('content')
<div class="max-w-3xl mx-auto"
     x-data="serviceWizard()"
     x-init="init()">

    {{-- ── Encabezado ── --}}
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('systems.show', $system) }}"
           class="inline-flex items-center justify-center w-10 h-10 rounded-lg
                  bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300
                  hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Nuevo Servicio / API</h1>
            <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">{{ $system->name }}</p>
        </div>
    </div>

    {{-- ── Barra de progreso ── --}}
    <div class="mb-6">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-medium text-gray-500 dark:text-gray-400">
                Paso <span x-text="currentStep"></span> de <span x-text="totalSteps()"></span>
            </span>
            <span class="text-xs font-medium text-blue-600 dark:text-blue-400" x-text="stepLabel()"></span>
        </div>
        <div class="h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
            <div class="h-full bg-blue-600 rounded-full transition-all duration-500"
                 :style="`width: ${(currentStep / totalSteps()) * 100}%`"></div>
        </div>
        {{-- Dots --}}
        <div class="flex justify-between mt-2">
            <template x-for="n in totalSteps()" :key="n">
                <div class="flex flex-col items-center">
                    <div class="w-2 h-2 rounded-full transition-colors duration-300"
                         :class="n <= currentStep ? 'bg-blue-600' : 'bg-gray-300 dark:bg-gray-600'"></div>
                </div>
            </template>
        </div>
    </div>

    {{-- ── Formulario ── --}}
    <form action="{{ route('systems.services.store', $system) }}" method="POST" id="wizard-form">
        @csrf

        {{-- Campos hidden (siempre en el DOM, siempre se envían) --}}
        <input type="hidden" name="direction"               x-model="direction">
        <input type="hidden" name="service_name"            x-model="service_name">
        <input type="hidden" name="service_type"            x-model="service_type">
        <input type="hidden" name="environment"             x-model="environment">
        <input type="hidden" name="version"                 x-model="version">
        <input type="hidden" name="description"             x-model="description">
        <input type="hidden" name="is_active"               :value="is_active ? '1' : '0'">
        <input type="hidden" name="endpoint_url"            x-model="endpoint_url">
        <input type="hidden" name="valid_from"              x-model="valid_from">
        <input type="hidden" name="valid_until"             x-model="valid_until">
        <input type="hidden" name="requested_by_persona_id" x-model="requested_by_persona_id">
        {{-- Proveedor --}}
        <input type="hidden" name="provider_type"           x-model="provider_type">
        <input type="hidden" name="provider_system_id"      x-model="provider_system_id">
        <input type="hidden" name="provider_name"           x-model="provider_name">
        {{-- Credenciales consumido --}}
        <input type="hidden" name="auth_type"               x-model="auth_type">
        <input type="hidden" name="api_key"                 x-model="api_key">
        <input type="hidden" name="api_secret"              x-model="api_secret">
        <input type="hidden" name="token"                   x-model="token">
        <input type="hidden" name="token_expires_at"        x-model="token_expires_at">
        {{-- Gateway (expuesto) --}}
        <input type="hidden" name="gateway_enabled"         :value="gateway_enabled ? '1' : '0'">
        <input type="hidden" name="gateway_require_key"     :value="gateway_require_key ? '1' : '0'">
        <input type="hidden" name="gateway_rate_per_minute" x-model="gateway_rate_per_minute">
        <input type="hidden" name="gateway_rate_per_day"    x-model="gateway_rate_per_day">
        <input type="hidden" name="gateway_active_from"     x-model="gateway_active_from">
        <input type="hidden" name="gateway_active_to"       x-model="gateway_active_to">

        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">

            {{-- ══════════════════════════════════════════════════════════
                 PASO 1 — Dirección
            ══════════════════════════════════════════════════════════ --}}
            <div x-show="currentStep === 1" x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-x-4"
                 x-transition:enter-end="opacity-100 translate-x-0">

                <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">¿Qué tipo de integración es?</h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Define si tu sistema expone un servicio o consume uno externo.</p>
                </div>

                <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Tarjeta: Expuesto --}}
                    <button type="button"
                            @click="direction = 'exposed'; nextStep()"
                            class="group relative flex flex-col items-start gap-3 p-5 rounded-xl border-2 text-left transition-all duration-200"
                            :class="direction === 'exposed'
                                ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20'
                                : 'border-gray-200 dark:border-gray-700 hover:border-blue-300 dark:hover:border-blue-600'">
                        <div class="flex items-center justify-between w-full">
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-blue-100 dark:bg-blue-900/40">
                                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <svg class="w-5 h-5 text-gray-300 dark:text-gray-600 group-hover:text-blue-400 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 dark:text-white">Mi sistema expone un servicio</p>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Tu sistema tiene una API o endpoint que otros pueden consumir. Se generará una URL de gateway para controlar el acceso.</p>
                        </div>
                        <span class="inline-flex items-center gap-1.5 text-xs font-medium text-blue-700 dark:text-blue-400">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            Incluye gateway con control de acceso
                        </span>
                    </button>

                    {{-- Tarjeta: Consumido --}}
                    <button type="button"
                            @click="direction = 'consumed'; nextStep()"
                            class="group relative flex flex-col items-start gap-3 p-5 rounded-xl border-2 text-left transition-all duration-200"
                            :class="direction === 'consumed'
                                ? 'border-green-500 bg-green-50 dark:bg-green-900/20'
                                : 'border-gray-200 dark:border-gray-700 hover:border-green-300 dark:hover:border-green-600'">
                        <div class="flex items-center justify-between w-full">
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-green-100 dark:bg-green-900/40">
                                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                            </div>
                            <svg class="w-5 h-5 text-gray-300 dark:text-gray-600 group-hover:text-green-400 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 dark:text-white">Mi sistema consume un servicio</p>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Tu sistema utiliza una API o servicio de terceros (otro sistema interno, RENIEC, SUNAT, etc.). Se guardan las credenciales de acceso.</p>
                        </div>
                        <span class="inline-flex items-center gap-1.5 text-xs font-medium text-green-700 dark:text-green-400">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                            </svg>
                            Almacenamiento seguro de credenciales
                        </span>
                    </button>
                </div>
            </div>

            {{-- ══════════════════════════════════════════════════════════
                 PASO 2 — Información básica
            ══════════════════════════════════════════════════════════ --}}
            <div x-show="currentStep === 2" x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-x-4"
                 x-transition:enter-end="opacity-100 translate-x-0">

                <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Información básica</h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Identifica el servicio con un nombre descriptivo y su tipo de protocolo.</p>
                </div>

                <div class="p-6 space-y-5">
                    {{-- Nombre --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Nombre del servicio <span class="text-red-500">*</span>
                        </label>
                        <input type="text" x-model="service_name" placeholder="API de Matrícula, Consulta de Expedientes…"
                               class="block w-full rounded-lg border border-gray-300 dark:border-gray-600
                                      bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                      px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                    </div>

                    {{-- Tipo (pills) --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Tipo de protocolo <span class="text-red-500">*</span>
                        </label>
                        <div class="flex flex-wrap gap-2">
                            <template x-for="t in serviceTypes" :key="t.value">
                                <button type="button" @click="service_type = t.value"
                                        class="px-3.5 py-1.5 rounded-full text-sm font-medium border transition-colors duration-150"
                                        :class="service_type === t.value
                                            ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300'
                                            : 'border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:border-blue-300 dark:hover:border-blue-600'"
                                        x-text="t.label"></button>
                            </template>
                        </div>
                    </div>

                    {{-- Ambiente (segmented control) --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Ambiente</label>
                        <div class="inline-flex rounded-lg border border-gray-300 dark:border-gray-600 overflow-hidden">
                            <template x-for="e in environments" :key="e.value">
                                <button type="button" @click="environment = e.value"
                                        class="px-4 py-2 text-sm font-medium transition-colors duration-150 border-r border-gray-300 dark:border-gray-600 last:border-r-0"
                                        :class="environment === e.value
                                            ? 'bg-blue-600 text-white'
                                            : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600'"
                                        x-text="e.label"></button>
                            </template>
                        </div>
                    </div>

                    {{-- Versión --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Versión</label>
                            <input type="text" x-model="version" placeholder="v1, 2.0, 2024…"
                                   class="block w-full rounded-lg border border-gray-300 dark:border-gray-600
                                          bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                          px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                        </div>
                        {{-- Activo --}}
                        <div class="flex items-end pb-1">
                            <div class="flex items-center justify-between w-full p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Servicio activo</span>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" class="sr-only peer" x-model="is_active">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:ring-2 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer peer-checked:bg-blue-600
                                                after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all
                                                peer-checked:after:translate-x-full"></div>
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- Descripción --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Descripción</label>
                        <textarea x-model="description" rows="3" placeholder="¿Para qué sirve este servicio? ¿Qué datos expone o consume?"
                                  class="block w-full rounded-lg border border-gray-300 dark:border-gray-600
                                         bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                         px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none resize-none"></textarea>
                    </div>
                </div>
            </div>

            {{-- ══════════════════════════════════════════════════════════
                 PASO 3 (EXPUESTO) — Backend real
            ══════════════════════════════════════════════════════════ --}}
            <div x-show="currentStep === 3 && direction === 'exposed'" x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-x-4"
                 x-transition:enter-end="opacity-100 translate-x-0">

                <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Backend real</h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">La URL de tu API original. Solo será visible internamente — los usuarios verán la URL del gateway.</p>
                </div>

                <div class="p-6 space-y-5">
                    {{-- URL del backend --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            URL del backend <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            <input type="url" x-model="endpoint_url"
                                   placeholder="https://apidatos.unamad.edu.pe/api/consulta"
                                   class="block w-full pl-9 pr-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600
                                          bg-white dark:bg-gray-700 text-gray-900 dark:text-white font-mono text-sm
                                          focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                        </div>

                        {{-- Hint: cómo construye la URL el gateway --}}
                        <div class="mt-2 rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700/40 p-3 space-y-2">
                            <p class="text-xs font-medium text-blue-700 dark:text-blue-300 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Pega solo la URL base — sin parámetros ni barras finales
                            </p>
                            <div x-show="endpoint_url" x-transition class="space-y-1.5">
                                <p class="text-xs text-blue-600 dark:text-blue-400">El gateway construye la URL destino así:</p>
                                <div class="bg-gray-900 rounded-md px-3 py-2 overflow-x-auto">
                                    <code class="text-xs font-mono whitespace-nowrap">
                                        <span class="text-gray-400">Gateway:</span>
                                        <span class="text-green-300"> /api/gw/{slug}/</span><span class="text-yellow-300">{parametro}</span>
                                        <span class="text-gray-500"> →</span>
                                        <span class="text-cyan-300" x-text="' ' + (endpoint_url.replace(/\/$/, '') || '…')"></span><span class="text-yellow-300">/{parametro}</span>
                                    </code>
                                </div>
                                <p class="text-[11px] text-blue-500 dark:text-blue-400">
                                    Ej: si un consumidor llama a <code class="font-mono">/api/gw/{slug}/12345678</code>, el gateway reenvía a <code class="font-mono" x-text="(endpoint_url.replace(/\/$/, '') || '…') + '/12345678'"></code>
                                </p>
                            </div>
                        </div>

                        <p class="mt-1.5 text-xs text-amber-600 dark:text-amber-400 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.962-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                            </svg>
                            Esta URL real nunca se comparte con los consumidores del gateway.
                        </p>
                    </div>

                    {{-- Vigencia --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Vigencia desde</label>
                            <input type="date" x-model="valid_from"
                                   class="block w-full rounded-lg border border-gray-300 dark:border-gray-600
                                          bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                          px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Vigencia hasta</label>
                            <input type="date" x-model="valid_until"
                                   class="block w-full rounded-lg border border-gray-300 dark:border-gray-600
                                          bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                          px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                        </div>
                    </div>

                    {{-- Auth del backend (toggle) --}}
                    <div class="rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <button type="button" @click="backendHasAuth = !backendHasAuth"
                                class="w-full flex items-center justify-between px-4 py-3 text-sm font-medium
                                       text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                </svg>
                                ¿Tu API real requiere autenticación para ser llamada?
                            </span>
                            <div class="flex items-center gap-2">
                                <span class="text-xs" :class="backendHasAuth ? 'text-amber-500 dark:text-amber-400' : 'text-gray-400 dark:text-gray-500'"
                                      x-text="backendHasAuth ? 'Sí, tiene credenciales' : 'No, es pública'"></span>
                                <svg class="w-4 h-4 transition-transform duration-200" :class="backendHasAuth ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                        </button>

                        {{-- Explicación contextual --}}
                        <div class="px-4 py-2.5 bg-gray-50 dark:bg-gray-700/40 border-t border-gray-100 dark:border-gray-700/60 text-xs text-gray-500 dark:text-gray-400">
                            <p>
                                Esto aplica a <strong class="text-gray-700 dark:text-gray-300">tu API real</strong> (el backend).
                                Si para llamarla necesitas enviar una clave, token u otro header de autenticación, activa esta opción.
                                El gateway la reenvía automáticamente al backend en cada petición.
                            </p>
                            <p class="mt-1.5 text-[11px] text-gray-400 dark:text-gray-500">
                                Ejemplo: tu API interna exige <code class="font-mono bg-gray-200 dark:bg-gray-600 px-1 rounded">Authorization: Bearer eyJ...</code>
                                o un header <code class="font-mono bg-gray-200 dark:bg-gray-600 px-1 rounded">X-API-Key: abc123</code> para responder.
                                Si tu API <em>no</em> requiere nada (como <code class="font-mono bg-gray-200 dark:bg-gray-600 px-1 rounded">https://apidatos.unamad.edu.pe/api/consulta</code>), deja esta opción cerrada.
                            </p>
                        </div>

                        <div x-show="backendHasAuth" x-transition class="border-t border-gray-100 dark:border-gray-700">

                            {{-- Selector visual de tipo de auth --}}
                            <div class="px-4 pt-4">
                                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">¿Cómo se autentica tu API real?</label>
                                <div class="grid grid-cols-3 gap-2">
                                    <button type="button" @click="auth_type = 'API Key'"
                                            class="flex flex-col items-center gap-1.5 p-2.5 rounded-lg border-2 text-center transition-all"
                                            :class="auth_type === 'API Key'
                                                ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20'
                                                : 'border-gray-200 dark:border-gray-600 hover:border-blue-300 dark:hover:border-blue-700'">
                                        <svg class="w-5 h-5" :class="auth_type === 'API Key' ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                        </svg>
                                        <span class="text-[11px] font-medium leading-tight" :class="auth_type === 'API Key' ? 'text-blue-700 dark:text-blue-300' : 'text-gray-600 dark:text-gray-400'">API Key</span>
                                        <span class="text-[10px] text-gray-400 dark:text-gray-500 leading-tight">Header o query param</span>
                                    </button>
                                    <button type="button" @click="auth_type = 'Bearer Token'"
                                            class="flex flex-col items-center gap-1.5 p-2.5 rounded-lg border-2 text-center transition-all"
                                            :class="auth_type === 'Bearer Token'
                                                ? 'border-purple-500 bg-purple-50 dark:bg-purple-900/20'
                                                : 'border-gray-200 dark:border-gray-600 hover:border-purple-300 dark:hover:border-purple-700'">
                                        <svg class="w-5 h-5" :class="auth_type === 'Bearer Token' ? 'text-purple-600 dark:text-purple-400' : 'text-gray-400'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                        </svg>
                                        <span class="text-[11px] font-medium leading-tight" :class="auth_type === 'Bearer Token' ? 'text-purple-700 dark:text-purple-300' : 'text-gray-600 dark:text-gray-400'">Bearer / JWT</span>
                                        <span class="text-[10px] text-gray-400 dark:text-gray-500 leading-tight">Authorization header</span>
                                    </button>
                                    <button type="button" @click="auth_type = 'Basic Auth'"
                                            class="flex flex-col items-center gap-1.5 p-2.5 rounded-lg border-2 text-center transition-all"
                                            :class="auth_type === 'Basic Auth'
                                                ? 'border-orange-500 bg-orange-50 dark:bg-orange-900/20'
                                                : 'border-gray-200 dark:border-gray-600 hover:border-orange-300 dark:hover:border-orange-700'">
                                        <svg class="w-5 h-5" :class="auth_type === 'Basic Auth' ? 'text-orange-600 dark:text-orange-400' : 'text-gray-400'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        <span class="text-[11px] font-medium leading-tight" :class="auth_type === 'Basic Auth' ? 'text-orange-700 dark:text-orange-300' : 'text-gray-600 dark:text-gray-400'">Basic Auth</span>
                                        <span class="text-[10px] text-gray-400 dark:text-gray-500 leading-tight">Usuario + contraseña</span>
                                    </button>
                                </div>
                            </div>

                            <div class="p-4 space-y-3">

                                {{-- API Key --}}
                                <div x-show="auth_type === 'API Key'" x-transition>
                                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                                        API Key del backend
                                        <span class="ml-1 font-normal text-gray-400">— la clave que te dio el proveedor de la API</span>
                                    </label>
                                    <input type="password" x-model="api_key" autocomplete="new-password"
                                           placeholder="abc123xyz..."
                                           class="block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-blue-500 outline-none">
                                    <p class="mt-1 text-[11px] text-gray-400 dark:text-gray-500">
                                        El gateway la enviará como <code class="font-mono bg-gray-100 dark:bg-gray-600 px-1 rounded">X-API-Key: &lt;valor&gt;</code> al backend en cada petición.
                                    </p>
                                </div>

                                {{-- Bearer Token --}}
                                <div x-show="auth_type === 'Bearer Token'" x-transition>
                                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                                        Token / Bearer
                                        <span class="ml-1 font-normal text-gray-400">— el token JWT u OAuth que te entregaron</span>
                                    </label>
                                    <input type="password" x-model="token" autocomplete="new-password"
                                           placeholder="eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
                                           class="block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-purple-500 outline-none">
                                    <p class="mt-1 text-[11px] text-gray-400 dark:text-gray-500">
                                        El gateway lo enviará como <code class="font-mono bg-gray-100 dark:bg-gray-600 px-1 rounded">Authorization: Bearer &lt;token&gt;</code> al backend.
                                    </p>
                                </div>

                                {{-- Basic Auth --}}
                                <div x-show="auth_type === 'Basic Auth'" x-transition class="space-y-3">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                                            Usuario
                                            <span class="ml-1 font-normal text-gray-400">— nombre de usuario del backend</span>
                                        </label>
                                        <input type="text" x-model="api_key" autocomplete="new-password"
                                               placeholder="admin"
                                               class="block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-orange-500 outline-none">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                                            Contraseña
                                            <span class="ml-1 font-normal text-gray-400">— contraseña del backend</span>
                                        </label>
                                        <input type="password" x-model="api_secret" autocomplete="new-password"
                                               placeholder="••••••••"
                                               class="block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-orange-500 outline-none">
                                    </div>
                                    <p class="text-[11px] text-gray-400 dark:text-gray-500">
                                        El gateway enviará el header <code class="font-mono bg-gray-100 dark:bg-gray-600 px-1 rounded">Authorization: Basic &lt;base64(usuario:contraseña)&gt;</code> al backend.
                                    </p>
                                </div>

                                {{-- Sin tipo seleccionado --}}
                                <div x-show="!auth_type" class="py-2 text-center text-xs text-gray-400 dark:text-gray-500">
                                    Selecciona el tipo de autenticación arriba.
                                </div>

                            </div>
                        </div>
                    </div>

                    {{-- Responsable (persona search) --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Responsable / Solicitante</label>
                        <div class="relative" id="requester-search-wrap-exp">
                            <div class="relative">
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                <input type="text" id="persona-search-exp" autocomplete="off"
                                       placeholder="Buscar por DNI o nombre…"
                                       class="w-full pl-9 pr-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600
                                              bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm
                                              focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                            </div>
                            <div id="persona-dropdown-exp"
                                 class="hidden absolute z-50 w-full mt-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg shadow-xl max-h-48 overflow-y-auto text-sm"></div>
                            <div id="persona-selected-exp"
                                 class="hidden mt-2 items-center gap-2 px-3 py-2 rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700/40">
                                <svg class="w-4 h-4 text-blue-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                <span id="persona-selected-name-exp" class="flex-1 text-sm font-medium text-blue-700 dark:text-blue-300 truncate"></span>
                                <button type="button" id="persona-clear-exp" class="text-blue-400 hover:text-red-500 transition-colors">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                            <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">Escribe al menos 4 caracteres para buscar</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ══════════════════════════════════════════════════════════
                 PASO 3 (CONSUMIDO) — Proveedor
            ══════════════════════════════════════════════════════════ --}}
            <div x-show="currentStep === 3 && direction === 'consumed'" x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-x-4"
                 x-transition:enter-end="opacity-100 translate-x-0">

                <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Proveedor del servicio</h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">¿Quién ofrece el servicio que tu sistema va a consumir?</p>
                </div>

                <div class="p-6 space-y-5">
                    {{-- Tipo de proveedor (cards) --}}
                    <div class="grid grid-cols-3 gap-3">
                        <button type="button" @click="provider_type = ''"
                                class="flex flex-col items-center gap-2 p-3 rounded-xl border-2 transition-all"
                                :class="provider_type === ''
                                    ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20'
                                    : 'border-gray-200 dark:border-gray-700 hover:border-blue-200 dark:hover:border-blue-700'">
                            <svg class="w-6 h-6" :class="provider_type === '' ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                            </svg>
                            <span class="text-xs font-medium" :class="provider_type === '' ? 'text-blue-700 dark:text-blue-300' : 'text-gray-600 dark:text-gray-400'">Sin proveedor</span>
                        </button>
                        <button type="button" @click="provider_type = 'internal'"
                                class="flex flex-col items-center gap-2 p-3 rounded-xl border-2 transition-all"
                                :class="provider_type === 'internal'
                                    ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20'
                                    : 'border-gray-200 dark:border-gray-700 hover:border-blue-200 dark:hover:border-blue-700'">
                            <svg class="w-6 h-6" :class="provider_type === 'internal' ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            <span class="text-xs font-medium" :class="provider_type === 'internal' ? 'text-blue-700 dark:text-blue-300' : 'text-gray-600 dark:text-gray-400'">Sistema interno</span>
                        </button>
                        <button type="button" @click="provider_type = 'external'"
                                class="flex flex-col items-center gap-2 p-3 rounded-xl border-2 transition-all"
                                :class="provider_type === 'external'
                                    ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20'
                                    : 'border-gray-200 dark:border-gray-700 hover:border-blue-200 dark:hover:border-blue-700'">
                            <svg class="w-6 h-6" :class="provider_type === 'external' ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/>
                            </svg>
                            <span class="text-xs font-medium" :class="provider_type === 'external' ? 'text-blue-700 dark:text-blue-300' : 'text-gray-600 dark:text-gray-400'">Entidad externa</span>
                        </button>
                    </div>

                    {{-- Sistema interno --}}
                    <div x-show="provider_type === 'internal'">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Sistema proveedor</label>
                        <select x-model="provider_system_id"
                                class="block w-full rounded-lg border border-gray-300 dark:border-gray-600
                                       bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                       px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                            <option value="">— Seleccionar sistema —</option>
                            @foreach($allSystems as $s)
                            <option value="{{ $s->id }}">{{ $s->name }}{{ $s->acronym ? ' (' . $s->acronym . ')' : '' }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Entidad externa --}}
                    <div x-show="provider_type === 'external'">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Nombre del proveedor</label>
                        <input type="text" x-model="provider_name" placeholder="RENIEC, SUNAT, Ministerio de Educación…"
                               class="block w-full rounded-lg border border-gray-300 dark:border-gray-600
                                      bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                      px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>

                    {{-- URL del servicio --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">URL / Endpoint del servicio</label>
                        <input type="url" x-model="endpoint_url" placeholder="https://api.reniec.gob.pe/consulta/dni"
                               class="block w-full rounded-lg border border-gray-300 dark:border-gray-600
                                      bg-white dark:bg-gray-700 text-gray-900 dark:text-white font-mono
                                      px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>

                    {{-- Vigencia --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Vigencia desde</label>
                            <input type="date" x-model="valid_from"
                                   class="block w-full rounded-lg border border-gray-300 dark:border-gray-600
                                          bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                          px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Vigencia hasta</label>
                            <input type="date" x-model="valid_until"
                                   class="block w-full rounded-lg border border-gray-300 dark:border-gray-600
                                          bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                          px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                        </div>
                    </div>

                    {{-- Responsable (persona search) --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Responsable / Solicitante</label>
                        <div class="relative" id="requester-search-wrap-con">
                            <div class="relative">
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                <input type="text" id="persona-search-con" autocomplete="off"
                                       placeholder="Buscar por DNI o nombre…"
                                       class="w-full pl-9 pr-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600
                                              bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm
                                              focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                            </div>
                            <div id="persona-dropdown-con"
                                 class="hidden absolute z-50 w-full mt-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg shadow-xl max-h-48 overflow-y-auto text-sm"></div>
                            <div id="persona-selected-con"
                                 class="hidden mt-2 items-center gap-2 px-3 py-2 rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700/40">
                                <svg class="w-4 h-4 text-blue-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                <span id="persona-selected-name-con" class="flex-1 text-sm font-medium text-blue-700 dark:text-blue-300 truncate"></span>
                                <button type="button" id="persona-clear-con" class="text-blue-400 hover:text-red-500 transition-colors">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                            <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">Escribe al menos 4 caracteres para buscar</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ══════════════════════════════════════════════════════════
                 PASO 4 (EXPUESTO) — Gateway
            ══════════════════════════════════════════════════════════ --}}
            <div x-show="currentStep === 4 && direction === 'exposed'" x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-x-4"
                 x-transition:enter-end="opacity-100 translate-x-0">

                <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Configuración del Gateway</h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Define cómo se expone la API a los consumidores externos.</p>
                </div>

                <div class="p-6 space-y-5">
                    {{-- Toggle gateway --}}
                    <div class="flex items-center justify-between p-4 rounded-xl border-2 transition-all"
                         :class="gateway_enabled ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-gray-700'">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center"
                                 :class="gateway_enabled ? 'bg-blue-100 dark:bg-blue-900/40' : 'bg-gray-100 dark:bg-gray-700'">
                                <svg class="w-5 h-5" :class="gateway_enabled ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-sm text-gray-900 dark:text-white">Activar Gateway</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Genera una URL pública de proxy para este servicio</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" x-model="gateway_enabled">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:ring-2 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer peer-checked:bg-blue-600
                                        after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all
                                        peer-checked:after:translate-x-full"></div>
                        </label>
                    </div>

                    {{-- Preview URL --}}
                    <div x-show="gateway_enabled && service_name" x-transition>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5 uppercase tracking-wider">URL del Gateway (previsualización)</label>
                        <div class="flex items-center gap-2 px-3 py-2.5 rounded-lg bg-gray-900 dark:bg-gray-950 border border-gray-700">
                            <svg class="w-4 h-4 text-green-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            <code class="flex-1 text-xs text-green-300 font-mono truncate"
                                  x-text="'{{ url('/api/gw/') }}/' + slugPreview() + '/...'"></code>
                        </div>
                        <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">El slug final se generará al guardar.</p>
                    </div>

                    {{-- Require API Key --}}
                    <div class="flex items-center justify-between p-3.5 rounded-lg bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-700">
                        <div>
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Requerir API Key</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Los consumidores deben enviar una clave en el header <code class="font-mono text-xs">X-API-Key</code></p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" x-model="gateway_require_key">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:ring-2 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer peer-checked:bg-blue-600
                                        after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all
                                        peer-checked:after:translate-x-full"></div>
                        </label>
                    </div>

                    {{-- Límites de tasa --}}
                    <div>
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Límites de consultas</p>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">Máx. por minuto</label>
                                <div class="relative">
                                    <input type="number" x-model="gateway_rate_per_minute" min="1" max="32767" placeholder="Sin límite"
                                           class="block w-full rounded-lg border border-gray-300 dark:border-gray-600
                                                  bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                                  px-3.5 py-2.5 pr-16 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400">req/min</span>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">Máx. por día</label>
                                <div class="relative">
                                    <input type="number" x-model="gateway_rate_per_day" min="1" max="32767" placeholder="Sin límite"
                                           class="block w-full rounded-lg border border-gray-300 dark:border-gray-600
                                                  bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                                  px-3.5 py-2.5 pr-16 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400">req/día</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Horario de operación --}}
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Horario de operación</p>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" class="sr-only peer" x-model="hasSchedule">
                                <div class="w-9 h-5 bg-gray-200 peer-focus:ring-2 peer-focus:ring-blue-300 rounded-full peer peer-checked:bg-blue-600
                                            after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all
                                            peer-checked:after:translate-x-full"></div>
                            </label>
                        </div>
                        <div x-show="hasSchedule" x-transition class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">Desde</label>
                                <input type="time" x-model="gateway_active_from"
                                       class="block w-full rounded-lg border border-gray-300 dark:border-gray-600
                                              bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                              px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">Hasta</label>
                                <input type="time" x-model="gateway_active_to"
                                       class="block w-full rounded-lg border border-gray-300 dark:border-gray-600
                                              bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                              px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                            </div>
                        </div>
                        <p x-show="hasSchedule && gateway_active_from && gateway_active_to" x-transition
                           class="mt-1.5 text-xs text-blue-600 dark:text-blue-400"
                           x-text="'El gateway solo responderá entre ' + gateway_active_from + ' y ' + gateway_active_to + ' (soporta cruce de medianoche).'"></p>
                        <p x-show="!hasSchedule" class="text-xs text-gray-400 dark:text-gray-500">Sin restricción de horario — disponible las 24 h.</p>
                    </div>

                    {{-- ── Vista previa de acceso ── --}}
                    <div class="rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <div class="px-4 py-3 bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700 flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Cómo consumirán esta API</p>
                        </div>

                        {{-- Sin gateway activo --}}
                        <div x-show="!gateway_enabled" class="p-4 space-y-3">
                            <div class="flex items-start gap-2.5 p-3 rounded-lg bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700/40">
                                <svg class="w-4 h-4 text-yellow-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.962-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                </svg>
                                <p class="text-xs text-yellow-700 dark:text-yellow-300">
                                    Sin gateway activo la API real quedará accesible directamente. Activa el gateway para controlar el acceso y ocultar la URL del backend.
                                </p>
                            </div>
                            <div x-show="endpoint_url">
                                <p class="text-xs text-gray-400 dark:text-gray-500 mb-1.5">Acceso directo (sin protección):</p>
                                <div class="bg-gray-900 dark:bg-gray-950 rounded-lg p-3 overflow-x-auto">
                                    <code class="text-xs text-yellow-300 font-mono whitespace-pre"
                                          x-text="'curl ' + endpoint_url"></code>
                                </div>
                            </div>
                        </div>

                        {{-- Con gateway, sin API key --}}
                        <div x-show="gateway_enabled && !gateway_require_key" class="p-4 space-y-3">
                            <div class="flex items-start gap-2.5 p-3 rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700/40">
                                <svg class="w-4 h-4 text-blue-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064"/>
                                </svg>
                                <p class="text-xs text-blue-700 dark:text-blue-300">
                                    Gateway activo sin autenticación. Cualquiera con la URL puede consultar la API. La URL real del backend queda oculta.
                                </p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mb-1.5">Ejemplo de consumo (acceso libre):</p>
                                <div class="bg-gray-900 dark:bg-gray-950 rounded-lg p-3 overflow-x-auto">
                                    <code class="text-xs font-mono whitespace-pre">
<span class="text-gray-400"># Llamada sin autenticación</span>
<span class="text-green-300">curl</span> <span class="text-yellow-200" x-text="'{{ url('/api/gw/') }}/' + slugPreview() + '/...'"></span>
                                    </code>
                                </div>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mb-1.5">Ejemplo con fetch (JavaScript):</p>
                                <div class="bg-gray-900 dark:bg-gray-950 rounded-lg p-3 overflow-x-auto">
                                    <code class="text-xs font-mono whitespace-pre">
<span class="text-blue-300">fetch</span>(<span class="text-yellow-200">`<span x-text="'{{ url('/api/gw/') }}/' + slugPreview() + '/...'"></span>`</span>)
  .<span class="text-blue-300">then</span>(r <span class="text-pink-300">=></span> r.<span class="text-blue-300">json</span>())
  .<span class="text-blue-300">then</span>(data <span class="text-pink-300">=></span> <span class="text-blue-300">console</span>.<span class="text-blue-300">log</span>(data));
                                    </code>
                                </div>
                            </div>
                        </div>

                        {{-- Con gateway + API key requerida --}}
                        <div x-show="gateway_enabled && gateway_require_key" class="p-4 space-y-3">
                            <div class="flex items-start gap-2.5 p-3 rounded-lg bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-700/40">
                                <svg class="w-4 h-4 text-emerald-600 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                                <p class="text-xs text-emerald-700 dark:text-emerald-300">
                                    <strong>API protegida con clave.</strong> Solo consumidores con una API Key válida podrán acceder. Podrás generar y gestionar las claves desde la vista del servicio.
                                </p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mb-1.5">Ejemplo de consumo con clave (curl):</p>
                                <div class="bg-gray-900 dark:bg-gray-950 rounded-lg p-3 overflow-x-auto">
                                    <code class="text-xs font-mono whitespace-pre">
<span class="text-gray-400"># La API Key se envía en el header X-API-Key</span>
<span class="text-green-300">curl</span> \
  <span class="text-cyan-300">-H</span> <span class="text-yellow-200">"X-API-Key: sk_xxxxxxxxxxxxxxxxxxxxxxxx"</span> \
  <span class="text-yellow-200" x-text="'{{ url('/api/gw/') }}/' + slugPreview() + '/...'"></span>
                                    </code>
                                </div>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mb-1.5">Ejemplo con fetch (JavaScript):</p>
                                <div class="bg-gray-900 dark:bg-gray-950 rounded-lg p-3 overflow-x-auto">
                                    <code class="text-xs font-mono whitespace-pre">
<span class="text-blue-300">fetch</span>(<span class="text-yellow-200">`<span x-text="'{{ url('/api/gw/') }}/' + slugPreview() + '/...'"></span>`</span>, {
  <span class="text-cyan-300">headers</span>: {
    <span class="text-yellow-200">'X-API-Key'</span>: <span class="text-yellow-200">'sk_xxxxxxxxxxxxxxxxxxxxxxxx'</span>
  }
}).<span class="text-blue-300">then</span>(r <span class="text-pink-300">=></span> r.<span class="text-blue-300">json</span>())
  .<span class="text-blue-300">then</span>(data <span class="text-pink-300">=></span> <span class="text-blue-300">console</span>.<span class="text-blue-300">log</span>(data));
                                    </code>
                                </div>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mb-1.5">Respuesta si la clave es inválida o falta:</p>
                                <div class="bg-gray-900 dark:bg-gray-950 rounded-lg p-3 overflow-x-auto">
                                    <code class="text-xs font-mono whitespace-pre">
<span class="text-gray-400">HTTP/1.1 <span class="text-red-400">401 Unauthorized</span></span>
{
  <span class="text-cyan-300">"error"</span>: <span class="text-yellow-200">"API key inválida o no proporcionada"</span>
}
                                    </code>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- ══════════════════════════════════════════════════════════
                 PASO 4 (CONSUMIDO) — Credenciales
            ══════════════════════════════════════════════════════════ --}}
            <div x-show="currentStep === 4 && direction === 'consumed'" x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-x-4"
                 x-transition:enter-end="opacity-100 translate-x-0">

                <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Credenciales de acceso</h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Almacenadas de forma encriptada. Todos los campos son opcionales.</p>
                </div>

                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Tipo de autenticación</label>
                        <input type="text" x-model="auth_type" placeholder="API Key, OAuth 2.0, Basic Auth, JWT…"
                               class="block w-full rounded-lg border border-gray-300 dark:border-gray-600
                                      bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                      px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">API Key</label>
                            <input type="password" x-model="api_key" autocomplete="new-password"
                                   class="block w-full rounded-lg border border-gray-300 dark:border-gray-600
                                          bg-white dark:bg-gray-700 text-gray-900 dark:text-white font-mono
                                          px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">API Secret / Client Secret</label>
                            <input type="password" x-model="api_secret" autocomplete="new-password"
                                   class="block w-full rounded-lg border border-gray-300 dark:border-gray-600
                                          bg-white dark:bg-gray-700 text-gray-900 dark:text-white font-mono
                                          px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Token / Bearer</label>
                            <input type="password" x-model="token" autocomplete="new-password"
                                   class="block w-full rounded-lg border border-gray-300 dark:border-gray-600
                                          bg-white dark:bg-gray-700 text-gray-900 dark:text-white font-mono
                                          px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Vencimiento del token</label>
                            <input type="date" x-model="token_expires_at"
                                   class="block w-full rounded-lg border border-gray-300 dark:border-gray-600
                                          bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                          px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                        </div>
                    </div>
                </div>
            </div>

            {{-- ══════════════════════════════════════════════════════════
                 PASO 5 — Resumen y confirmar
            ══════════════════════════════════════════════════════════ --}}
            <div x-show="currentStep === 5" x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-x-4"
                 x-transition:enter-end="opacity-100 translate-x-0">

                <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Confirmar registro</h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Revisa los datos antes de guardar.</p>
                </div>

                <div class="p-6 space-y-4">
                    <template x-for="section in summaryData()" :key="section.title">
                        <div class="rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                            <div class="px-4 py-2.5 bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700">
                                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider" x-text="section.title"></p>
                            </div>
                            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                                <template x-for="row in section.rows" :key="row.label">
                                    <div class="flex items-start px-4 py-2.5 gap-3">
                                        <span class="w-40 text-xs text-gray-500 dark:text-gray-400 flex-shrink-0 pt-0.5" x-text="row.label"></span>
                                        <span class="text-sm text-gray-900 dark:text-white font-medium flex-1 break-all"
                                              :class="row.mono ? 'font-mono text-xs' : ''"
                                              x-text="row.value || '—'"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>

                    {{-- Aviso de seguridad si tiene credenciales --}}
                    <div x-show="api_key || api_secret || token"
                         class="flex items-start gap-3 p-3.5 rounded-lg bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700/40">
                        <svg class="w-4 h-4 text-amber-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.962-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                        <p class="text-xs text-amber-700 dark:text-amber-300">Las credenciales se almacenan encriptadas. Solo se muestran los campos rellenados como indicador.</p>
                    </div>
                </div>
            </div>

            {{-- ── Navegación ── --}}
            <div class="flex items-center justify-between px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                <div>
                    <button type="button" @click="prevStep()"
                            x-show="currentStep > 1"
                            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300
                                   bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg
                                   hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Atrás
                    </button>
                    <a href="{{ route('systems.show', $system) }}" x-show="currentStep === 1"
                       class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300
                              bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg
                              hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                        Cancelar
                    </a>
                </div>

                <div class="flex items-center gap-3">
                    {{-- Botón siguiente (pasos 2, 3, 4) --}}
                    <button type="button" @click="nextStep()"
                            x-show="currentStep > 1 && currentStep < 5"
                            :disabled="!canProceed()"
                            class="inline-flex items-center gap-2 px-5 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg
                                   hover:bg-blue-700 transition-colors shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">
                        Siguiente
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>

                    {{-- Botón guardar (solo paso 5) --}}
                    <button type="submit"
                            x-show="currentStep === 5"
                            class="inline-flex items-center gap-2 px-6 py-2 text-sm font-medium text-white bg-green-600 rounded-lg
                                   hover:bg-green-700 transition-colors shadow-sm">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Registrar Servicio
                    </button>
                </div>
            </div>

        </div>{{-- /card --}}
    </form>

</div>{{-- /x-data --}}

@push('scripts')
<script>
function serviceWizard() {
    return {
        currentStep: 1,

        // Campos del formulario
        direction: '',
        service_name: '',
        service_type: '',
        environment: 'production',
        version: '',
        description: '',
        is_active: true,
        endpoint_url: '',
        valid_from: '',
        valid_until: '',
        requested_by_persona_id: '',
        requester_name: '',

        // Proveedor
        provider_type: '',
        provider_system_id: '',
        provider_name: '',

        // Credenciales
        auth_type: '',
        api_key: '',
        api_secret: '',
        token: '',
        token_expires_at: '',

        // Gateway
        gateway_enabled: false,
        gateway_require_key: false,
        gateway_rate_per_minute: '',
        gateway_rate_per_day: '',
        gateway_active_from: '',
        gateway_active_to: '',
        hasSchedule: false,
        backendHasAuth: false,

        // Opciones
        serviceTypes: [
            { value: 'rest_api',  label: 'REST API' },
            { value: 'soap',      label: 'SOAP' },
            { value: 'sftp',      label: 'SFTP' },
            { value: 'smtp',      label: 'SMTP' },
            { value: 'ldap',      label: 'LDAP' },
            { value: 'database',  label: 'Base de Datos' },
            { value: 'other',     label: 'Otro' },
        ],
        environments: [
            { value: 'production',  label: 'Producción' },
            { value: 'staging',     label: 'Staging' },
            { value: 'development', label: 'Desarrollo' },
        ],

        init() {
            // Escuchar selección de persona (expuesto)
            document.addEventListener('requester-selected-exp', e => {
                this.requested_by_persona_id = e.detail.id;
                this.requester_name = e.detail.name;
            });
            // Escuchar selección de persona (consumido)
            document.addEventListener('requester-selected-con', e => {
                this.requested_by_persona_id = e.detail.id;
                this.requester_name = e.detail.name;
            });

            // Limpiar horario si se desactiva
            this.$watch('hasSchedule', val => {
                if (!val) {
                    this.gateway_active_from = '';
                    this.gateway_active_to   = '';
                }
            });

            // Limpiar credenciales de backend si se colapsa
            this.$watch('backendHasAuth', val => {
                if (!val) {
                    this.auth_type  = '';
                    this.api_key    = '';
                    this.token      = '';
                }
            });

            // Inicializar persona autocomplete para ambos formularios
            this.$nextTick(() => {
                initPersonaSearch('exp', id => {
                    this.requested_by_persona_id = id;
                });
                initPersonaSearch('con', id => {
                    this.requested_by_persona_id = id;
                });
            });
        },

        totalSteps() {
            return 5;
        },

        stepLabel() {
            const labels = {
                1: 'Dirección',
                2: 'Información básica',
                3: this.direction === 'exposed' ? 'Backend real' : 'Proveedor',
                4: this.direction === 'exposed' ? 'Gateway' : 'Credenciales',
                5: 'Resumen',
            };
            return labels[this.currentStep] || '';
        },

        canProceed() {
            if (this.currentStep === 2) {
                return this.service_name.trim().length > 0 && this.service_type !== '';
            }
            if (this.currentStep === 3 && this.direction === 'exposed') {
                return this.endpoint_url.trim().length > 0;
            }
            return true;
        },

        nextStep() {
            if (this.currentStep < this.totalSteps()) {
                this.currentStep++;
            }
        },

        prevStep() {
            if (this.currentStep > 1) {
                this.currentStep--;
            }
        },

        slugPreview() {
            if (!this.service_name) return 'nombre-del-servicio-xxxxxxxx';
            return this.service_name
                .toLowerCase()
                .replace(/[^a-z0-9\s-]/g, '')
                .trim()
                .replace(/\s+/g, '-')
                .substring(0, 30) + '-xxxxxxxx';
        },

        typeLabel(val) {
            const t = this.serviceTypes.find(x => x.value === val);
            return t ? t.label : val;
        },

        envLabel(val) {
            const e = this.environments.find(x => x.value === val);
            return e ? e.label : val;
        },

        summaryData() {
            const sections = [];

            // Sección 1: Básico
            sections.push({
                title: 'Información básica',
                rows: [
                    { label: 'Dirección',    value: this.direction === 'exposed' ? 'Expuesto (ofrece servicio)' : 'Consumido (usa servicio)' },
                    { label: 'Nombre',       value: this.service_name },
                    { label: 'Tipo',         value: this.typeLabel(this.service_type) },
                    { label: 'Ambiente',     value: this.envLabel(this.environment) },
                    { label: 'Versión',      value: this.version },
                    { label: 'Descripción',  value: this.description },
                    { label: 'Estado',       value: this.is_active ? 'Activo' : 'Inactivo' },
                ],
            });

            if (this.direction === 'exposed') {
                // Sección 2: Backend
                const backendRows = [
                    { label: 'URL del backend', value: this.endpoint_url, mono: true },
                    { label: 'Vigencia desde',  value: this.valid_from },
                    { label: 'Vigencia hasta',  value: this.valid_until },
                    { label: 'Responsable',     value: this.requester_name },
                ];
                if (this.backendHasAuth) {
                    backendRows.push({ label: 'Auth del backend', value: this.auth_type || 'Configurada' });
                }
                sections.push({ title: 'Backend real', rows: backendRows });

                // Sección 3: Gateway
                sections.push({
                    title: 'Gateway',
                    rows: [
                        { label: 'Gateway',          value: this.gateway_enabled ? 'Activado' : 'Desactivado' },
                        { label: 'Requiere API Key', value: this.gateway_require_key ? 'Sí' : 'No' },
                        { label: 'Límite por minuto', value: this.gateway_rate_per_minute ? this.gateway_rate_per_minute + ' req/min' : 'Sin límite' },
                        { label: 'Límite por día',   value: this.gateway_rate_per_day ? this.gateway_rate_per_day + ' req/día' : 'Sin límite' },
                        { label: 'Horario',          value: this.hasSchedule && this.gateway_active_from
                            ? this.gateway_active_from + ' – ' + this.gateway_active_to
                            : 'Sin restricción (24 h)' },
                    ],
                });
            } else {
                // Sección 2: Proveedor
                let provName = 'Sin proveedor';
                if (this.provider_type === 'internal') provName = 'Sistema interno';
                if (this.provider_type === 'external') provName = this.provider_name || 'Entidad externa';
                sections.push({
                    title: 'Proveedor',
                    rows: [
                        { label: 'Tipo',          value: provName },
                        { label: 'URL / Endpoint', value: this.endpoint_url, mono: true },
                        { label: 'Vigencia desde', value: this.valid_from },
                        { label: 'Vigencia hasta', value: this.valid_until },
                        { label: 'Responsable',    value: this.requester_name },
                    ],
                });

                // Sección 3: Credenciales
                sections.push({
                    title: 'Credenciales',
                    rows: [
                        { label: 'Tipo de auth',  value: this.auth_type },
                        { label: 'API Key',        value: this.api_key       ? '••••••••' : '' },
                        { label: 'API Secret',     value: this.api_secret    ? '••••••••' : '' },
                        { label: 'Token',          value: this.token         ? '••••••••' : '' },
                        { label: 'Venc. token',    value: this.token_expires_at },
                    ],
                });
            }

            return sections;
        },
    };
}

// ── Persona autocomplete helper ───────────────────────────────────────────────
const personaSearchUrl = "{{ route('admin.personas.search') }}";

function initPersonaSearch(suffix, onSelect) {
    const wrap     = document.getElementById('requester-search-wrap-' + suffix);
    const input    = document.getElementById('persona-search-' + suffix);
    const dropdown = document.getElementById('persona-dropdown-' + suffix);
    const selBox   = document.getElementById('persona-selected-' + suffix);
    const selName  = document.getElementById('persona-selected-name-' + suffix);
    const clearBtn = document.getElementById('persona-clear-' + suffix);

    if (!input) return;

    let timer;

    input.addEventListener('input', function () {
        const q = this.value.trim();
        clearTimeout(timer);
        dropdown.classList.add('hidden');
        dropdown.innerHTML = '';
        if (q.length < 4) return;

        dropdown.innerHTML = '<p class="px-4 py-3 text-xs text-gray-400">Buscando...</p>';
        dropdown.classList.remove('hidden');

        timer = setTimeout(async () => {
            try {
                const res  = await fetch(personaSearchUrl + '?q=' + encodeURIComponent(q));
                const data = await res.json();
                dropdown.innerHTML = '';
                if (!data.length) {
                    dropdown.innerHTML = '<p class="px-4 py-3 text-xs text-gray-400">Sin resultados</p>';
                    return;
                }
                data.forEach(p => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'w-full text-left px-4 py-2.5 text-sm text-gray-800 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors';
                    btn.innerHTML = `${p.apellido_paterno} ${p.apellido_materno ?? ''}, ${p.nombres} &mdash; <span class="font-mono">${p.dni}</span>`;
                    btn.addEventListener('click', () => {
                        const name = `${p.apellido_paterno} ${p.apellido_materno ?? ''}, ${p.nombres} (${p.dni})`;
                        onSelect(p.id);
                        // Also update display
                        const alpineRoot = document.querySelector('[x-data]');
                        if (alpineRoot && alpineRoot._x_dataStack) {
                            // Alpine 3: update via $data
                        }
                        selName.textContent = name;
                        input.value = '';
                        dropdown.classList.add('hidden');
                        selBox.classList.remove('hidden');
                        selBox.classList.add('flex');
                        // Update requester_name in Alpine
                        document.dispatchEvent(new CustomEvent('requester-selected-' + suffix, {
                            detail: { id: p.id, name }
                        }));
                    });
                    dropdown.appendChild(btn);
                });
            } catch {
                dropdown.innerHTML = '<p class="px-4 py-3 text-xs text-red-400">Error al buscar</p>';
            }
        }, 300);
    });

    if (clearBtn) {
        clearBtn.addEventListener('click', () => {
            onSelect('');
            document.dispatchEvent(new CustomEvent('requester-selected-' + suffix, { detail: { id: '', name: '' } }));
            selName.textContent = '';
            selBox.classList.add('hidden');
            selBox.classList.remove('flex');
            input.value = '';
        });
    }

    document.addEventListener('click', e => {
        if (wrap && !wrap.contains(e.target)) dropdown.classList.add('hidden');
    });
}
</script>
@endpush
@endsection
