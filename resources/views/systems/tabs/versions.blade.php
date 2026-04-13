<div class="space-y-4">

    {{-- ── Banner de ayuda (colapsable, se recuerda en localStorage) ── --}}
    <div x-data="{ open: localStorage.getItem('help_versions') !== '0' }" x-init="$watch('open', v => localStorage.setItem('help_versions', v ? '1' : '0'))">
        <div x-show="open" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="rounded-xl border border-blue-200 dark:border-blue-700/50 bg-blue-50 dark:bg-blue-900/20 p-4 mb-1">
            <div class="flex items-start gap-3">
                <div class="flex-shrink-0 w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/50 flex items-center justify-center mt-0.5">
                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-blue-800 dark:text-blue-200">¿Para qué sirve el Historial de Versiones?</p>
                    <p class="mt-1 text-xs text-blue-700 dark:text-blue-300 leading-relaxed">
                        Aquí registras cada <strong>despliegue o actualización</strong> del sistema. Permite saber qué versión está actualmente en producción, cuándo se publicó, qué cambios incluyó y quién la desplegó.
                    </p>
                    <div class="mt-2.5 grid grid-cols-1 sm:grid-cols-3 gap-2">
                        <div class="flex items-start gap-2 p-2 rounded-lg bg-white/60 dark:bg-gray-800/40 border border-blue-100 dark:border-blue-700/30">
                            <svg class="w-3.5 h-3.5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                            </svg>
                            <div>
                                <p class="text-[11px] font-semibold text-blue-800 dark:text-blue-200">Número de versión</p>
                                <p class="text-[11px] text-blue-600 dark:text-blue-400">Ej: <code class="font-mono">1.0.0</code>, <code class="font-mono">2.3.1</code>, <code class="font-mono">2024.04</code></p>
                            </div>
                        </div>
                        <div class="flex items-start gap-2 p-2 rounded-lg bg-white/60 dark:bg-gray-800/40 border border-blue-100 dark:border-blue-700/30">
                            <svg class="w-3.5 h-3.5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <div>
                                <p class="text-[11px] font-semibold text-blue-800 dark:text-blue-200">Cambios incluidos</p>
                                <p class="text-[11px] text-blue-600 dark:text-blue-400">Qué se corrigió, mejoró o añadió en ese despliegue</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-2 p-2 rounded-lg bg-white/60 dark:bg-gray-800/40 border border-blue-100 dark:border-blue-700/30">
                            <svg class="w-3.5 h-3.5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2"/>
                            </svg>
                            <div>
                                <p class="text-[11px] font-semibold text-blue-800 dark:text-blue-200">Ambiente</p>
                                <p class="text-[11px] text-blue-600 dark:text-blue-400">Si se desplegó en Producción, Staging o Desarrollo</p>
                            </div>
                        </div>
                    </div>
                </div>
                <button @click="open = false" title="Cerrar ayuda"
                        class="flex-shrink-0 w-6 h-6 flex items-center justify-center rounded text-blue-400 hover:text-blue-600 dark:hover:text-blue-300 hover:bg-blue-100 dark:hover:bg-blue-900/40 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Botón para volver a mostrar la ayuda --}}
        <div x-show="!open" class="flex justify-end mb-1">
            <button @click="open = true"
                    class="inline-flex items-center gap-1 text-[11px] text-gray-400 dark:text-gray-500 hover:text-blue-500 dark:hover:text-blue-400 transition-colors">
                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                ¿Para qué sirve esto?
            </button>
        </div>
    </div>

    <div class="flex items-center justify-between">
        <h3 class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">
            Historial de Versiones ({{ $system->versions->count() }})
        </h3>
        @can('versions.create/edit/delete')
        <a href="{{ route('systems.versions.create', $system) }}"
           class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/30 hover:bg-blue-100 dark:hover:bg-blue-900/50 rounded-lg transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nueva Versión
        </a>
        @endcan
    </div>

    @forelse($system->versions as $version)
    @php
    $envColors = match($version->environment?->value) {
        'production'  => 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300',
        'staging'     => 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300',
        'development' => 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300',
        default       => 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300',
    };
    @endphp
    <div class="relative pl-6 pb-5 border-l-2 border-gray-200 dark:border-gray-700 last:border-l-transparent last:pb-0">
        {{-- Dot --}}
        <span class="absolute -left-[9px] top-0 w-4 h-4 rounded-full bg-white dark:bg-gray-800 border-2 border-blue-400 dark:border-blue-500 flex items-center justify-center">
            <span class="w-1.5 h-1.5 rounded-full bg-blue-400 dark:bg-blue-500"></span>
        </span>

        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 shadow-sm hover:shadow-md dark:hover:shadow-gray-700/50 transition-shadow">
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 text-xs font-bold font-mono ring-1 ring-blue-200 dark:ring-blue-800">
                        v{{ $version->version }}
                    </span>
                    @if($version->environment)
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $envColors }}">
                        {{ $version->environment->label() }}
                    </span>
                    @endif
                    @if($version->release_date)
                    <span class="text-xs text-gray-400 dark:text-gray-500">{{ $version->release_date->format('d/m/Y') }}</span>
                    @endif
                </div>
                @can('versions.create/edit/delete')
                <div class="flex items-center gap-1 flex-shrink-0">
                    <a href="{{ route('systems.versions.edit', [$system, $version]) }}"
                       class="inline-flex items-center justify-center w-7 h-7 rounded text-gray-400 dark:text-gray-500 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-all">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                    </a>
                    <form action="{{ route('systems.versions.destroy', [$system, $version]) }}" method="POST" class="inline">
                        @csrf @method('DELETE')
                        <button type="button" x-data @click.prevent="sgDeleteForm($el.closest('form'), '¿Eliminar versión {{ addslashes($version->version) }}?')"
                                class="inline-flex items-center justify-center w-7 h-7 rounded text-gray-400 dark:text-gray-500 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 transition-all">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </form>
                </div>
                @endcan
            </div>

            @if($version->changes)
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-300 leading-relaxed">{{ $version->changes }}</p>
            @endif

            <div class="mt-2.5 flex flex-wrap gap-x-4 gap-y-1 text-xs text-gray-400 dark:text-gray-500">
                @if($version->git_branch)
                <span class="flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ $version->git_branch }}
                </span>
                @endif
                @if($version->git_commit)
                <span class="font-mono">{{ substr($version->git_commit, 0, 8) }}</span>
                @endif
                @if($version->deployedBy)
                <span>por {{ $version->deployedBy->name }}</span>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="text-center py-12 text-gray-400 dark:text-gray-500">
        <svg class="mx-auto w-10 h-10 text-gray-300 dark:text-gray-600 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
        </svg>
        <p class="text-sm">No hay versiones registradas.</p>
        @can('versions.create/edit/delete')
        <a href="{{ route('systems.versions.create', $system) }}" class="mt-2 inline-block text-sm text-blue-600 dark:text-blue-400 hover:underline">
            Registrar primera versión →
        </a>
        @endcan
    </div>
    @endforelse
</div>
