@props([])

<aside
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
    class="fixed inset-y-0 left-0 z-30 w-64 bg-gradient-to-b from-slate-800 to-slate-900 text-white transform transition-transform duration-300 ease-in-out lg:static lg:translate-x-0"
>
    {{-- Logo --}}
    <div class="flex items-center justify-center h-16 bg-slate-900 border-b border-slate-700">
        <div class="flex items-center space-x-2">
            <svg class="w-8 h-8 text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01" />
            </svg>
            <span class="text-xl font-bold">SGATI</span>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="mt-5 px-2">
        {{-- Dashboard --}}
        <a href="{{ route('dashboard') }}" 
           class="group flex items-center px-2 py-2 text-base font-medium rounded-md transition-colors {{ request()->routeIs('dashboard') ? 'bg-slate-700 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
            <svg class="mr-4 h-6 w-6 text-slate-400 group-hover:text-slate-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            Dashboard
        </a>

        {{-- Sistemas --}}
        <a href="{{ route('systems.index') }}" 
           class="mt-1 group flex items-center px-2 py-2 text-base font-medium rounded-md transition-colors {{ request()->routeIs('systems.*') ? 'bg-slate-700 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
            <svg class="mr-4 h-6 w-6 text-slate-400 group-hover:text-slate-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
            Sistemas
        </a>

        {{-- Documentos --}}
        <a href="{{ route('documents.repository') }}" 
           class="mt-1 group flex items-center px-2 py-2 text-base font-medium rounded-md transition-colors {{ request()->routeIs('documents.repository') ? 'bg-slate-700 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
            <svg class="mr-4 h-6 w-6 text-slate-400 group-hover:text-slate-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Documentos
        </a>

        {{-- Reportes --}}
        <a href="{{ route('reports.index') }}" 
           class="mt-1 group flex items-center px-2 py-2 text-base font-medium rounded-md transition-colors {{ request()->routeIs('reports.*') ? 'bg-slate-700 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
            <svg class="mr-4 h-6 w-6 text-slate-400 group-hover:text-slate-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            Reportes
        </a>

        {{-- Administración (solo admin) --}}
        @can('admin.users')
        <div class="mt-4">
            <p class="px-2 text-xs font-semibold text-slate-400 uppercase tracking-wider">Administración</p>
        </div>

        {{-- Usuarios --}}
        <a href="{{ route('admin.users.index') }}" 
           class="mt-1 group flex items-center px-2 py-2 text-base font-medium rounded-md transition-colors {{ request()->routeIs('admin.users.*') ? 'bg-slate-700 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
            <svg class="mr-4 h-6 w-6 text-slate-400 group-hover:text-slate-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            Usuarios
        </a>

        {{-- Personas --}}
        <a href="{{ route('admin.personas.index') }}" 
           class="mt-1 group flex items-center px-2 py-2 text-base font-medium rounded-md transition-colors {{ request()->routeIs('admin.personas.*') ? 'bg-slate-700 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
            <svg class="mr-4 h-6 w-6 text-slate-400 group-hover:text-slate-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            Personas
        </a>

        {{-- Áreas --}}
        <a href="{{ route('admin.areas.index') }}" 
           class="mt-1 group flex items-center px-2 py-2 text-base font-medium rounded-md transition-colors {{ request()->routeIs('admin.areas.*') ? 'bg-slate-700 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
            <svg class="mr-4 h-6 w-6 text-slate-400 group-hover:text-slate-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
            Áreas
        </a>
        @endcan
    </nav>
</aside>
