@extends('layouts.app')
@section('title', 'Repositorios')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Repositorios</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Credenciales de acceso a repositorios de código</p>
        </div>
        <div class="text-sm text-gray-500 dark:text-gray-400">
            {{ $repositories->count() }} repositorio{{ $repositories->count() !== 1 ? 's' : '' }}
        </div>
    </div>

    @if($repositories->isEmpty())
    <div class="text-center py-20 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
        <div class="p-3 bg-gray-100 dark:bg-gray-700 rounded-full w-fit mx-auto mb-4">
            <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
            </svg>
        </div>
        <h4 class="text-base font-semibold text-gray-700 dark:text-gray-300 mb-1">Sin repositorios registrados</h4>
        <p class="text-sm text-gray-500 dark:text-gray-400">Los repositorios se registran desde la ficha de cada sistema.</p>
    </div>
    @else

    {{-- Agrupado por proveedor --}}
    @php
    $grouped = $repositories->groupBy(fn($r) => $r->provider->value);
    $providerColors = [
        'github'    => ['bg' => 'bg-slate-100 dark:bg-slate-700',   'text' => 'text-slate-700 dark:text-slate-200',  'dot' => 'bg-slate-500'],
        'gitlab'    => ['bg' => 'bg-orange-50 dark:bg-orange-900/30','text' => 'text-orange-700 dark:text-orange-300','dot' => 'bg-orange-500'],
        'bitbucket' => ['bg' => 'bg-blue-50 dark:bg-blue-900/30',   'text' => 'text-blue-700 dark:text-blue-300',   'dot' => 'bg-blue-500'],
        'gitea'     => ['bg' => 'bg-teal-50 dark:bg-teal-900/30',   'text' => 'text-teal-700 dark:text-teal-300',   'dot' => 'bg-teal-500'],
        'other'     => ['bg' => 'bg-gray-50 dark:bg-gray-700',      'text' => 'text-gray-700 dark:text-gray-300',   'dot' => 'bg-gray-400'],
    ];
    @endphp

    @foreach($grouped as $providerValue => $repos)
    @php
        $firstRepo = $repos->first();
        $colors    = $providerColors[$providerValue] ?? $providerColors['other'];
    @endphp
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
        {{-- Provider header --}}
        <div class="px-5 py-3 border-b border-gray-100 dark:border-gray-700 flex items-center gap-2 {{ $colors['bg'] }}">
            <span class="w-2 h-2 rounded-full {{ $colors['dot'] }}"></span>
            <span class="text-sm font-semibold {{ $colors['text'] }}">{{ $firstRepo->provider->label() }}</span>
            <span class="ml-auto text-xs {{ $colors['text'] }} opacity-70">{{ $repos->count() }} repo{{ $repos->count() !== 1 ? 's' : '' }}</span>
        </div>

        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            @foreach($repos as $repo)
            <div class="px-5 py-4 flex items-start gap-4 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $repo->name }}</span>
                        @if($repo->system)
                        <a href="{{ route('systems.show', $repo->system) }}"
                           class="text-xs text-blue-600 dark:text-blue-400 hover:underline">
                            {{ $repo->system->acronym ?? $repo->system->name }}
                        </a>
                        @endif
                        @if($repo->is_private)
                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-xs bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400">
                            <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            Privado
                        </span>
                        @endif
                        @if(!$repo->is_active)
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs bg-red-50 dark:bg-red-900/30 text-red-500 dark:text-red-400">Inactivo</span>
                        @endif
                    </div>

                    <div class="mt-1.5 flex flex-wrap gap-x-5 gap-y-1 text-xs text-gray-500 dark:text-gray-400">
                        @if($repo->clean_url)
                        <a href="{{ $repo->clean_url }}" target="_blank"
                           class="font-mono text-blue-600 dark:text-blue-400 hover:underline truncate max-w-xs">
                            {{ $repo->clean_url }}
                        </a>
                        @endif
                        @if($repo->username)
                        <span class="font-mono">{{ $repo->username }}</span>
                        @endif
                        @if($repo->default_branch)
                        <span class="flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ $repo->default_branch }}
                        </span>
                        @endif
                        @if($repo->credential_type)
                        <span>{{ match($repo->credential_type) {
                            'token'      => 'Token',
                            'password'   => 'Contraseña',
                            'deploy_key' => 'Deploy Key',
                            'oauth'      => 'OAuth',
                            default      => $repo->credential_type,
                        } }}</span>
                        @endif
                    </div>
                </div>

                @if($repo->system)
                <a href="{{ route('systems.repositories.edit', [$repo->system, $repo]) }}"
                   class="flex-shrink-0 inline-flex items-center justify-center w-7 h-7 rounded text-gray-400 dark:text-gray-500 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-all">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                    </svg>
                </a>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endforeach

    @endif
</div>
@endsection
