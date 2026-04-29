@php
$fieldLabels = [
    'name' => 'Nombre', 'acronym' => 'Siglas', 'description' => 'Descripción',
    'area_id' => 'Área', 'responsible_id' => 'Responsable',
    'tech_stack' => 'Stack tecnológico', 'observations' => 'Observaciones',
    'repo_url' => 'URL repositorio', 'version' => 'Versión',
    'release_date' => 'Fecha de lanzamiento', 'environment' => 'Entorno',
    'changes' => 'Cambios', 'git_commit' => 'Commit', 'git_branch' => 'Rama',
    'notes' => 'Notas', 'provider' => 'Proveedor', 'provider_other' => 'Proveedor (otro)',
    'default_branch' => 'Rama principal', 'is_private' => 'Privado',
    'is_active' => 'Activo', 'service_name' => 'Nombre del servicio',
    'endpoint_url' => 'Endpoint', 'direction' => 'Dirección',
    'auth_type' => 'Autenticación', 'system_url' => 'URL del sistema',
    'port' => 'Puerto', 'ssl_enabled' => 'SSL', 'web_server' => 'Servidor web',
    'public_ip' => 'IP pública', 'server_id' => 'Servidor',
    'db_name' => 'Base de datos', 'engine' => 'Motor', 'schema_name' => 'Esquema',
    'db_user' => 'Usuario BD', 'connection_type' => 'Tipo de conexión',
    'level' => 'Nivel/Rol', 'assigned_at' => 'Asignado el',
    'unassigned_at' => 'Desasignado el', 'persona_id' => 'Persona',
    'is_active' => 'Activo', 'gateway_enabled' => 'Gateway habilitado',
    'gateway_rate_per_minute' => 'Límite por minuto', 'gateway_rate_per_day' => 'Límite diario',
];

$formatValue = function($val) {
    if (is_null($val))          return '—';
    if (is_bool($val))          return $val ? 'Sí' : 'No';
    if (is_array($val))         return implode(', ', $val) ?: '—';
    $str = (string) $val;
    return mb_strlen($str) > 70 ? mb_substr($str, 0, 70) . '…' : $str;
};
@endphp

<div class="space-y-8">

    {{-- ── Actividad General ─────────────────────────────────────── --}}
    <div>
        <h3 class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-4">
            Actividad General ({{ $system->activityLogs->count() }})
        </h3>

        @forelse($system->activityLogs as $log)
        @php
        [$dotColor, $iconColor, $eventLabel] = match($log->event) {
            'creado'      => ['bg-green-400',  'text-green-600 dark:text-green-400',  'Registró'],
            'eliminado'   => ['bg-red-400',    'text-red-600 dark:text-red-400',      'Eliminó'],
            default       => ['bg-blue-400',   'text-blue-600 dark:text-blue-400',    'Actualizó'],
        };
        @endphp
        <div class="relative pl-6 pb-4 border-l-2 border-gray-200 dark:border-gray-700 last:border-l-transparent last:pb-0">
            <span class="absolute -left-[5px] top-1 w-2.5 h-2.5 rounded-full {{ $dotColor }}"></span>
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-3 shadow-sm">
                <div class="flex items-center justify-between gap-2 flex-wrap">
                    <p class="text-sm text-gray-700 dark:text-gray-200">
                        <span class="{{ $iconColor }} font-semibold">{{ $eventLabel }}</span>
                        <span class="font-medium"> {{ $log->subject_type }}</span>
                    </p>
                    <div class="flex items-center gap-2 text-xs text-gray-400 dark:text-gray-500">
                        @if($log->causer)
                        <span>{{ $log->causer->name }}</span>
                        <span>·</span>
                        @endif
                        <span title="{{ $log->created_at->format('d/m/Y H:i:s') }}">
                            {{ $log->created_at->diffForHumans() }}
                        </span>
                    </div>
                </div>

                @if($log->event === 'actualizado' && $log->properties)
                <dl class="mt-2 space-y-0.5">
                    @foreach($log->properties as $field => $change)
                    <div class="flex flex-wrap gap-x-1.5 text-xs text-gray-500 dark:text-gray-400">
                        <dt class="font-medium text-gray-600 dark:text-gray-300 shrink-0">
                            {{ $fieldLabels[$field] ?? $field }}:
                        </dt>
                        <dd class="line-through text-gray-400">{{ $formatValue($change['old']) }}</dd>
                        <dd class="text-gray-400">→</dd>
                        <dd class="text-gray-700 dark:text-gray-200">{{ $formatValue($change['new']) }}</dd>
                    </div>
                    @endforeach
                </dl>
                @endif
            </div>
        </div>
        @empty
        <div class="text-center py-8 text-gray-400 dark:text-gray-500">
            <svg class="mx-auto w-8 h-8 text-gray-300 dark:text-gray-600 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <p class="text-sm">Sin actividad registrada aún.</p>
        </div>
        @endforelse
    </div>

    {{-- ── Historial de Estado ────────────────────────────────────── --}}
    <div>
        <h3 class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-4">
            Historial de Estado ({{ $system->statusLogs->count() }})
        </h3>

        @forelse($system->statusLogs as $log)
        @php
        $color = fn(string $s) => match($s) {
            'active'      => 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300',
            'development' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300',
            'maintenance' => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300',
            'inactive'    => 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-300',
            default       => 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300',
        };
        $label = fn(string $s) => match($s) {
            'active'      => 'Activo',
            'development' => 'En desarrollo',
            'maintenance' => 'Mantenimiento',
            'inactive'    => 'Inactivo',
            default       => ucfirst($s),
        };
        @endphp
        <div class="relative pl-6 pb-4 border-l-2 border-gray-200 dark:border-gray-700 last:border-l-transparent last:pb-0">
            <span class="absolute -left-[9px] top-0 w-4 h-4 rounded-full bg-white dark:bg-gray-800 border-2 border-gray-300 dark:border-gray-600 flex items-center justify-center">
                <span class="w-1.5 h-1.5 rounded-full bg-gray-400 dark:bg-gray-500"></span>
            </span>
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-3.5 shadow-sm">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $color($log->old_status) }}">
                        {{ $label($log->old_status) }}
                    </span>
                    <svg class="w-3.5 h-3.5 text-gray-400 dark:text-gray-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $color($log->new_status) }}">
                        {{ $label($log->new_status) }}
                    </span>
                </div>
                @if($log->reason)
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300 italic">{{ $log->reason }}</p>
                @endif
                <div class="mt-2 flex flex-wrap gap-x-3 text-xs text-gray-400 dark:text-gray-500">
                    @if($log->changedBy)
                    <span>por {{ $log->changedBy->name }}</span>
                    @endif
                    <span>{{ $log->created_at->format('d/m/Y H:i') }}</span>
                    <span>{{ $log->created_at->diffForHumans() }}</span>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-8 text-gray-400 dark:text-gray-500">
            <svg class="mx-auto w-8 h-8 text-gray-300 dark:text-gray-600 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-sm">No hay cambios de estado registrados.</p>
        </div>
        @endforelse
    </div>

</div>
