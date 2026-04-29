<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name') }} - Iniciar Sesión</title>

    <link rel="icon" type="image/png" href="{{ asset('images/sistema/logo.png') }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Inter', sans-serif; }

        .login-bg {
            background-image: url('{{ asset('images/sistema/FONDO.png') }}');
            background-size: cover;
            background-position: center;
        }

        .login-overlay {
            background: linear-gradient(135deg,
                rgba(2,6,23,0.82) 0%,
                rgba(15,23,42,0.75) 40%,
                rgba(23,37,84,0.70) 100%);
        }

        .glow-dot {
            animation: pulse-glow 3s ease-in-out infinite;
        }

        @keyframes pulse-glow {
            0%, 100% { opacity: 0.35; transform: scale(1); }
            50%       { opacity: 1;    transform: scale(1.2); }
        }

        .float-logo {
            animation: float-logo 5s ease-in-out infinite;
        }

        @keyframes float-logo {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50%       { transform: translateY(-10px) rotate(1deg); }
        }

        .input-field {
            transition: border-color .2s, box-shadow .2s, background-color .2s;
        }
        .input-field:focus {
            box-shadow: 0 0 0 3px rgba(59,130,246,0.18);
            outline: none;
        }
        .input-field.has-error:focus {
            box-shadow: 0 0 0 3px rgba(239,68,68,0.15);
        }

        .btn-login {
            background: linear-gradient(135deg, #1e40af 0%, #2563eb 50%, #3b82f6 100%);
            transition: all 0.25s ease;
        }
        .btn-login:hover:not(:disabled) {
            background: linear-gradient(135deg, #1e3a8a 0%, #1d4ed8 50%, #2563eb 100%);
            transform: translateY(-1px);
            box-shadow: 0 8px 28px rgba(37,99,235,0.45);
        }
        .btn-login:active:not(:disabled) { transform: translateY(0); }
        .btn-login:disabled { opacity: .65; cursor: not-allowed; }

        @keyframes shake {
            0%,100% { transform: translateX(0); }
            20%      { transform: translateX(-6px); }
            40%      { transform: translateX(6px); }
            60%      { transform: translateX(-4px); }
            80%      { transform: translateX(4px); }
        }
        .shake { animation: shake .45s ease-in-out; }

        @keyframes slide-down {
            from { opacity: 0; transform: translateY(-8px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .slide-down { animation: slide-down .25s ease-out forwards; }
    </style>
</head>
<body class="antialiased bg-slate-950">

    <div class="min-h-screen flex">

        {{-- ── Panel izquierdo – Branding + Imagen ── --}}
        <div class="hidden lg:flex lg:w-1/2 xl:w-3/5 relative overflow-hidden login-bg">

            {{-- Overlay oscuro --}}
            <div class="absolute inset-0 login-overlay"></div>

            {{-- Puntos decorativos animados --}}
            <div class="absolute top-16 left-20 w-2 h-2 rounded-full bg-blue-400 glow-dot" style="animation-delay:.4s"></div>
            <div class="absolute top-36 right-44 w-1.5 h-1.5 rounded-full bg-cyan-300 glow-dot" style="animation-delay:1s"></div>
            <div class="absolute bottom-44 left-36 w-2 h-2 rounded-full bg-blue-300 glow-dot" style="animation-delay:1.6s"></div>
            <div class="absolute bottom-20 right-28 w-1 h-1 rounded-full bg-cyan-400 glow-dot" style="animation-delay:2.2s"></div>
            <div class="absolute top-1/3 right-24 w-1.5 h-1.5 rounded-full bg-blue-400 glow-dot" style="animation-delay:.7s"></div>

            {{-- Contenido central --}}
            <div class="relative z-10 flex flex-col items-center justify-center w-full px-16 text-center">

                {{-- Logo con efecto flotante --}}
                <div class="float-logo mb-8">
                    <div class="relative">
                        <div class="absolute inset-0 rounded-3xl bg-blue-500/40 blur-2xl scale-110"></div>
                        <div class="relative bg-white/10 border border-white/20 rounded-3xl p-5 backdrop-blur-sm shadow-2xl">
                            <img src="{{ asset('images/sistema/logo.png') }}"
                                 alt="Logo {{ config('app.name') }}"
                                 class="w-24 h-24 object-contain drop-shadow-lg">
                        </div>
                    </div>
                </div>

                {{-- Badge institución --}}
                <div class="mb-4">
                    <span class="inline-block px-4 py-1.5 rounded-full text-xs font-semibold tracking-widest uppercase
                                 bg-blue-500/20 border border-blue-400/30 text-blue-300 backdrop-blur-sm">
                        OTI · UNAMAD
                    </span>
                </div>

                <h1 class="text-5xl xl:text-6xl font-extrabold tracking-tight text-white mb-3 drop-shadow-lg">
                    {{ config('app.name') }}
                </h1>

                <p class="text-blue-200/80 text-base xl:text-lg font-light leading-relaxed max-w-xs xl:max-w-sm">
                    Sistema Integral de Gestión de Infraestructura y Seguridad
                </p>

                {{-- Divisor --}}
                <div class="flex items-center gap-3 my-8">
                    <div class="h-px w-20 bg-gradient-to-r from-transparent to-blue-500/50"></div>
                    <div class="w-1.5 h-1.5 rounded-full bg-blue-400"></div>
                    <div class="h-px w-20 bg-gradient-to-l from-transparent to-blue-500/50"></div>
                </div>

                {{-- Features --}}
                <div class="grid grid-cols-3 gap-6 w-full max-w-sm">
                    @foreach([
                        ['icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'label' => 'Seguro'],
                        ['icon' => 'M13 10V3L4 14h7v7l9-11h-7z',                                                                                                                                                                                                                                          'label' => 'Eficiente'],
                        ['icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',                                                           'label' => 'Control'],
                    ] as $item)
                    <div class="flex flex-col items-center gap-2">
                        <div class="w-10 h-10 rounded-xl bg-white/10 border border-white/15 flex items-center justify-center backdrop-blur-sm">
                            <svg class="w-5 h-5 text-blue-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $item['icon'] }}" />
                            </svg>
                        </div>
                        <span class="text-xs text-blue-300/70 font-medium">{{ $item['label'] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Pie del panel izquierdo --}}
            <div class="absolute bottom-5 left-0 right-0 text-center">
                <p class="text-xs text-blue-400/40 tracking-widest uppercase">
                    Oficina de Tecnologías de Información
                </p>
            </div>
        </div>

        {{-- ── Panel derecho – Formulario ── --}}
        <div class="flex-1 flex flex-col items-center justify-center
                    bg-white px-6 sm:px-12 lg:px-16 xl:px-20 relative">

            {{-- Decoraciones de fondo --}}
            <div class="absolute top-0 right-0 w-48 h-48 bg-blue-50 rounded-bl-full opacity-50 pointer-events-none"></div>
            <div class="absolute bottom-0 left-0 w-32 h-32 bg-slate-50 rounded-tr-full opacity-70 pointer-events-none"></div>

            <div class="relative z-10 w-full max-w-md">

                {{-- Logo + nombre – solo móvil --}}
                <div class="lg:hidden flex items-center gap-3 mb-10">
                    <img src="{{ asset('images/sistema/logo.png') }}"
                         alt="Logo {{ config('app.name') }}"
                         class="w-10 h-10 object-contain">
                    <div>
                        <p class="font-bold text-slate-800 text-lg leading-none">{{ config('app.name') }}</p>
                        <p class="text-xs text-slate-400 mt-0.5">OTI · UNAMAD</p>
                    </div>
                </div>

                {{-- Encabezado --}}
                <div class="mb-8">
                    <h2 class="text-3xl font-bold text-slate-900 tracking-tight">Bienvenido</h2>
                    <p class="mt-2 text-slate-500 text-sm">Ingresa tus credenciales para acceder al sistema</p>
                </div>

                {{-- Slot del formulario --}}
                {{ $slot }}

                {{-- Footer --}}
                <p class="mt-10 text-center text-xs text-slate-400">
                    &copy; {{ date('Y') }} {{ config('app.name') }} &mdash; Oficina de Tecnologías de Información · UNAMAD
                </p>
            </div>
        </div>

    </div>

</body>
</html>
