<x-guest-layout>

    {{-- Encabezado --}}
    <div class="mb-8">
        <div class="flex items-center gap-2 mb-4">
            <a href="{{ route('login') }}"
               class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-slate-700 transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver al inicio de sesión
            </a>
        </div>
        <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Recuperar contraseña</h2>
        <p class="mt-2 text-sm text-slate-500">
            Ingresa tu correo institucional y te enviaremos un enlace para restablecer tu contraseña.
        </p>
    </div>

    {{-- Mensaje de éxito --}}
    @if(session('status'))
    <div class="mb-6 flex items-start gap-3 px-4 py-4
                bg-emerald-50 border border-emerald-200 rounded-xl slide-down">
        <svg class="w-5 h-5 text-emerald-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div>
            <p class="text-sm font-semibold text-emerald-800">Correo enviado</p>
            <p class="mt-0.5 text-sm text-emerald-700">{{ session('status') }}</p>
        </div>
    </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}"
          x-data="{ submitting: false }"
          @submit="submitting = true"
          class="space-y-5">
        @csrf

        {{-- Email --}}
        <div>
            <label for="email" class="block text-sm font-medium text-slate-700 mb-1.5">
                Correo Electrónico
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                    <svg class="h-4 w-4 {{ $errors->has('email') ? 'text-red-400' : 'text-slate-400' }}"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                              d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <input id="email" name="email" type="email"
                       autocomplete="email" required autofocus
                       value="{{ old('email') }}"
                       placeholder="correo@unamad.edu.pe"
                       class="input-field w-full pl-10 pr-4 py-2.5 text-sm text-slate-800 rounded-xl
                              placeholder-slate-400 transition-all duration-200
                              {{ $errors->has('email')
                                  ? 'has-error border border-red-400 bg-red-50 focus:border-red-400'
                                  : 'border border-slate-200 bg-slate-50 focus:border-blue-400 focus:bg-white' }}">
            </div>
            @error('email')
            <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                <svg class="w-3 h-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                {{ $message }}
            </p>
            @enderror
        </div>

        {{-- Botón --}}
        <button type="submit" :disabled="submitting"
                class="btn-login w-full flex items-center justify-center gap-2
                       py-3 px-4 rounded-xl text-sm font-semibold text-white
                       shadow-lg shadow-blue-500/20 focus:outline-none
                       focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
            <svg x-show="!submitting" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            <svg x-show="submitting" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24" style="display:none">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
            </svg>
            <span x-text="submitting ? 'Enviando enlace…' : 'Enviar enlace de recuperación'">
                Enviar enlace de recuperación
            </span>
        </button>

    </form>

</x-guest-layout>
