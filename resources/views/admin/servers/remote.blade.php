<!DOCTYPE html>
<html lang="es" class="{{ session('darkMode') ? 'dark' : '' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $server->name }} — Acceso remoto · {{ config('app.name') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/sistema/logo.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        if (localStorage.getItem('darkMode') === 'true' ||
            (!localStorage.getItem('darkMode') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>
    <style>
        html, body { height: 100%; margin: 0; overflow: hidden; }
        #guac-frame { display: block; width: 100%; border: none; }
    </style>
</head>
<body class="bg-gray-900 flex flex-col h-screen">

    {{-- Barra superior --}}
    <div class="flex items-center justify-between gap-3 px-4 h-11 shrink-0
                bg-gray-800 border-b border-gray-700">

        {{-- Izquierda: volver + nombre --}}
        <div class="flex items-center gap-3 min-w-0">
            <a href="{{ route('admin.servers.show', $server) }}"
               title="Volver al servidor"
               class="flex items-center justify-center w-7 h-7 rounded-md
                      text-gray-400 hover:text-white hover:bg-gray-700 transition-colors shrink-0">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div class="flex items-center gap-2 min-w-0">
                <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                <span class="text-sm font-semibold text-white truncate">{{ $server->name }}</span>
                <span class="hidden sm:inline text-xs text-gray-400 truncate">
                    — {{ $server->operating_system ?? strtoupper($server->guacamole_protocol ?? '') }}
                </span>
            </div>
        </div>

        {{-- Derecha: usuario + acciones --}}
        <div class="flex items-center gap-2 shrink-0">
            <span class="hidden sm:inline text-xs text-gray-400">
                {{ auth()->user()->guacamole_username }}
            </span>

            {{-- Pantalla completa --}}
            <button type="button" onclick="toggleFullscreen()"
                    title="Pantalla completa"
                    class="flex items-center justify-center w-7 h-7 rounded-md
                           text-gray-400 hover:text-white hover:bg-gray-700 transition-colors">
                <svg id="icon-expand" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                </svg>
                <svg id="icon-compress" class="w-4 h-4 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 9V4.5M9 9H4.5M9 9L3.75 3.75M9 15v4.5M9 15H4.5M9 15l-5.25 5.25M15 9h4.5M15 9V4.5M15 9l5.25-5.25M15 15h4.5M15 15v4.5m0-4.5l5.25 5.25"/>
                </svg>
            </button>

            {{-- Recargar sesión --}}
            <button type="button" onclick="reloadFrame()"
                    title="Recargar sesión"
                    class="flex items-center justify-center w-7 h-7 rounded-md
                           text-gray-400 hover:text-white hover:bg-gray-700 transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
            </button>

            {{-- Logo SGATI --}}
            <span class="text-xs font-bold text-gray-500 pl-1 border-l border-gray-700 ml-1">
                {{ config('app.name') }}
            </span>
        </div>
    </div>

    {{-- iframe de Guacamole --}}
    <iframe id="guac-frame"
            src="{{ $guacUrl }}"
            allow="fullscreen"
            class="flex-1">
    </iframe>

    <script>
        const frame = document.getElementById('guac-frame');

        function reloadFrame() {
            frame.src = frame.src;
        }

        function toggleFullscreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen();
                document.getElementById('icon-expand').classList.add('hidden');
                document.getElementById('icon-compress').classList.remove('hidden');
            } else {
                document.exitFullscreen();
                document.getElementById('icon-expand').classList.remove('hidden');
                document.getElementById('icon-compress').classList.add('hidden');
            }
        }

        document.addEventListener('fullscreenchange', () => {
            const full = !!document.fullscreenElement;
            document.getElementById('icon-expand').classList.toggle('hidden', full);
            document.getElementById('icon-compress').classList.toggle('hidden', !full);
        });
    </script>
</body>
</html>
