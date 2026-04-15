@props([])

<aside
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
    class="fixed inset-y-0 left-0 z-30 w-64 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 transform transition-transform duration-300 ease-in-out lg:sticky lg:top-0 lg:h-screen lg:translate-x-0 flex flex-col"
>
    {{-- Logo --}}
    <div class="flex items-center gap-3 px-6 h-16 border-b border-gray-200 dark:border-gray-700 flex-shrink-0">
        <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-blue-600 to-blue-700 flex items-center justify-center shadow-md">
            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
        </div>
        <div>
            <span class="text-lg font-bold text-gray-900 dark:text-white">SGATI</span>
            <p class="text-[10px] text-gray-500 dark:text-gray-400 -mt-0.5">Sistema de Gestión TI</p>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-auto scrollbar-hide px-3 py-5 space-y-5">

        @php
        $linkBase     = 'group flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200';
        $linkActive   = 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 shadow-sm';
        $linkInactive = 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700/60 hover:text-gray-900 dark:hover:text-gray-200';
        $iconBase     = 'w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0';
        $iconActive   = 'bg-blue-100 dark:bg-blue-900/50';
        $iconInactive = 'bg-gray-100 dark:bg-gray-700 group-hover:bg-gray-200 dark:group-hover:bg-gray-600';
        $svgActive    = 'w-4 h-4 text-blue-600 dark:text-blue-400';
        $svgInactive  = 'w-4 h-4 text-gray-500 dark:text-gray-400 group-hover:text-gray-700 dark:group-hover:text-gray-300';
        @endphp

        {{-- ── MENÚ PRINCIPAL ─────────────────────────────── --}}
        <div>
            <p class="px-3 pb-2 text-[10px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">
                Menú Principal
            </p>
            <div class="space-y-0.5">

                {{-- Dashboard --}}
                @php $active = request()->routeIs('dashboard'); @endphp
                <a href="{{ route('dashboard') }}" class="{{ $linkBase }} {{ $active ? $linkActive : $linkInactive }}">
                    <div class="{{ $iconBase }} {{ $active ? $iconActive : $iconInactive }}">
                        <svg class="{{ $active ? $svgActive : $svgInactive }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                    </div>
                    <span>Dashboard</span>
                    @if($active)<div class="ml-auto w-1.5 h-1.5 rounded-full bg-blue-600 dark:bg-blue-400"></div>@endif
                </a>

                {{-- Sistemas --}}
                @can('systems.viewAny')
                @php $active = request()->routeIs('systems.*'); @endphp
                <a href="{{ route('systems.index') }}" class="{{ $linkBase }} {{ $active ? $linkActive : $linkInactive }}">
                    <div class="{{ $iconBase }} {{ $active ? $iconActive : $iconInactive }}">
                        <svg class="{{ $active ? $svgActive : $svgInactive }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <span>Sistemas</span>
                    @if($active)<div class="ml-auto w-1.5 h-1.5 rounded-full bg-blue-600 dark:bg-blue-400"></div>@endif
                </a>
                @endcan

                {{-- Repositorios --}}
                @php $active = request()->routeIs('repositories.index') || request()->routeIs('systems.repositories.*'); @endphp
                <a href="{{ route('repositories.index') }}" class="{{ $linkBase }} {{ $active ? $linkActive : $linkInactive }}">
                    <div class="{{ $iconBase }} {{ $active ? $iconActive : $iconInactive }}">
                        <svg class="{{ $active ? $svgActive : $svgInactive }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                        </svg>
                    </div>
                    <span>Repositorios</span>
                    @if($active)<div class="ml-auto w-1.5 h-1.5 rounded-full bg-blue-600 dark:bg-blue-400"></div>@endif
                </a>

                {{-- Documentos --}}
                @php $active = request()->routeIs('documents.repository'); @endphp
                <a href="{{ route('documents.repository') }}" class="{{ $linkBase }} {{ $active ? $linkActive : $linkInactive }}">
                    <div class="{{ $iconBase }} {{ $active ? $iconActive : $iconInactive }}">
                        <svg class="{{ $active ? $svgActive : $svgInactive }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <span>Documentos</span>
                    @if($active)<div class="ml-auto w-1.5 h-1.5 rounded-full bg-blue-600 dark:bg-blue-400"></div>@endif
                </a>

                {{-- Reportes --}}
                @php $active = request()->routeIs('reports.*'); @endphp
                <a href="{{ route('reports.index') }}" class="{{ $linkBase }} {{ $active ? $linkActive : $linkInactive }}">
                    <div class="{{ $iconBase }} {{ $active ? $iconActive : $iconInactive }}">
                        <svg class="{{ $active ? $svgActive : $svgInactive }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <span>Reportes</span>
                    @if($active)<div class="ml-auto w-1.5 h-1.5 rounded-full bg-blue-600 dark:bg-blue-400"></div>@endif
                </a>

            </div>
        </div>

        @can('admin.users')

        {{-- ── INFRAESTRUCTURA ─────────────────────────────── --}}
        <div>
            <p class="px-3 pb-2 text-[10px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">
                Infraestructura
            </p>
            <div class="space-y-0.5">

                {{-- Servidores --}}
                @can('servers.viewAny')
                @php $active = request()->routeIs('admin.servers.*'); @endphp
                <a href="{{ route('admin.servers.index') }}" class="{{ $linkBase }} {{ $active ? $linkActive : $linkInactive }}">
                    <div class="{{ $iconBase }} {{ $active ? $iconActive : $iconInactive }}">
                        <svg class="{{ $active ? $svgActive : $svgInactive }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/>
                        </svg>
                    </div>
                    <span>Servidores</span>
                    @if($active)<div class="ml-auto w-1.5 h-1.5 rounded-full bg-blue-600 dark:bg-blue-400"></div>@endif
                </a>
                @endcan

                {{-- Certificados SSL --}}
                @can('ssl_certificates.viewAny')
                @php $active = request()->routeIs('admin.ssl-certificates.*'); @endphp
                <a href="{{ route('admin.ssl-certificates.index') }}" class="{{ $linkBase }} {{ $active ? $linkActive : $linkInactive }}">
                    <div class="{{ $iconBase }} {{ $active ? $iconActive : $iconInactive }}">
                        <svg class="{{ $active ? $svgActive : $svgInactive }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <span>Certificados SSL</span>
                    @if($active)<div class="ml-auto w-1.5 h-1.5 rounded-full bg-blue-600 dark:bg-blue-400"></div>@endif
                </a>
                @endcan

            </div>
        </div>

        {{-- ── ADMINISTRACIÓN ──────────────────────────────── --}}
        <div>
            <p class="px-3 pb-2 text-[10px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">
                Administración
            </p>
            <div class="space-y-0.5">

                {{-- Usuarios --}}
                @php $active = request()->routeIs('admin.users.*'); @endphp
                <a href="{{ route('admin.users.index') }}" class="{{ $linkBase }} {{ $active ? $linkActive : $linkInactive }}">
                    <div class="{{ $iconBase }} {{ $active ? $iconActive : $iconInactive }}">
                        <svg class="{{ $active ? $svgActive : $svgInactive }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                    <span>Usuarios</span>
                    @if($active)<div class="ml-auto w-1.5 h-1.5 rounded-full bg-blue-600 dark:bg-blue-400"></div>@endif
                </a>

                {{-- Personas --}}
                @php $active = request()->routeIs('admin.personas.*'); @endphp
                <a href="{{ route('admin.personas.index') }}" class="{{ $linkBase }} {{ $active ? $linkActive : $linkInactive }}">
                    <div class="{{ $iconBase }} {{ $active ? $iconActive : $iconInactive }}">
                        <svg class="{{ $active ? $svgActive : $svgInactive }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <span>Personas</span>
                    @if($active)<div class="ml-auto w-1.5 h-1.5 rounded-full bg-blue-600 dark:bg-blue-400"></div>@endif
                </a>

                {{-- Áreas --}}
                @can('areas.viewAny')
                @php $active = request()->routeIs('admin.areas.*'); @endphp
                <a href="{{ route('admin.areas.index') }}" class="{{ $linkBase }} {{ $active ? $linkActive : $linkInactive }}">
                    <div class="{{ $iconBase }} {{ $active ? $iconActive : $iconInactive }}">
                        <svg class="{{ $active ? $svgActive : $svgInactive }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <span>Áreas</span>
                    @if($active)<div class="ml-auto w-1.5 h-1.5 rounded-full bg-blue-600 dark:bg-blue-400"></div>@endif
                </a>
                @endcan

                {{-- Roles y Permisos --}}
                @php $active = request()->routeIs('admin.roles.*'); @endphp
                <a href="{{ route('admin.roles.index') }}" class="{{ $linkBase }} {{ $active ? $linkActive : $linkInactive }}">
                    <div class="{{ $iconBase }} {{ $active ? $iconActive : $iconInactive }}">
                        <svg class="{{ $active ? $svgActive : $svgInactive }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <span>Roles y Permisos</span>
                    @if($active)<div class="ml-auto w-1.5 h-1.5 rounded-full bg-blue-600 dark:bg-blue-400"></div>@endif
                </a>

                {{-- Configuración --}}
                @php $active = request()->routeIs('admin.settings.*'); @endphp
                <a href="{{ route('admin.settings.index') }}" class="{{ $linkBase }} {{ $active ? $linkActive : $linkInactive }}">
                    <div class="{{ $iconBase }} {{ $active ? $iconActive : $iconInactive }}">
                        <svg class="{{ $active ? $svgActive : $svgInactive }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <span>Configuración</span>
                    @if($active)<div class="ml-auto w-1.5 h-1.5 rounded-full bg-blue-600 dark:bg-blue-400"></div>@endif
                </a>

            </div>
        </div>

        @endcan

    </nav>

    {{-- Footer / Usuario --}}
    <div class="px-4 py-4 border-t border-gray-200 dark:border-gray-700 flex-shrink-0">
        <div class="flex items-center gap-3 px-3 py-3 rounded-xl bg-gray-50 dark:bg-gray-700/60 border border-gray-200 dark:border-gray-600">
            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-semibold text-gray-900 dark:text-white truncate">{{ auth()->user()->name ?? 'Usuario' }}</p>
                <p class="text-[10px] text-gray-500 dark:text-gray-400 truncate">{{ auth()->user()->role->label ?? 'Sin rol' }}</p>
            </div>
        </div>
    </div>
</aside>
