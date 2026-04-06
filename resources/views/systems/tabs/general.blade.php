<div class="space-y-6">
    {{-- Descripción --}}
    @if($system->description)
    <div>
        <h3 class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">Descripción</h3>
        <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">{{ $system->description }}</p>
    </div>
    @endif

    {{-- Ficha técnica --}}
    <div>
        <h3 class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-3">Ficha Técnica</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="space-y-3">
                <div class="flex gap-3">
                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400 w-28 flex-shrink-0 pt-0.5">Nombre</span>
                    <span class="text-sm text-gray-800 dark:text-gray-200">{{ $system->name }}</span>
                </div>
                @if($system->acronym)
                <div class="flex gap-3">
                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400 w-28 flex-shrink-0 pt-0.5">Siglas</span>
                    <span class="text-sm font-mono text-gray-800 dark:text-gray-200">{{ $system->acronym }}</span>
                </div>
                @endif
                <div class="flex gap-3">
                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400 w-28 flex-shrink-0 pt-0.5">Estado</span>
                    <x-status-badge :status="$system->status" />
                </div>
                <div class="flex gap-3">
                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400 w-28 flex-shrink-0 pt-0.5">Área</span>
                    <span class="text-sm text-gray-800 dark:text-gray-200">{{ $system->area->name ?? '—' }}</span>
                </div>
                <div class="flex gap-3">
                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400 w-28 flex-shrink-0 pt-0.5">Responsable</span>
                    <span class="text-sm text-gray-800 dark:text-gray-200">{{ $system->responsible->name ?? '—' }}</span>
                </div>
            </div>
            <div class="space-y-3">
                @if($system->tech_stack)
                <div class="flex gap-3">
                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400 w-28 flex-shrink-0 pt-0.5">Stack</span>
                    <span class="text-sm text-gray-800 dark:text-gray-200">{{ $system->tech_stack }}</span>
                </div>
                @endif
                @if($system->repo_url)
                <div class="flex gap-3">
                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400 w-28 flex-shrink-0 pt-0.5">Repositorio</span>
                    <a href="{{ $system->repo_url }}" target="_blank"
                       class="text-sm text-blue-600 dark:text-blue-400 hover:underline truncate">{{ $system->repo_url }}</a>
                </div>
                @endif
                <div class="flex gap-3">
                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400 w-28 flex-shrink-0 pt-0.5">Registrado</span>
                    <span class="text-sm text-gray-800 dark:text-gray-200">{{ $system->created_at->format('d/m/Y') }}</span>
                </div>
                <div class="flex gap-3">
                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400 w-28 flex-shrink-0 pt-0.5">Actualizado</span>
                    <span class="text-sm text-gray-800 dark:text-gray-200">{{ $system->updated_at->diffForHumans() }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Observaciones --}}
    @if($system->observations)
    <div>
        <h3 class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">Observaciones</h3>
        <div class="p-4 bg-amber-50 dark:bg-amber-900/30 border border-amber-200 dark:border-amber-800 rounded-lg">
            <p class="text-sm text-amber-800 dark:text-amber-200 leading-relaxed">{{ $system->observations }}</p>
        </div>
    </div>
    @endif

    @if(!$system->description && !$system->observations && !$system->tech_stack)
    <div class="text-center py-10 text-gray-400 dark:text-gray-500">
        <svg class="mx-auto w-10 h-10 text-gray-300 dark:text-gray-600 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        <p class="text-sm">Sin información adicional registrada.</p>
        @can('systems.edit')
        <a href="{{ route('systems.edit', $system) }}" class="mt-2 inline-block text-sm text-blue-600 dark:text-blue-400 hover:underline">Completar información →</a>
        @endcan
    </div>
    @endif
</div>
