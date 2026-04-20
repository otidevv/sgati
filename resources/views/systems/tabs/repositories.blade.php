<div class="space-y-4">

    {{-- ── Banner de ayuda ── --}}
    <div x-data="{ open: localStorage.getItem('help_repositories') !== '0' }" x-init="$watch('open', v => localStorage.setItem('help_repositories', v ? '1' : '0'))">
        <div x-show="open" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="rounded-xl border border-slate-200 dark:border-slate-600/50 bg-slate-50 dark:bg-slate-800/40 p-4 mb-1">
            <div class="flex items-start gap-3">
                <div class="flex-shrink-0 w-8 h-8 rounded-lg bg-slate-100 dark:bg-slate-700 flex items-center justify-center mt-0.5">
                    <svg class="w-4 h-4 text-slate-600 dark:text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-slate-800 dark:text-slate-200">¿Para qué sirven los Repositorios?</p>
                    <p class="mt-1 text-xs text-slate-700 dark:text-slate-300 leading-relaxed">
                        Vincula el <strong>código fuente</strong> del sistema a su repositorio Git. Permite saber dónde está alojado, qué rama es la principal, quién tiene acceso y cómo se autentican los despliegues.
                    </p>
                    <div class="mt-2.5 grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <div class="flex items-start gap-2 p-2 rounded-lg bg-white/60 dark:bg-gray-800/40 border border-slate-200 dark:border-slate-600/30">
                            <svg class="w-3.5 h-3.5 text-slate-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div>
                                <p class="text-[11px] font-semibold text-slate-700 dark:text-slate-300">Proveedor Git</p>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400">GitHub, GitLab, Bitbucket, Gitea u otro</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-2 p-2 rounded-lg bg-white/60 dark:bg-gray-800/40 border border-slate-200 dark:border-slate-600/30">
                            <svg class="w-3.5 h-3.5 text-slate-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            <div>
                                <p class="text-[11px] font-semibold text-slate-700 dark:text-slate-300">Credenciales de acceso</p>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400">Token, contraseña, Deploy Key u OAuth — guardados de forma segura</p>
                            </div>
                        </div>
                    </div>
                </div>
                <button @click="open = false" title="Cerrar ayuda"
                        class="flex-shrink-0 w-6 h-6 flex items-center justify-center rounded text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
        <div x-show="!open" class="flex justify-end mb-1">
            <button @click="open = true"
                    class="inline-flex items-center gap-1 text-[11px] text-gray-400 dark:text-gray-500 hover:text-slate-500 dark:hover:text-slate-400 transition-colors">
                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                ¿Para qué sirve esto?
            </button>
        </div>
    </div>

    <div class="flex items-center justify-between">
        <h3 class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">
            Repositorios ({{ $system->repositories->count() }})
        </h3>
        @can('repositories.create')
        <a href="{{ route('systems.repositories.create', $system) }}"
           class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/30 hover:bg-blue-100 dark:hover:bg-blue-900/50 rounded-lg transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nuevo Repositorio
        </a>
        @endcan
    </div>

    @forelse($system->repositories->load('collaborators') as $repo)
    @php
    $providerColors = [
        'github'    => 'bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200 ring-slate-300 dark:ring-slate-600',
        'gitlab'    => 'bg-orange-50 dark:bg-orange-900/30 text-orange-700 dark:text-orange-300 ring-orange-200 dark:ring-orange-800',
        'bitbucket' => 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 ring-blue-200 dark:ring-blue-800',
        'gitea'     => 'bg-teal-50 dark:bg-teal-900/30 text-teal-700 dark:text-teal-300 ring-teal-200 dark:ring-teal-800',
        'other'     => 'bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300 ring-gray-200 dark:ring-gray-600',
    ];
    $providerColor = $providerColors[$repo->provider->value] ?? $providerColors['other'];
    @endphp
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 shadow-sm hover:shadow-md dark:hover:shadow-gray-700/50 transition-shadow">
        <div class="flex items-start justify-between gap-3">
            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('systems.repositories.show', [$system, $repo]) }}"
                   class="text-sm font-semibold text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 hover:underline">
                    {{ $repo->name }}
                </a>
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium ring-1 {{ $providerColor }}">
                    {{ $repo->provider->label() }}
                </span>
                @php $activeCollabCount = $repo->collaborators->where('is_active', true)->count(); @endphp
                @if($repo->collaborators->count())
                <a href="{{ route('systems.repositories.show', [$system, $repo]) }}"
                   class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-xs font-medium
                          {{ $activeCollabCount ? 'bg-violet-50 dark:bg-violet-900/30 text-violet-600 dark:text-violet-400' : 'bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400' }}">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    {{ $activeCollabCount }}
                </a>
                @endif
                @if($repo->is_private)
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 ring-1 ring-gray-200 dark:ring-gray-600">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    Privado
                </span>
                @endif
                @if(!$repo->is_active)
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 ring-1 ring-red-200 dark:ring-red-800">
                    Inactivo
                </span>
                @endif
            </div>
            @can('repositories.edit')
            <div class="flex items-center gap-1 flex-shrink-0">
                <a href="{{ route('systems.repositories.show', [$system, $repo]) }}"
                   class="inline-flex items-center justify-center w-7 h-7 rounded text-gray-400 dark:text-gray-500 hover:text-violet-600 dark:hover:text-violet-400 hover:bg-violet-50 dark:hover:bg-violet-900/30 transition-all"
                   title="Ver / gestionar colaboradores">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </a>
                <a href="{{ route('systems.repositories.edit', [$system, $repo]) }}"
                   class="inline-flex items-center justify-center w-7 h-7 rounded text-gray-400 dark:text-gray-500 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-all">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                </a>
                <form action="{{ route('systems.repositories.destroy', [$system, $repo]) }}" method="POST" class="inline">
                    @csrf @method('DELETE')
                    <button type="button" x-data @click.prevent="sgDeleteForm($el.closest('form'), '¿Eliminar el repositorio {{ addslashes($repo->name) }}?')"
                            class="inline-flex items-center justify-center w-7 h-7 rounded text-gray-400 dark:text-gray-500 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 transition-all">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </form>
            </div>
            @endcan
        </div>

        <div class="mt-2.5 grid grid-cols-2 sm:grid-cols-4 gap-2 text-xs">
            @if($repo->clean_url)
            <div class="sm:col-span-2">
                <span class="text-gray-400 dark:text-gray-500">URL</span>
                <p class="mt-0.5">
                    <a href="{{ $repo->clean_url }}" target="_blank"
                       class="font-mono text-blue-600 dark:text-blue-400 hover:underline break-all">
                        {{ $repo->clean_url }}
                    </a>
                </p>
            </div>
            @endif
            @if($repo->username)
            <div>
                <span class="text-gray-400 dark:text-gray-500">Usuario</span>
                <p class="font-mono text-gray-700 dark:text-gray-300 mt-0.5">{{ $repo->username }}</p>
            </div>
            @endif
            @if($repo->default_branch)
            <div>
                <span class="text-gray-400 dark:text-gray-500">Rama</span>
                <p class="font-mono text-gray-700 dark:text-gray-300 mt-0.5">{{ $repo->default_branch }}</p>
            </div>
            @endif
            @if($repo->credential_type)
            <div>
                <span class="text-gray-400 dark:text-gray-500">Credencial</span>
                <p class="text-gray-700 dark:text-gray-300 mt-0.5">
                    {{ match($repo->credential_type) {
                        'token'      => 'Token',
                        'password'   => 'Contraseña',
                        'deploy_key' => 'Deploy Key',
                        'oauth'      => 'OAuth',
                        default      => $repo->credential_type,
                    } }}
                </p>
            </div>
            @endif
        </div>

        @if($repo->notes)
        <p class="mt-2 text-xs text-gray-400 dark:text-gray-500 italic">{{ $repo->notes }}</p>
        @endif
    </div>
    @empty
    <div class="text-center py-12 text-gray-400 dark:text-gray-500">
        <svg class="mx-auto w-10 h-10 text-gray-300 dark:text-gray-600 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
        </svg>
        <p class="text-sm">No hay repositorios registrados.</p>
        @can('repositories.create')
        <a href="{{ route('systems.repositories.create', $system) }}" class="mt-2 inline-block text-sm text-blue-600 dark:text-blue-400 hover:underline">Registrar repositorio →</a>
        @endcan
    </div>
    @endforelse
</div>
