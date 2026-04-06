<div class="space-y-4">
    <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">
        Historial de Cambios de Estado ({{ $system->statusLogs->count() }})
    </h3>

    @forelse($system->statusLogs as $log)
    @php
    $oldColor = match($log->old_status) {
        'active'      => 'bg-green-100 text-green-700',
        'development' => 'bg-blue-100 text-blue-700',
        'maintenance' => 'bg-yellow-100 text-yellow-700',
        'inactive'    => 'bg-red-100 text-red-600',
        default       => 'bg-gray-100 text-gray-600',
    };
    $newColor = match($log->new_status) {
        'active'      => 'bg-green-100 text-green-700',
        'development' => 'bg-blue-100 text-blue-700',
        'maintenance' => 'bg-yellow-100 text-yellow-700',
        'inactive'    => 'bg-red-100 text-red-600',
        default       => 'bg-gray-100 text-gray-600',
    };
    $statusLabel = fn(string $s) => match($s) {
        'active'      => 'Activo',
        'development' => 'En desarrollo',
        'maintenance' => 'Mantenimiento',
        'inactive'    => 'Inactivo',
        default       => ucfirst($s),
    };
    @endphp
    <div class="relative pl-6 pb-5 border-l-2 border-gray-200 last:border-l-transparent last:pb-0">
        <span class="absolute -left-[9px] top-0 w-4 h-4 rounded-full bg-white border-2 border-gray-300 flex items-center justify-center">
            <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
        </span>
        <div class="bg-white rounded-lg border border-gray-200 p-3.5 shadow-sm">
            <div class="flex flex-wrap items-center gap-2">
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $oldColor }}">
                    {{ $statusLabel($log->old_status) }}
                </span>
                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $newColor }}">
                    {{ $statusLabel($log->new_status) }}
                </span>
            </div>
            @if($log->reason)
            <p class="mt-2 text-sm text-gray-600 italic">{{ $log->reason }}</p>
            @endif
            <div class="mt-2 flex flex-wrap gap-x-3 text-xs text-gray-400">
                @if($log->changedBy)
                <span>por {{ $log->changedBy->name }}</span>
                @endif
                <span>{{ $log->created_at->format('d/m/Y H:i') }}</span>
                <span>{{ $log->created_at->diffForHumans() }}</span>
            </div>
        </div>
    </div>
    @empty
    <div class="text-center py-12 text-gray-400">
        <svg class="mx-auto w-10 h-10 text-gray-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="text-sm">No hay cambios de estado registrados.</p>
        <p class="text-xs mt-1 text-gray-300">Los cambios aparecen aquí al editar el estado del sistema.</p>
    </div>
    @endforelse
</div>
