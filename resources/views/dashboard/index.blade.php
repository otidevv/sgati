@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    {{-- Page Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Dashboard</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Resumen general del sistema de inventario</p>
        </div>
        <div class="text-sm text-gray-500 dark:text-gray-400">
            Última actualización: {{ now()->format('d/m/Y H:i') }}
        </div>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-5">
        {{-- Total Sistemas --}}
        <x-stat-card
            :value="$stats['total']"
            title="Total Sistemas"
            color="blue"
            :href="route('systems.index')">
            <x-slot name="icon">
                <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </x-slot>
        </x-stat-card>

        {{-- Sistemas Activos --}}
        <x-stat-card
            :value="$stats['active']"
            title="Sistemas Activos"
            color="green"
            :href="route('systems.index') . '?status=active'">
            <x-slot name="icon">
                <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </x-slot>
            <x-slot name="subtitle">En producción</x-slot>
        </x-stat-card>

        {{-- En Desarrollo --}}
        <x-stat-card
            :value="$stats['development']"
            title="En Desarrollo"
            color="indigo"
            :href="route('systems.index') . '?status=development'">
            <x-slot name="icon">
                <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                </svg>
            </x-slot>
            <x-slot name="subtitle">En construcción</x-slot>
        </x-stat-card>

        {{-- En Mantenimiento --}}
        <x-stat-card
            :value="$stats['maintenance']"
            title="En Mantenimiento"
            color="yellow"
            :href="route('systems.index') . '?status=maintenance'">
            <x-slot name="icon">
                <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </x-slot>
            <x-slot name="subtitle">Mantenimiento programado</x-slot>
        </x-stat-card>

        {{-- Sistemas Inactivos --}}
        <x-stat-card
            :value="$stats['inactive']"
            title="Inactivos"
            color="red"
            :href="route('systems.index') . '?status=inactive'">
            <x-slot name="icon">
                <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                </svg>
            </x-slot>
            <x-slot name="subtitle">Fuera de servicio</x-slot>
        </x-stat-card>
    </div>

    {{-- Main Content Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Recent Systems --}}
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
            <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Sistemas Recientes</h3>
                    <a href="{{ route('systems.index') }}" class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-500 dark:hover:text-blue-300">
                        Ver todos →
                    </a>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nombre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Área</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actualizado</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($recentSystems as $system)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded bg-gradient-to-r from-blue-500 to-indigo-600 flex items-center justify-center text-white font-semibold text-sm">
                                            {{ strtoupper(substr($system->acronym ?? $system->name, 0, 2)) }}
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $system->name }}</div>
                                        @if($system->acronym)
                                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $system->acronym }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-white">{{ $system->area->name ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <x-status-badge :status="$system->status" />
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $system->updated_at->diffForHumans() }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                <p class="mt-2 text-sm">No hay sistemas registrados</p>
                                @can('systems.create')
                                    <a href="{{ route('systems.create') }}" class="mt-2 inline-block text-blue-600 dark:text-blue-400 hover:text-blue-500 dark:hover:text-blue-300 text-sm font-medium">
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

        {{-- SSL Alerts --}}
        <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
            <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Alertas SSL</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Certificados próximos a vencer (30 días)</p>
            </div>
            <div class="p-6">
                @forelse($sslExpiring as $infra)
                    <div class="mb-4 p-4 bg-yellow-50 dark:bg-yellow-900/30 border-l-4 border-yellow-400 dark:border-yellow-600 rounded">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="ml-3 flex-1">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $infra->system->name }}</p>
                                <p class="text-xs text-gray-600 dark:text-gray-300 mt-1">
                                    Vence: <span class="font-semibold">{{ $infra->ssl_expiry->format('d/m/Y') }}</span>
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    ({{ $infra->ssl_expiry->diffForHumans() }})
                                </p>
                                @if($infra->ssl_issuer)
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        Emisor: {{ $infra->ssl_issuer }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-green-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No hay alertas SSL pendientes</p>
                        <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">Todos los certificados están vigentes</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
