<x-guest-layout>

    {{-- Banner de error general (credenciales inválidas) --}}
    @if($errors->any())
    <div class="slide-down shake mb-6 flex items-start gap-3 px-4 py-3.5
                bg-red-50 border border-red-200 rounded-xl text-red-700">
        <svg class="w-5 h-5 flex-shrink-0 mt-0.5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
        </svg>
        <div class="text-sm">
            <p class="font-semibold">No se pudo iniciar sesión</p>
            @foreach($errors->all() as $error)
            <p class="mt-0.5 text-red-600">{{ $error }}</p>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Mensaje de estado (ej: email de recuperación enviado) --}}
    @if(session('status'))
    <div class="slide-down mb-6 flex items-center gap-3 px-4 py-3.5
                bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-700 text-sm">
        <svg class="w-5 h-5 flex-shrink-0 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        {{ session('status') }}
    </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-5" id="login-form"
          x-data="{ submitting: false }"
          @submit="submitting = true">
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
                <input id="email" name="email" type="email" autocomplete="email" required
                       value="{{ old('email') }}"
                       placeholder="correo@unamad.edu.pe"
                       class="input-field w-full pl-10 pr-10 py-2.5 text-sm text-slate-800 rounded-xl
                              placeholder-slate-400 transition-all duration-200
                              {{ $errors->has('email')
                                  ? 'has-error border border-red-400 bg-red-50 focus:border-red-400'
                                  : 'border border-slate-200 bg-slate-50 focus:border-blue-400 focus:bg-white' }}">
                @if($errors->has('email'))
                <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </div>
                @endif
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

        {{-- Contraseña --}}
        <div x-data="{ show: false }">
            <label for="password" class="block text-sm font-medium text-slate-700 mb-1.5">
                Contraseña
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                    <svg class="h-4 w-4 {{ $errors->has('password') ? 'text-red-400' : 'text-slate-400' }}"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                              d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <input id="password" name="password" :type="show ? 'text' : 'password'"
                       autocomplete="current-password" required
                       placeholder="••••••••••"
                       class="input-field w-full pl-10 pr-10 py-2.5 text-sm text-slate-800 rounded-xl
                              placeholder-slate-400 transition-all duration-200
                              {{ $errors->has('password')
                                  ? 'has-error border border-red-400 bg-red-50 focus:border-red-400'
                                  : 'border border-slate-200 bg-slate-50 focus:border-blue-400 focus:bg-white' }}">
                {{-- Toggle mostrar/ocultar --}}
                <button type="button" @click="show = !show"
                        class="absolute inset-y-0 right-3 flex items-center text-slate-400 hover:text-slate-600 transition-colors">
                    <svg x-show="!show" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <svg x-show="show" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display:none">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                              d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                    </svg>
                </button>
            </div>
            @error('password')
            <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                <svg class="w-3 h-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                {{ $message }}
            </p>
            @enderror
        </div>

        {{-- Recuérdame + Olvidé contraseña --}}
        <div class="flex items-center justify-between pt-1">
            <label class="flex items-center gap-2 cursor-pointer group select-none">
                <input id="remember_me" name="remember" type="checkbox"
                       class="w-4 h-4 text-blue-600 border-slate-300 rounded
                              focus:ring-blue-500 focus:ring-offset-0 cursor-pointer">
                <span class="text-sm text-slate-600 group-hover:text-slate-800 transition-colors">
                    Recordar sesión
                </span>
            </label>

            @if(Route::has('password.request'))
            <a href="{{ route('password.request') }}"
               class="text-sm text-blue-600 hover:text-blue-800 font-medium transition-colors">
                ¿Olvidaste tu contraseña?
            </a>
            @endif
        </div>

        {{-- Botón --}}
        <div class="pt-2">
            <button type="submit" :disabled="submitting"
                    class="btn-login w-full flex items-center justify-center gap-2
                           py-3 px-4 rounded-xl text-sm font-semibold text-white
                           shadow-lg shadow-blue-500/20 focus:outline-none
                           focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                <svg x-show="!submitting" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                </svg>
                <svg x-show="submitting" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24" style="display:none">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                </svg>
                <span x-text="submitting ? 'Verificando…' : 'Ingresar al Sistema'">Ingresar al Sistema</span>
            </button>
        </div>

    </form>

</x-guest-layout>
