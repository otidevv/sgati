<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SGATI') }} - Iniciar Sesión</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-gray-900 antialiased">
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-800 via-slate-900 to-slate-800 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-6">
            {{-- Logo and Title --}}
            <div class="text-center">
                <div class="flex justify-center">
                    <div class="bg-white rounded-full p-4 shadow-lg">
                        <svg class="w-16 h-16 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01" />
                        </svg>
                    </div>
                </div>
                <h2 class="mt-6 text-3xl font-extrabold text-white">
                    SGATI
                </h2>
                <p class="mt-2 text-sm text-slate-300">
                    Sistema de Gestión y Administración de Tecnologías de Información
                </p>
                <p class="mt-1 text-xs text-slate-400">
                    OTI - UNAMAD
                </p>
            </div>

            {{-- Login Form Card --}}
            <div class="bg-white shadow-2xl rounded-lg p-8">
                <div class="mb-6">
                    <h3 class="text-xl font-semibold text-gray-900">
                        Iniciar Sesión
                    </h3>
                    <p class="mt-1 text-sm text-gray-600">
                        Ingresa tus credenciales para acceder al sistema
                    </p>
                </div>

                {{ $slot }}
            </div>

            {{-- Footer --}}
            <div class="text-center">
                <p class="text-xs text-slate-400">
                    &copy; {{ date('Y') }} SGATI - OTI UNAMAD
                </p>
            </div>
        </div>
    </div>
</body>
</html>
