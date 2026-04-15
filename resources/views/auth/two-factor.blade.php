<x-guest-layout>

    {{-- Mensajes --}}
    @if(session('resent'))
        <div class="mb-5 flex items-center gap-2.5 px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-200 text-sm text-emerald-700">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Se envió un nuevo código a tu correo.
        </div>
    @endif

    {{-- Icono y título --}}
    <div class="text-center mb-6">
        <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-blue-50 mb-4">
            <svg class="w-7 h-7 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                      d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
        </div>
        <h2 class="text-lg font-bold text-slate-800">Verificación en 2 pasos</h2>
        <p class="mt-1 text-sm text-slate-500">
            Ingresa el código de 6 dígitos enviado a tu correo.<br>
            <span class="text-xs text-slate-400">El código expira en 10 minutos.</span>
        </p>
    </div>

    <form method="POST" action="{{ route('two-factor.store') }}" class="space-y-5">
        @csrf

        {{-- Input del código --}}
        <div>
            <label for="code" class="block text-sm font-medium text-slate-700 mb-1.5">
                Código de verificación
            </label>
            <input id="code" name="code" type="text"
                   inputmode="numeric" pattern="\d{6}" maxlength="6"
                   autocomplete="one-time-code" autofocus required
                   placeholder="000000"
                   class="w-full text-center text-2xl font-bold tracking-[0.5em] py-3 px-4 rounded-xl
                          border focus:outline-none focus:bg-white transition-all duration-200
                          {{ $errors->has('code')
                              ? 'border-red-400 bg-red-50 focus:border-red-400'
                              : 'border-slate-200 bg-slate-50 focus:border-blue-400' }}">
            <x-input-error :messages="$errors->get('code')" class="mt-1.5" />
        </div>

        {{-- Botón verificar --}}
        <div class="pt-1">
            <button type="submit"
                    class="btn-login w-full flex items-center justify-center gap-2
                           py-3 px-4 rounded-xl text-sm font-semibold text-white
                           shadow-lg shadow-blue-500/20 focus:outline-none
                           focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Verificar y acceder
            </button>
        </div>
    </form>

    {{-- Reenviar código --}}
    <div class="mt-5 text-center">
        <p class="text-xs text-slate-500 mb-2">¿No recibiste el código?</p>
        <form method="POST" action="{{ route('two-factor.resend') }}" class="inline">
            @csrf
            <button type="submit"
                    class="text-sm text-blue-600 hover:text-blue-800 font-medium transition-colors underline-offset-2 hover:underline">
                Reenviar código
            </button>
        </form>
    </div>

    {{-- Volver al login --}}
    <div class="mt-3 text-center">
        <a href="{{ route('login') }}"
           class="text-xs text-slate-400 hover:text-slate-600 transition-colors">
            ← Volver al inicio de sesión
        </a>
    </div>

</x-guest-layout>
