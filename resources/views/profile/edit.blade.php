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

    {{-- Eliminar cuenta --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-red-200 dark:border-red-900/50 shadow-sm overflow-hidden">
        <div class="p-6">
            @include('profile.partials.delete-user-form')
        </div>
    </div>

</div>
@endsection
