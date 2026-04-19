@extends('layouts.app')

@section('title', 'Mi Perfil')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex items-center gap-4">
        <div class="flex items-center justify-center w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 text-lg font-bold shrink-0">
            {{ strtoupper(substr($user->name, 0, 1)) }}
        </div>
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Mi Perfil</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ $user->nombre_completo }}
                @if($user->role)
                    &mdash;
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300">
                        {{ $user->role->label ?? $user->role->name }}
                    </span>
                @endif
            </p>
        </div>
    </div>

    {{-- Datos personales vinculados --}}
    @if($user->persona)
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm p-6">
        <h2 class="text-sm font-semibold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2 mb-4">Datos Personales</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <div>
                <p class="text-gray-500 dark:text-gray-400">Nombre completo</p>
                <p class="font-medium text-gray-900 dark:text-white mt-0.5">{{ $user->persona->nombre_completo }}</p>
            </div>
            <div>
                <p class="text-gray-500 dark:text-gray-400">DNI</p>
                <p class="font-medium text-gray-900 dark:text-white mt-0.5">{{ $user->persona->dni }}</p>
            </div>
            @if($user->area)
            <div>
                <p class="text-gray-500 dark:text-gray-400">Área</p>
                <p class="font-medium text-gray-900 dark:text-white mt-0.5">
                    {{ $user->area->name }}{{ $user->area->acronym ? " ({$user->area->acronym})" : '' }}
                </p>
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- Actualizar información de cuenta --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
        <div class="p-6">
            @include('profile.partials.update-profile-information-form')
        </div>
    </div>

    {{-- Cambiar contraseña --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
        <div class="p-6">
            @include('profile.partials.update-password-form')
        </div>
    </div>

    {{-- Auditoría de accesos --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center gap-2">
            <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
            <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Historial de Accesos</h2>
            <span class="ml-auto text-xs text-gray-400 dark:text-gray-500">Últimas 15 sesiones</span>
        </div>

        @if($loginLogs->isEmpty())
            <div class="px-6 py-10 text-center text-sm text-gray-400 dark:text-gray-500">
                Sin registros de acceso todavía.
            </div>
        @else
            <div class="divide-y divide-gray-100 dark:divide-gray-700/60">
                @foreach($loginLogs as $log)
                @php $isActive = is_null($log->logged_out_at); @endphp
                <div class="px-6 py-3.5 flex flex-wrap items-center gap-x-5 gap-y-2 text-sm {{ $loop->first ? 'bg-blue-50/50 dark:bg-blue-900/10' : '' }}">

                    {{-- Estado --}}
                    @if($isActive)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 flex-shrink-0">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                            Activa
                        </span>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 flex-shrink-0">
                            Cerrada
                        </span>
                    @endif

                    {{-- Fecha --}}
                    <div class="min-w-[130px]">
                        <p class="text-xs font-medium text-gray-700 dark:text-gray-300">{{ $log->logged_in_at->format('d/m/Y H:i') }}</p>
                        <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-0.5">{{ $log->logged_in_at->diffForHumans() }}</p>
                    </div>

                    {{-- Duración --}}
                    <div>
                        <p class="text-[11px] text-gray-400 dark:text-gray-500 uppercase tracking-wide">Duración</p>
                        <p class="text-xs font-medium text-gray-700 dark:text-gray-300 mt-0.5">
                            {{ $isActive ? 'En curso' : $log->duration }}
                        </p>
                    </div>

                    {{-- IP --}}
                    <div>
                        <p class="text-[11px] text-gray-400 dark:text-gray-500 uppercase tracking-wide">IP</p>
                        <p class="text-xs font-mono text-gray-700 dark:text-gray-300 mt-0.5">{{ $log->ip_address ?? '—' }}</p>
                    </div>

                    {{-- SO --}}
                    <div>
                        <p class="text-[11px] text-gray-400 dark:text-gray-500 uppercase tracking-wide">Sistema</p>
                        <p class="text-xs text-gray-700 dark:text-gray-300 mt-0.5">{{ $log->os }}</p>
                    </div>

                    {{-- Navegador --}}
                    <div class="flex-1 min-w-0">
                        <p class="text-[11px] text-gray-400 dark:text-gray-500 uppercase tracking-wide">Navegador</p>
                        <p class="text-xs text-gray-700 dark:text-gray-300 mt-0.5 truncate">{{ $log->browser }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>

</div>
@endsection
