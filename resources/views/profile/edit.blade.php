@extends('layouts.app')
@section('title', 'Mi Perfil')

@section('content')
@php
    $initials = collect(explode(' ', $user->name))->map(fn($w) => strtoupper(substr($w,0,1)))->take(2)->implode('');
@endphp

<div class="max-w-4xl mx-auto space-y-6">

    {{-- ══════════ HERO CARD ══════════ --}}
    <div class="relative bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">

        {{-- Banda decorativa superior --}}
        <div class="h-24 bg-gradient-to-r from-blue-600 via-blue-500 to-indigo-600"></div>

        <div class="px-6 pb-6">
            {{-- Avatar --}}
            <div class="flex items-end justify-between -mt-12 mb-4">
                <div class="w-20 h-20 rounded-2xl bg-white dark:bg-gray-700 shadow-lg border-4 border-white dark:border-gray-700
                            flex items-center justify-center text-blue-600 dark:text-blue-400 text-2xl font-bold select-none">
                    {{ $initials }}
                </div>
                <div class="flex items-center gap-2 mt-14">
                    <button onclick="openModal('modal-cuenta')"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold
                                   text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700
                                   hover:bg-gray-200 dark:hover:bg-gray-600 rounded-xl transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                        </svg>
                        Editar cuenta
                    </button>
                    <button onclick="openModal('modal-password')"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold
                                   text-white bg-blue-600 hover:bg-blue-700 rounded-xl transition-colors shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                        </svg>
                        Cambiar contraseña
                    </button>
                </div>
            </div>

            {{-- Nombre y rol --}}
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ $user->nombre_completo ?? $user->name }}</h1>
                    @if($user->role)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold
                                 bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300">
                        {{ $user->role->label ?? $user->role->name }}
                    </span>
                    @endif
                </div>
                <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
            </div>

            {{-- Grid de datos --}}
            <div class="mt-5 grid grid-cols-2 sm:grid-cols-4 gap-4">
                @if($user->persona?->dni)
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-3.5">
                    <p class="text-[11px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">DNI</p>
                    <p class="mt-1 text-sm font-semibold text-gray-800 dark:text-gray-200 font-mono">{{ $user->persona->dni }}</p>
                </div>
                @endif
                @if($user->area)
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-3.5 sm:col-span-2">
                    <p class="text-[11px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Área</p>
                    <p class="mt-1 text-sm font-semibold text-gray-800 dark:text-gray-200 truncate">
                        {{ $user->area->name }}{{ $user->area->acronym ? " ({$user->area->acronym})" : '' }}
                    </p>
                </div>
                @endif
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-3.5">
                    <p class="text-[11px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Usuario</p>
                    <p class="mt-1 text-sm font-semibold text-gray-800 dark:text-gray-200 font-mono truncate">{{ $user->name }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════ HISTORIAL DE ACCESOS ══════════ --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">

        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center">
                    <svg class="w-4 h-4 text-indigo-500 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-sm font-semibold text-gray-800 dark:text-gray-200">Historial de Accesos</h2>
                    <p class="text-[11px] text-gray-400 dark:text-gray-500">Últimas 15 sesiones registradas</p>
                </div>
            </div>
            @if($loginLogs->where('logged_out_at', null)->count())
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium
                         bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400">
                <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                Sesión activa
            </span>
            @endif
        </div>

        @if($loginLogs->isEmpty())
            <div class="flex flex-col items-center justify-center py-14 text-center">
                <div class="w-14 h-14 rounded-2xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center mb-3">
                    <svg class="w-7 h-7 text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Sin registros de acceso</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Los accesos se registrarán a partir de ahora</p>
            </div>
        @else
            {{-- Cabecera tabla --}}
            <div class="hidden sm:grid grid-cols-[90px_1fr_80px_110px_120px_110px] gap-4 px-6 py-2.5
                        bg-gray-50 dark:bg-gray-700/40 text-[11px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">
                <span>Estado</span>
                <span>Fecha y hora</span>
                <span>Duración</span>
                <span>Dirección IP</span>
                <span>Sistema</span>
                <span>Navegador</span>
            </div>

            <div class="divide-y divide-gray-100 dark:divide-gray-700/50">
                @foreach($loginLogs as $log)
                @php $isActive = is_null($log->logged_out_at); @endphp
                <div class="grid grid-cols-1 sm:grid-cols-[90px_1fr_80px_110px_120px_110px] gap-2 sm:gap-4 items-center
                            px-6 py-3.5 transition-colors
                            {{ $isActive ? 'bg-green-50/40 dark:bg-green-900/5' : 'hover:bg-gray-50/60 dark:hover:bg-gray-700/20' }}">

                    {{-- Estado --}}
                    <div>
                        @if($isActive)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold
                                         bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-400">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                                Activa
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium
                                         bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400">
                                Cerrada
                            </span>
                        @endif
                    </div>

                    {{-- Fecha --}}
                    <div>
                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200">
                            {{ $log->logged_in_at->format('d/m/Y · H:i') }}
                        </p>
                        <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-0.5">
                            {{ $log->logged_in_at->diffForHumans() }}
                        </p>
                    </div>

                    {{-- Duración --}}
                    <div>
                        @if($isActive)
                            <p class="text-xs font-semibold text-green-600 dark:text-green-400">En curso</p>
                        @else
                            <p class="text-xs font-semibold text-gray-700 dark:text-gray-300">{{ $log->duration }}</p>
                        @endif
                    </div>

                    {{-- IP --}}
                    <div class="flex items-center gap-1.5">
                        <svg class="w-3 h-3 text-gray-300 dark:text-gray-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9"/>
                        </svg>
                        <span class="text-xs font-mono text-gray-600 dark:text-gray-400">{{ $log->ip_address ?? '—' }}</span>
                    </div>

                    {{-- SO --}}
                    <div>
                        <span class="text-xs text-gray-600 dark:text-gray-400">{{ $log->os }}</span>
                    </div>

                    {{-- Navegador --}}
                    <div>
                        <span class="text-xs text-gray-600 dark:text-gray-400 truncate">{{ $log->browser }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>

</div>


{{-- ══════════ MODAL: Editar cuenta ══════════ --}}
<div id="modal-cuenta" class="hidden fixed inset-0 z-50 items-center justify-center p-4" role="dialog" aria-modal="true">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeModal('modal-cuenta')"></div>
    <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">

        {{-- Header modal --}}
        <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-100 dark:border-gray-700">
            <div class="w-9 h-9 rounded-xl bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0">
                <svg class="w-4.5 h-4.5 text-blue-600 dark:text-blue-400 w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Editar información de cuenta</h3>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Actualiza tu nombre de usuario y correo</p>
            </div>
            <button onclick="closeModal('modal-cuenta')"
                    class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400
                           hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form id="send-verification" method="post" action="{{ route('verification.send') }}">@csrf</form>

        <form method="post" action="{{ route('profile.update') }}">
            @csrf
            @method('patch')
            <div class="p-6 space-y-4">

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Nombre de usuario <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="name" name="name"
                           value="{{ old('name', $user->name) }}"
                           required autofocus autocomplete="name"
                           class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white
                                  focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    @if($errors->get('name'))
                        <p class="mt-1.5 text-xs text-red-500">{{ $errors->first('name') }}</p>
                    @endif
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Correo electrónico <span class="text-red-500">*</span>
                    </label>
                    <input type="email" id="email" name="email"
                           value="{{ old('email', $user->email) }}"
                           required autocomplete="username"
                           class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white
                                  focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    @if($errors->get('email'))
                        <p class="mt-1.5 text-xs text-red-500">{{ $errors->first('email') }}</p>
                    @endif

                    @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <div class="mt-2 flex items-start gap-2 p-3 rounded-lg bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700/40">
                        <svg class="w-4 h-4 text-amber-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                        </svg>
                        <div class="flex-1">
                            <p class="text-xs text-amber-700 dark:text-amber-300">Correo no verificado.
                                <button form="send-verification" class="underline font-medium hover:text-amber-900 dark:hover:text-amber-100">
                                    Reenviar verificación
                                </button>
                            </p>
                            @if (session('status') === 'verification-link-sent')
                            <p class="mt-1 text-xs font-medium text-green-600 dark:text-green-400">¡Enlace enviado!</p>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>

            </div>
            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                <button type="button" onclick="closeModal('modal-cuenta')"
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700
                               border border-gray-300 dark:border-gray-600 rounded-xl hover:bg-gray-50 transition-colors">
                    Cancelar
                </button>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2 text-sm font-semibold text-white
                               bg-blue-600 hover:bg-blue-700 rounded-xl transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Guardar cambios
                </button>
            </div>
        </form>
    </div>
</div>


{{-- ══════════ MODAL: Cambiar contraseña ══════════ --}}
<div id="modal-password" class="hidden fixed inset-0 z-50 items-center justify-center p-4" role="dialog" aria-modal="true">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeModal('modal-password')"></div>
    <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">

        <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-100 dark:border-gray-700">
            <div class="w-9 h-9 rounded-xl bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Cambiar contraseña</h3>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Usa una contraseña larga y segura</p>
            </div>
            <button onclick="closeModal('modal-password')"
                    class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400
                           hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form method="post" action="{{ route('password.update') }}">
            @csrf
            @method('put')
            <div class="p-6 space-y-4">

                <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Contraseña actual <span class="text-red-500">*</span>
                    </label>
                    <input type="password" id="current_password" name="current_password"
                           autocomplete="current-password"
                           class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white
                                  focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    @if($errors->updatePassword->get('current_password'))
                        <p class="mt-1.5 text-xs text-red-500">{{ $errors->updatePassword->first('current_password') }}</p>
                    @endif
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Nueva contraseña <span class="text-red-500">*</span>
                    </label>
                    <input type="password" id="password" name="password"
                           autocomplete="new-password"
                           class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white
                                  focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    @if($errors->updatePassword->get('password'))
                        <p class="mt-1.5 text-xs text-red-500">{{ $errors->updatePassword->first('password') }}</p>
                    @endif
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Confirmar nueva contraseña <span class="text-red-500">*</span>
                    </label>
                    <input type="password" id="password_confirmation" name="password_confirmation"
                           autocomplete="new-password"
                           class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white
                                  focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    @if($errors->updatePassword->get('password_confirmation'))
                        <p class="mt-1.5 text-xs text-red-500">{{ $errors->updatePassword->first('password_confirmation') }}</p>
                    @endif
                </div>

            </div>
            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                <button type="button" onclick="closeModal('modal-password')"
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700
                               border border-gray-300 dark:border-gray-600 rounded-xl hover:bg-gray-50 transition-colors">
                    Cancelar
                </button>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2 text-sm font-semibold text-white
                               bg-blue-600 hover:bg-blue-700 rounded-xl transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Actualizar contraseña
                </button>
            </div>
        </form>
    </div>
</div>


{{-- Notificaciones de éxito --}}
@if(session('status') === 'profile-updated')
<div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)"
     class="fixed bottom-6 right-6 z-[80] flex items-center gap-3 px-5 py-3.5 rounded-2xl
            bg-green-600 text-white shadow-lg text-sm font-medium">
    <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    Perfil actualizado correctamente.
</div>
@endif

@if(session('status') === 'password-updated')
<div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)"
     class="fixed bottom-6 right-6 z-[80] flex items-center gap-3 px-5 py-3.5 rounded-2xl
            bg-green-600 text-white shadow-lg text-sm font-medium">
    <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    Contraseña actualizada correctamente.
</div>
@endif


@push('scripts')
<script>
(function () {
    function openModal(id) {
        const m = document.getElementById(id);
        m.classList.remove('hidden');
        m.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }
    function closeModal(id) {
        const m = document.getElementById(id);
        m.classList.add('hidden');
        m.classList.remove('flex');
        document.body.style.overflow = '';
    }
    window.openModal  = openModal;
    window.closeModal = closeModal;

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            ['modal-cuenta', 'modal-password'].forEach(id => {
                if (!document.getElementById(id).classList.contains('hidden')) closeModal(id);
            });
        }
    });

    // Abrir automáticamente el modal si hay errores de validación
    @if($errors->any() && !$errors->updatePassword->any())
        openModal('modal-cuenta');
    @endif
    @if($errors->updatePassword->any())
        openModal('modal-password');
    @endif
})();
</script>
@endpush

@endsection
