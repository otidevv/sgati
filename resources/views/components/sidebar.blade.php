@props([])

<aside
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
    class="fixed inset-y-0 left-0 z-30 w-64 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700
           transform transition-transform duration-300 ease-in-out
           lg:sticky lg:top-0 lg:h-screen lg:translate-x-0 flex flex-col"
>
    {{-- Logo --}}
    <div class="flex items-center gap-3 px-6 h-16 border-b border-gray-200 dark:border-gray-700 flex-shrink-0">
        <img src="{{ asset('images/sistema/logo.png') }}" alt="Logo SGATI" class="w-9 h-9 object-contain">
        <div>
            <span class="text-lg font-bold text-gray-900 dark:text-white">SGATI</span>
            <p class="text-[10px] text-gray-500 dark:text-gray-400 -mt-0.5">Sistema de Gestión TI</p>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-auto scrollbar-hide px-3 py-4 space-y-0.5">

        @php
        /* ── Estilos reutilizables ── */
        $link      = 'group flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200';
        $active    = 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 shadow-sm';
        $inactive  = 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700/60 hover:text-gray-900 dark:hover:text-gray-200';

        $iconWrap    = 'w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0';
        $iconActive  = 'bg-blue-100 dark:bg-blue-900/50';
        $iconInact   = 'bg-gray-100 dark:bg-gray-700 group-hover:bg-gray-200 dark:group-hover:bg-gray-600';

        $svg    = 'w-4 h-4';
        $svgA   = 'text-blue-600 dark:text-blue-400';
        $svgI   = 'text-gray-500 dark:text-gray-400 group-hover:text-gray-700 dark:group-hover:text-gray-300';

        /* Sub-ítems (dentro de dropdowns) */
        $sub    = 'group flex items-center gap-2.5 pl-11 pr-3 py-2 text-sm font-medium rounded-xl transition-all duration-200';
        $subA   = 'text-blue-700 dark:text-blue-300 bg-blue-50 dark:bg-blue-900/20';
        $subI   = 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700/60 hover:text-gray-800 dark:hover:text-gray-200';
        @endphp

        {{-- ════ MENÚ PRINCIPAL ════════════════════════════════════ --}}
        <p class="px-3 pt-1 pb-2 text-[10px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">
            Principal
        </p>

        {{-- Dashboard --}}
        @php $on = request()->routeIs('dashboard'); @endphp
        <a href="{{ route('dashboard') }}" class="{{ $link }} {{ $on ? $active : $inactive }}">
            <div class="{{ $iconWrap }} {{ $on ? $iconActive : $iconInact }}">
                <svg class="{{ $svg }} {{ $on ? $svgA : $svgI }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
            </div>
            <span>Dashboard</span>
            @if($on)<span class="ml-auto w-1.5 h-1.5 rounded-full bg-blue-600 dark:bg-blue-400"></span>@endif
        </a>

        {{-- Sistemas --}}
        @can('systems.viewAny')
        @php $on = request()->routeIs('systems.*'); @endphp
        <a href="{{ route('systems.index') }}" class="{{ $link }} {{ $on ? $active : $inactive }}">
            <div class="{{ $iconWrap }} {{ $on ? $iconActive : $iconInact }}">
                <svg class="{{ $svg }} {{ $on ? $svgA : $svgI }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <span>Sistemas</span>
            @if($on)<span class="ml-auto w-1.5 h-1.5 rounded-full bg-blue-600 dark:bg-blue-400"></span>@endif
        </a>
        @endcan

        {{-- Repositorios --}}
        @php $on = request()->routeIs('repositories.index') || request()->routeIs('systems.repositories.*'); @endphp
        <a href="{{ route('repositories.index') }}" class="{{ $link }} {{ $on ? $active : $inactive }}">
            <div class="{{ $iconWrap }} {{ $on ? $iconActive : $iconInact }}">
                <svg class="{{ $svg }} {{ $on ? $svgA : $svgI }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                </svg>
            </div>
            <span>Repositorios</span>
            @if($on)<span class="ml-auto w-1.5 h-1.5 rounded-full bg-blue-600 dark:bg-blue-400"></span>@endif
        </a>

        {{-- Documentos --}}
        @php $on = request()->routeIs('documents.repository'); @endphp
        <a href="{{ route('documents.repository') }}" class="{{ $link }} {{ $on ? $active : $inactive }}">
            <div class="{{ $iconWrap }} {{ $on ? $iconActive : $iconInact }}">
                <svg class="{{ $svg }} {{ $on ? $svgA : $svgI }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <span>Documentos</span>
            @if($on)<span class="ml-auto w-1.5 h-1.5 rounded-full bg-blue-600 dark:bg-blue-400"></span>@endif
        </a>

        {{-- Reportes --}}
        @php $on = request()->routeIs('reports.*'); @endphp
        <a href="{{ route('reports.index') }}" class="{{ $link }} {{ $on ? $active : $inactive }}">
            <div class="{{ $iconWrap }} {{ $on ? $iconActive : $iconInact }}">
                <svg class="{{ $svg }} {{ $on ? $svgA : $svgI }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
            <span>Reportes</span>
            @if($on)<span class="ml-auto w-1.5 h-1.5 rounded-full bg-blue-600 dark:bg-blue-400"></span>@endif
        </a>

        @can('admin.users')

        {{-- Divisor --}}
        <div class="my-3 border-t border-gray-100 dark:border-gray-700/60"></div>

        {{-- ════ INFRAESTRUCTURA (dropdown) ═══════════════════════ --}}
        @php
            $infraOn = request()->routeIs('admin.servers.*')
                    || request()->routeIs('admin.ssl-certificates.*');
        @endphp
        <div x-data="{ open: {{ $infraOn ? 'true' : 'false' }} }">

            <button @click="open = !open"
                    class="{{ $link }} w-full {{ $infraOn ? $active : $inactive }}">
                <div class="{{ $iconWrap }} {{ $infraOn ? $iconActive : $iconInact }}">
                    <svg class="{{ $svg }} {{ $infraOn ? $svgA : $svgI }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/>
                    </svg>
                </div>
                <span class="flex-1 text-left">Infraestructura</span>
                <svg class="w-3.5 h-3.5 opacity-40 transition-transform duration-200"
                     :class="open ? 'rotate-180' : ''"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            <div x-show="open"
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 -translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-1"
                 class="mt-0.5 space-y-0.5">

                {{-- Servidores --}}
                @can('servers.viewAny')
                @php $on = request()->routeIs('admin.servers.*'); @endphp
                <a href="{{ route('admin.servers.index') }}" class="{{ $sub }} {{ $on ? $subA : $subI }}">
                    <span class="w-1.5 h-1.5 rounded-full shrink-0
                                 {{ $on ? 'bg-blue-500 dark:bg-blue-400' : 'bg-gray-300 dark:bg-gray-600 group-hover:bg-gray-400 dark:group-hover:bg-gray-500' }}"></span>
                    Servidores
                </a>
                @endcan

                {{-- Certificados SSL --}}
                @can('ssl_certificates.viewAny')
                @php $on = request()->routeIs('admin.ssl-certificates.*'); @endphp
                <a href="{{ route('admin.ssl-certificates.index') }}" class="{{ $sub }} {{ $on ? $subA : $subI }}">
                    <span class="w-1.5 h-1.5 rounded-full shrink-0
                                 {{ $on ? 'bg-blue-500 dark:bg-blue-400' : 'bg-gray-300 dark:bg-gray-600 group-hover:bg-gray-400 dark:group-hover:bg-gray-500' }}"></span>
                    Certificados SSL
                </a>
                @endcan

            </div>
        </div>

        {{-- ════ ADMINISTRACIÓN (dropdown) ════════════════════════ --}}
        @php
            $adminOn = request()->routeIs('admin.users.*')
                    || request()->routeIs('admin.personas.*')
                    || request()->routeIs('admin.areas.*')
                    || request()->routeIs('admin.roles.*')
                    || request()->routeIs('admin.settings.*');
        @endphp
        <div x-data="{ open: {{ $adminOn ? 'true' : 'false' }} }">

            <button @click="open = !open"
                    class="{{ $link }} w-full {{ $adminOn ? $active : $inactive }}">
                <div class="{{ $iconWrap }} {{ $adminOn ? $iconActive : $iconInact }}">
                    <svg class="{{ $svg }} {{ $adminOn ? $svgA : $svgI }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                    </svg>
                </div>
                <span class="flex-1 text-left">Administración</span>
                <svg class="w-3.5 h-3.5 opacity-40 transition-transform duration-200"
                     :class="open ? 'rotate-180' : ''"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            <div x-show="open"
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 -translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-1"
                 class="mt-0.5 space-y-0.5">

                {{-- Usuarios --}}
                @php $on = request()->routeIs('admin.users.*'); @endphp
                <a href="{{ route('admin.users.index') }}" class="{{ $sub }} {{ $on ? $subA : $subI }}">
                    <span class="w-1.5 h-1.5 rounded-full shrink-0
                                 {{ $on ? 'bg-blue-500 dark:bg-blue-400' : 'bg-gray-300 dark:bg-gray-600 group-hover:bg-gray-400 dark:group-hover:bg-gray-500' }}"></span>
                    Usuarios
                </a>

                {{-- Personas --}}
                @php $on = request()->routeIs('admin.personas.*'); @endphp
                <a href="{{ route('admin.personas.index') }}" class="{{ $sub }} {{ $on ? $subA : $subI }}">
                    <span class="w-1.5 h-1.5 rounded-full shrink-0
                                 {{ $on ? 'bg-blue-500 dark:bg-blue-400' : 'bg-gray-300 dark:bg-gray-600 group-hover:bg-gray-400 dark:group-hover:bg-gray-500' }}"></span>
                    Personas
                </a>

                {{-- Áreas --}}
                @can('areas.viewAny')
                @php $on = request()->routeIs('admin.areas.*'); @endphp
                <a href="{{ route('admin.areas.index') }}" class="{{ $sub }} {{ $on ? $subA : $subI }}">
                    <span class="w-1.5 h-1.5 rounded-full shrink-0
                                 {{ $on ? 'bg-blue-500 dark:bg-blue-400' : 'bg-gray-300 dark:bg-gray-600 group-hover:bg-gray-400 dark:group-hover:bg-gray-500' }}"></span>
                    Áreas
                </a>
                @endcan

                {{-- Roles y Permisos --}}
                @php $on = request()->routeIs('admin.roles.*'); @endphp
                <a href="{{ route('admin.roles.index') }}" class="{{ $sub }} {{ $on ? $subA : $subI }}">
                    <span class="w-1.5 h-1.5 rounded-full shrink-0
                                 {{ $on ? 'bg-blue-500 dark:bg-blue-400' : 'bg-gray-300 dark:bg-gray-600 group-hover:bg-gray-400 dark:group-hover:bg-gray-500' }}"></span>
                    Roles y Permisos
                </a>

                {{-- Configuración --}}
                @php $on = request()->routeIs('admin.settings.*'); @endphp
                <a href="{{ route('admin.settings.index') }}" class="{{ $sub }} {{ $on ? $subA : $subI }}">
                    <span class="w-1.5 h-1.5 rounded-full shrink-0
                                 {{ $on ? 'bg-blue-500 dark:bg-blue-400' : 'bg-gray-300 dark:bg-gray-600 group-hover:bg-gray-400 dark:group-hover:bg-gray-500' }}"></span>
                    Configuración
                </a>

            </div>
        </div>

        @endcan

    </nav>

    {{-- Footer / Usuario --}}
    <div class="px-4 py-4 border-t border-gray-200 dark:border-gray-700 flex-shrink-0">
        <div class="flex items-center gap-3 px-3 py-3 rounded-xl bg-gray-50 dark:bg-gray-700/60
                    border border-gray-200 dark:border-gray-600">
            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600
                        flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-semibold text-gray-900 dark:text-white truncate">
                    {{ auth()->user()->name ?? 'Usuario' }}
                </p>
                <p class="text-[10px] text-gray-500 dark:text-gray-400 truncate">
                    {{ auth()->user()->role->label ?? 'Sin rol' }}
                </p>
            </div>
        </div>
    </div>
</aside>
