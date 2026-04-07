<x-guest-layout>

    <x-auth-session-status class="mb-6" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        {{-- Email --}}
        <div>
            <label for="email" class="block text-sm font-medium text-slate-700 mb-1.5">
                Correo Electrónico
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                    <svg class="h-4.5 w-4.5 text-slate-400" xmlns="http://www.w3.org/2000/svg"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                              d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8
                                 M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
                <input id="email" name="email" type="email" autocomplete="email" required
                       value="{{ old('email') }}"
                       placeholder="correo@unamad.edu.pe"
                       class="input-focus w-full pl-10 pr-4 py-2.5 text-sm text-slate-800 rounded-xl
                              placeholder-slate-400 focus:outline-none focus:bg-white transition-all duration-200
                              {{ $errors->has('email')
                                  ? 'border border-red-400 bg-red-50 focus:border-red-400'
                                  : 'border border-slate-200 bg-slate-50 focus:border-blue-400' }}">
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        {{-- Contraseña --}}
        <div>
            <label for="password" class="block text-sm font-medium text-slate-700 mb-1.5">
                Contraseña
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                    <svg class="h-4.5 w-4.5 text-slate-400" xmlns="http://www.w3.org/2000/svg"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                              d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z
                                 m10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <input id="password" name="password" type="password"
                       autocomplete="current-password" required
                       placeholder="••••••••••"
                       class="input-focus w-full pl-10 pr-4 py-2.5 text-sm text-slate-800 rounded-xl
                              placeholder-slate-400 focus:outline-none focus:bg-white transition-all duration-200
                              {{ $errors->has('password')
                                  ? 'border border-red-400 bg-red-50 focus:border-red-400'
                                  : 'border border-slate-200 bg-slate-50 focus:border-blue-400' }}">
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
        </div>

        {{-- Recuérdame + Olvidé contraseña --}}
        <div class="flex items-center justify-between pt-1">
            <label class="flex items-center gap-2 cursor-pointer group">
                <input id="remember_me" name="remember" type="checkbox"
                       class="w-4 h-4 text-blue-600 border-slate-300 rounded
                              focus:ring-blue-500 focus:ring-offset-0 cursor-pointer">
                <span class="text-sm text-slate-600 group-hover:text-slate-800 transition-colors">
                    Recordar sesión
                </span>
            </label>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}"
                   class="text-sm text-blue-600 hover:text-blue-800 font-medium transition-colors">
                    ¿Olvidaste tu contraseña?
                </a>
            @endif
        </div>

        {{-- Botón --}}
        <div class="pt-2">
            <button type="submit"
                    class="btn-login w-full flex items-center justify-center gap-2
                           py-3 px-4 rounded-xl text-sm font-semibold text-white
                           shadow-lg shadow-blue-500/20 focus:outline-none
                           focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M11 16l-4-4m0 0l4-4m-4 4h14
                             m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                </svg>
                Ingresar al Sistema
            </button>
        </div>

    </form>

</x-guest-layout>
