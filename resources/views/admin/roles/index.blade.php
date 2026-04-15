@extends('layouts.app')

@section('title', 'Roles y Permisos')

@section('content')
<div class="space-y-6" x-data="{
    tab: '{{ request('tab', $roles->where('name','!=','admin')->first()?->name) }}',
    counts: {
        @foreach($roles->where('name','!=','admin') as $role)
        '{{ $role->name }}': {{ $role->permissions->count() }},
        @endforeach
    },
    updateCount(roleId, containerId) {
        const total = document.querySelectorAll('#' + containerId + ' input[type=checkbox]').length;
        const checked = document.querySelectorAll('#' + containerId + ' input[type=checkbox]:checked').length;
        this.counts[roleId] = checked;
    }
}">

    {{-- Header --}}
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Roles y Permisos</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            Define qué acciones puede realizar cada rol en el sistema.
        </p>
    </div>

    @if(session('success'))
    <div class="flex items-center gap-3 p-4 bg-emerald-50 dark:bg-emerald-900/20
                border border-emerald-200 dark:border-emerald-800 rounded-lg">
        <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        <p class="text-sm text-emerald-700 dark:text-emerald-300">{{ session('success') }}</p>
    </div>
    @endif

    {{-- Nota admin --}}
    <div class="flex items-start gap-3 p-4 bg-amber-50 dark:bg-amber-900/20
                border border-amber-200 dark:border-amber-800 rounded-lg">
        <svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="text-sm text-amber-700 dark:text-amber-300">
            <span class="font-semibold">El rol Administrador</span> tiene acceso total al sistema sin restricciones
            y no requiere asignación de permisos.
        </p>
    </div>

    {{-- Tabs --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">

        {{-- Tab bar --}}
        <div class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/60">
            <nav class="flex overflow-x-auto px-4 gap-1 pt-3" aria-label="Roles">
                @foreach($roles as $role)
                <button
                    type="button"
                    @click="tab = '{{ $role->name }}'"
                    :class="tab === '{{ $role->name }}'
                        ? 'border-blue-600 text-blue-600 dark:text-blue-400 dark:border-blue-400 bg-white dark:bg-gray-800'
                        : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300'"
                    class="flex items-center gap-2 whitespace-nowrap px-4 py-2.5 text-sm font-medium
                           border-b-2 -mb-px transition-colors rounded-t-md focus:outline-none">
                    @if($role->name === 'admin')
                    <svg class="w-4 h-4 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    @endif
                    {{ $role->label }}
                    @if($role->name !== 'admin')
                    <span :class="tab === '{{ $role->name }}' ? 'bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400' : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400'"
                          class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium transition-colors"
                          x-text="counts['{{ $role->name }}']">
                    </span>
                    @endif
                </button>
                @endforeach
            </nav>
        </div>

        {{-- Tab panels --}}
        @foreach($roles as $role)
        <div x-show="tab === '{{ $role->name }}'" x-cloak>

            @if($role->name === 'admin')
            {{-- Panel admin --}}
            <div class="p-10 text-center">
                <div class="mx-auto w-16 h-16 rounded-full bg-amber-100 dark:bg-amber-900/30
                            flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Administrador — Acceso Total</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 max-w-sm mx-auto">
                    Este rol tiene todos los privilegios del sistema. No es necesario asignar permisos individuales.
                </p>
            </div>

            @else
            {{-- Panel de permisos editables --}}
            <form action="{{ route('admin.roles.permissions', $role) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="p-6 space-y-6">

                    {{-- Descripción del rol --}}
                    @if($role->description)
                    <p class="text-sm text-gray-500 dark:text-gray-400 italic">{{ $role->description }}</p>
                    @endif

                    {{-- Acciones bulk --}}
                    <div class="flex items-center gap-3">
                        <button type="button"
                                @click="toggleAll('role-{{ $role->id }}', true); updateCount('{{ $role->name }}', 'role-{{ $role->id }}')"
                                class="text-xs text-blue-600 dark:text-blue-400 hover:underline font-medium">
                            Seleccionar todo
                        </button>
                        <span class="text-gray-300 dark:text-gray-600">|</span>
                        <button type="button"
                                @click="toggleAll('role-{{ $role->id }}', false); updateCount('{{ $role->name }}', 'role-{{ $role->id }}')"
                                class="text-xs text-gray-500 dark:text-gray-400 hover:underline font-medium">
                            Deseleccionar todo
                        </button>
                    </div>

                    {{-- Módulos --}}
                    @php
                        $moduleLabels = [
                            'systems'        => ['Sistemas',            'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 00-1-1H9a1 1 0 00-1 1v5m4 0H9'],
                            'infrastructure' => ['Infraestructura',     'M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01'],
                            'versions'       => ['Versiones',           'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                            'databases'      => ['Bases de Datos',      'M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4'],
                            'services'       => ['Servicios / APIs',    'M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                            'integrations'   => ['Integraciones',       'M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z'],
                            'documents'      => ['Documentos',          'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                            'reports'        => ['Reportes',            'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                            'servers'          => ['Servidores',          'M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01'],
                            'ssl_certificates' => ['Certificados SSL',    'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z'],
                            'areas'          => ['Áreas',               'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
                            'admin'          => ['Administración',      'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z'],
                        ];
                        $rolePerms = $role->permissions->pluck('id')->toArray();
                    @endphp

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4" id="role-{{ $role->id }}">
                        @foreach($permissions as $module => $modulePerms)
                        @php
                            [$modLabel, $modIcon] = $moduleLabels[$module] ?? [ucfirst($module), 'M4 6h16M4 12h16M4 18h16'];
                        @endphp
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                            {{-- Module header --}}
                            <div class="flex items-center gap-2 px-4 py-2.5
                                        bg-gray-50 dark:bg-gray-700/40
                                        border-b border-gray-200 dark:border-gray-700">
                                <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 flex-shrink-0"
                                     fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="{{ $modIcon }}"/>
                                </svg>
                                <span class="text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                    {{ $modLabel }}
                                </span>
                                <span class="ml-auto text-xs text-gray-400 dark:text-gray-500">
                                    {{ $modulePerms->whereIn('id', $rolePerms)->count() }}/{{ $modulePerms->count() }}
                                </span>
                            </div>
                            {{-- Permissions list --}}
                            <ul class="divide-y divide-gray-100 dark:divide-gray-700/50">
                                @foreach($modulePerms as $perm)
                                <li class="flex items-center justify-between px-4 py-2.5
                                           hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                    <label for="perm-{{ $role->id }}-{{ $perm->id }}"
                                           class="flex items-center gap-3 cursor-pointer flex-1 min-w-0">
                                        <input type="checkbox"
                                               id="perm-{{ $role->id }}-{{ $perm->id }}"
                                               name="permissions[]"
                                               value="{{ $perm->id }}"
                                               {{ in_array($perm->id, $rolePerms) ? 'checked' : '' }}
                                               @change="updateCount('{{ $role->name }}', 'role-{{ $role->id }}')"
                                               class="w-4 h-4 text-blue-600 border-gray-300 dark:border-gray-600
                                                      rounded focus:ring-blue-500 dark:bg-gray-700 flex-shrink-0">
                                        <div class="min-w-0">
                                            <p class="text-sm text-gray-700 dark:text-gray-200">{{ $perm->label }}</p>
                                            <p class="text-xs text-gray-400 dark:text-gray-500 font-mono truncate">
                                                {{ $perm->name }}
                                            </p>
                                        </div>
                                    </label>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        @endforeach
                    </div>

                </div>

                {{-- Footer --}}
                <div class="flex items-center justify-end gap-3 px-6 py-4
                            border-t border-gray-200 dark:border-gray-700
                            bg-gray-50 dark:bg-gray-800/60">
                    <p class="text-xs text-gray-400 dark:text-gray-500 mr-auto">
                        Los cambios aplican de inmediato al guardar.
                    </p>
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2 text-sm font-medium
                                   text-white bg-blue-600 rounded-lg hover:bg-blue-700
                                   transition-colors shadow-sm focus:outline-none focus:ring-2
                                   focus:ring-blue-500 focus:ring-offset-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Guardar permisos de {{ $role->label }}
                    </button>
                </div>
            </form>
            @endif

        </div>
        @endforeach

    </div>
</div>

@push('scripts')
<script>
function toggleAll(containerId, checked) {
    document.querySelectorAll('#' + containerId + ' input[type="checkbox"]')
            .forEach(cb => cb.checked = checked);
}
</script>
@endpush

@endsection
