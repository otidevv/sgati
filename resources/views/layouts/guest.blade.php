<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SGATI') }} - Iniciar Sesión</title>

    <link rel="icon" type="image/png" href="{{ asset('images/sistema/logo.png') }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Inter', sans-serif; }

        .tech-grid {
            background-image:
                linear-gradient(rgba(99,179,237,0.07) 1px, transparent 1px),
                linear-gradient(90deg, rgba(99,179,237,0.07) 1px, transparent 1px);
            background-size: 40px 40px;
        }

        .glow-dot {
            animation: pulse-glow 3s ease-in-out infinite;
        }

        @keyframes pulse-glow {
            0%, 100% { opacity: 0.4; transform: scale(1); }
            50%       { opacity: 1;   transform: scale(1.15); }
        }

        .float {
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50%       { transform: translateY(-12px); }
        }

        .input-focus:focus {
            box-shadow: 0 0 0 3px rgba(59,130,246,0.15);
        }

        .btn-login {
            background: linear-gradient(135deg, #1e40af 0%, #2563eb 50%, #3b82f6 100%);
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            background: linear-gradient(135deg, #1e3a8a 0%, #1d4ed8 50%, #2563eb 100%);
            transform: translateY(-1px);
            box-shadow: 0 8px 25px rgba(37,99,235,0.4);
        }

        .btn-login:active {
            transform: translateY(0);
        }
    </style>
</head>
<body class="antialiased bg-slate-950">

    <div class="min-h-screen flex">

        {{-- ── Panel izquierdo – Branding ── --}}
        <div class="hidden lg:flex lg:w-1/2 xl:w-3/5 relative overflow-hidden
                    bg-gradient-to-br from-slate-950 via-blue-950 to-slate-900 tech-grid">

            {{-- Orbes decorativos --}}
            <div class="absolute -top-32 -left-32 w-96 h-96 rounded-full
                        bg-blue-600/20 blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-32 -right-32 w-96 h-96 rounded-full
                        bg-cyan-500/10 blur-3xl pointer-events-none"></div>
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-72 h-72 rounded-full
                        bg-blue-700/10 blur-2xl pointer-events-none"></div>

            {{-- Puntos de la cuadrícula animados --}}
            <div class="absolute top-16 left-20 w-2 h-2 rounded-full bg-blue-400 glow-dot" style="animation-delay:.5s"></div>
            <div class="absolute top-32 right-40 w-1.5 h-1.5 rounded-full bg-cyan-400 glow-dot" style="animation-delay:1s"></div>
            <div class="absolute bottom-40 left-32 w-2 h-2 rounded-full bg-blue-300 glow-dot" style="animation-delay:1.5s"></div>
            <div class="absolute bottom-24 right-24 w-1 h-1 rounded-full bg-cyan-300 glow-dot" style="animation-delay:2s"></div>
            <div class="absolute top-1/3 right-20 w-1.5 h-1.5 rounded-full bg-blue-400 glow-dot" style="animation-delay:.8s"></div>

            {{-- Contenido central --}}
            <div class="relative z-10 flex flex-col items-center justify-center w-full px-16 text-center">

                {{-- Ícono flotante --}}
                <div class="float mb-10">
                    <div class="relative">
                        <div class="absolute inset-0 rounded-2xl bg-blue-500/30 blur-xl"></div>
                        <div class="relative bg-gradient-to-br from-blue-500/20 to-cyan-500/10
                                    border border-blue-400/30 rounded-2xl p-7 backdrop-blur-sm">
                            <svg class="w-20 h-20 text-blue-300" xmlns="http://www.w3.org/2000/svg"
                                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2
                                         M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2
                                         m-2-4h.01M17 16h.01" />
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Nombre del sistema --}}
                <div class="mb-3">
                    <span class="inline-block px-4 py-1 rounded-full text-xs font-semibold tracking-widest uppercase
                                 bg-blue-500/15 border border-blue-500/30 text-blue-300">
                        OTI · UNAMAD
                    </span>
                </div>

                <h1 class="text-6xl font-bold tracking-tight text-white mb-4">
                    SGATI
                </h1>

                <p class="text-blue-200/80 text-lg font-light leading-relaxed max-w-sm">
                    Sistema de Gestión y Administración de Tecnologías de Información
                </p>

                {{-- Línea divisora decorativa --}}
                <div class="flex items-center gap-3 my-8">
                    <div class="h-px w-16 bg-gradient-to-r from-transparent to-blue-500/50"></div>
                    <div class="w-1.5 h-1.5 rounded-full bg-blue-400"></div>
                    <div class="h-px w-16 bg-gradient-to-l from-transparent to-blue-500/50"></div>
                </div>

                {{-- Estadísticas / features --}}
                <div class="grid grid-cols-3 gap-6 w-full max-w-sm">
                    @foreach([
                        ['icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'label' => 'Seguro'],
                        ['icon' => 'M13 10V3L4 14h7v7l9-11h-7z', 'label' => 'Eficiente'],
                        ['icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'label' => 'Control'],
                    ] as $item)
                    <div class="flex flex-col items-center gap-2">
                        <div class="w-10 h-10 rounded-xl bg-blue-500/10 border border-blue-500/20
                                    flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $item['icon'] }}" />
                            </svg>
                        </div>
                        <span class="text-xs text-blue-300/70 font-medium">{{ $item['label'] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Pie del panel --}}
            <div class="absolute bottom-6 left-0 right-0 text-center">
                <p class="text-xs text-blue-400/40 tracking-widest uppercase">
                    Oficina de Tecnologías de Información
                </p>
            </div>
        </div>

        {{-- ── Panel derecho – Formulario ── --}}
        <div class="flex-1 flex flex-col items-center justify-center
                    bg-white px-6 sm:px-12 lg:px-16 xl:px-20 relative">

            {{-- Decoración top-right --}}
            <div class="absolute top-0 right-0 w-40 h-40 bg-blue-50 rounded-bl-full opacity-60 pointer-events-none"></div>
            <div class="absolute bottom-0 left-0 w-28 h-28 bg-slate-50 rounded-tr-full opacity-80 pointer-events-none"></div>

            <div class="relative z-10 w-full max-w-md">

                {{-- Logo mobile --}}
                <div class="lg:hidden flex items-center gap-3 mb-10">
                    <div class="bg-blue-600 rounded-xl p-2">
                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2
                                     M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-bold text-slate-800 text-lg leading-none">SGATI</p>
                        <p class="text-xs text-slate-400">OTI · UNAMAD</p>
                    </div>
                </div>

                {{-- Encabezado --}}
                <div class="mb-10">
                    <h2 class="text-3xl font-bold text-slate-900 tracking-tight">
                        Bienvenido
                    </h2>
                    <p class="mt-2 text-slate-500 text-sm">
                        Ingresa tus credenciales para acceder al sistema
                    </p>
                </div>

                {{-- Slot del formulario --}}
                {{ $slot }}

                {{-- Footer --}}
                <p class="mt-10 text-center text-xs text-slate-400">
                    &copy; {{ date('Y') }} SGATI &mdash; Oficina de Tecnologías de Información · UNAMAD
                </p>
            </div>
        </div>

    </div>

</body>
</html>
