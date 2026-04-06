@props([])

<aside
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
    class="fixed inset-y-0 left-0 z-30 w-64 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 transform transition-transform duration-300 ease-in-out lg:static lg:translate-x-0 flex flex-col"
>
    {{-- Logo Section --}}
    <div class="flex items-center gap-3 px-6 h-16 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 flex-shrink-0">
        <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-blue-600 to-blue-700 flex items-center justify-center shadow-md">
            <svg class="w-5 h-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
        </div>
        <div>
            <span class="text-lg font-bold text-gray-900 dark:text-white">SGATI</span>
            <p class="text-[10px] text-gray-500 dark:text-gray-400 -mt-0.5">Sistema de Gestión TI</p>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-auto scrollbar-hide px-4 py-5 space-y-1">
        {{-- Main Menu Label --}}
        <p class="px-3 text-[11px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">Menú Principal</p>
        
        {{-- Dashboard --}}
        <a href="{{ route('dashboard') }}"
           class="group flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-gray-900 dark:hover:text-gray-200' }}">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center {{ request()->routeIs('dashboard') ? 'bg-blue-100 dark:bg-blue-900/50' : 'bg-gray-100 dark:bg-gray-700 group-hover:bg-gray-200 dark:group-hover:bg-gray-600' }}">
                <svg class="w-4 h-4 {{ request()->routeIs('dashboard') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400 group-hover:text-gray-700 dark:group-hover:text-gray-300' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
            </div>
            <span>Dashboard</span>
            @if(request()->routeIs('dashboard'))
            <div class="ml-auto w-1.5 h-1.5 rounded-full bg-blue-600 dark:bg-blue-400"></div>
            @endif
        </a>

        {{-- Sistemas --}}
        <a href="{{ route('systems.index') }}"
           class="group flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('systems.*') ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-gray-900 dark:hover:text-gray-200' }}">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center {{ request()->routeIs('systems.*') ? 'bg-blue-100 dark:bg-blue-900/50' : 'bg-gray-100 dark:bg-gray-700 group-hover:bg-gray-200 dark:group-hover:bg-gray-600' }}">
                <svg class="w-4 h-4 {{ request()->routeIs('systems.*') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400 group-hover:text-gray-700 dark:group-hover:text-gray-300' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </div>
            <span>Sistemas</span>
            @if(request()->routeIs('systems.*'))
            <div class="ml-auto w-1.5 h-1.5 rounded-full bg-blue-600 dark:bg-blue-400"></div>
            @endif
        </a>

        {{-- Documentos --}}
        <a href="{{ route('documents.repository') }}"
           class="group flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('documents.repository') ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-gray-900 dark:hover:text-gray-200' }}">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center {{ request()->routeIs('documents.repository') ? 'bg-blue-100 dark:bg-blue-900/50' : 'bg-gray-100 dark:bg-gray-700 group-hover:bg-gray-200 dark:group-hover:bg-gray-600' }}">
                <svg class="w-4 h-4 {{ request()->routeIs('documents.repository') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400 group-hover:text-gray-700 dark:group-hover:text-gray-300' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <span>Documentos</span>
            @if(request()->routeIs('documents.repository'))
            <div class="ml-auto w-1.5 h-1.5 rounded-full bg-blue-600 dark:bg-blue-400"></div>
            @endif
        </a>

        {{-- Reportes --}}
        <a href="{{ route('reports.index') }}"
           class="group flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('reports.*') ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-gray-900 dark:hover:text-gray-200' }}">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center {{ request()->routeIs('reports.*') ? 'bg-blue-100 dark:bg-blue-900/50' : 'bg-gray-100 dark:bg-gray-700 group-hover:bg-gray-200 dark:group-hover:bg-gray-600' }}">
                <svg class="w-4 h-4 {{ request()->routeIs('reports.*') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400 group-hover:text-gray-700 dark:group-hover:text-gray-300' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
            </div>
            <span>Reportes</span>
            @if(request()->routeIs('reports.*'))
            <div class="ml-auto w-1.5 h-1.5 rounded-full bg-blue-600 dark:bg-blue-400"></div>
            @endif
        </a>

        {{-- Administration Section --}}
        @can('admin.users')
        <div class="pt-4 mt-4 border-t border-gray-200 dark:border-gray-700">
            <p class="px-3 text-[11px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">Administración</p>

            {{-- Usuarios --}}
            <a href="{{ route('admin.users.index') }}"
               class="group flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('admin.users.*') ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-gray-900 dark:hover:text-gray-200' }}">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center {{ request()->routeIs('admin.users.*') ? 'bg-blue-100 dark:bg-blue-900/50' : 'bg-gray-100 dark:bg-gray-700 group-hover:bg-gray-200 dark:group-hover:bg-gray-600' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('admin.users.*') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400 group-hover:text-gray-700 dark:group-hover:text-gray-300' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <span>Usuarios</span>
                @if(request()->routeIs('admin.users.*'))
                <div class="ml-auto w-1.5 h-1.5 rounded-full bg-blue-600 dark:bg-blue-400"></div>
                @endif
            </a>

            {{-- Personas --}}
            <a href="{{ route('admin.personas.index') }}"
               class="group flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('admin.personas.*') ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-gray-900 dark:hover:text-gray-200' }}">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center {{ request()->routeIs('admin.personas.*') ? 'bg-blue-100 dark:bg-blue-900/50' : 'bg-gray-100 dark:bg-gray-700 group-hover:bg-gray-200 dark:group-hover:bg-gray-600' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('admin.personas.*') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400 group-hover:text-gray-700 dark:group-hover:text-gray-300' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <span>Personas</span>
                @if(request()->routeIs('admin.personas.*'))
                <div class="ml-auto w-1.5 h-1.5 rounded-full bg-blue-600 dark:bg-blue-400"></div>
                @endif
            </a>

            {{-- Áreas --}}
            <a href="{{ route('admin.areas.index') }}"
               class="group flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('admin.areas.*') ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-gray-900 dark:hover:text-gray-200' }}">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center {{ request()->routeIs('admin.areas.*') ? 'bg-blue-100 dark:bg-blue-900/50' : 'bg-gray-100 dark:bg-gray-700 group-hover:bg-gray-200 dark:group-hover:bg-gray-600' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('admin.areas.*') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400 group-hover:text-gray-700 dark:group-hover:text-gray-300' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <span>Áreas</span>
                @if(request()->routeIs('admin.areas.*'))
                <div class="ml-auto w-1.5 h-1.5 rounded-full bg-blue-600 dark:bg-blue-400"></div>
                @endif
            </a>
        </div>
        @endcan
    </nav>

    {{-- Footer Section --}}
    <div class="px-4 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 flex-shrink-0">
        <div class="flex items-center gap-3 px-3 py-2 rounded-lg bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600">
            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-semibold text-gray-900 dark:text-white truncate">{{ auth()->user()->name ?? 'Usuario' }}</p>
                <p class="text-[10px] text-gray-500 dark:text-gray-400 truncate">{{ auth()->user()->email ?? '' }}</p>
            </div>
        </div>
    </div>
</aside>
