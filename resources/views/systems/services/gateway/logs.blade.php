@extends('layouts.app')
@section('title', 'Log Gateway — ' . $service->service_name)

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('systems.services.show', [$system, $service]) }}"
           class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-900 dark:text-white">Log del Gateway</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $service->service_name }} — {{ $system->name }}</p>
        </div>
    </div>

    {{-- Estadísticas --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        @foreach([
            ['label' => 'Consultas hoy',  'value' => number_format($stats['total_today']),  'color' => 'blue'],
            ['label' => 'Esta semana',    'value' => number_format($stats['total_week']),    'color' => 'indigo'],
            ['label' => 'Errores hoy',    'value' => number_format($stats['errors_today']), 'color' => $stats['errors_today'] > 0 ? 'red' : 'gray'],
            ['label' => 'Tiempo prom.',   'value' => $stats['avg_ms'] . ' ms',              'color' => 'gray'],
        ] as $stat)
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 text-center shadow-sm">
            <p class="text-2xl font-bold text-{{ $stat['color'] }}-600 dark:text-{{ $stat['color'] }}-400">{{ $stat['value'] }}</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ $stat['label'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- Tabla de logs --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
            <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                Registros ({{ $logs->total() }})
            </h3>
        </div>

        @if($logs->isEmpty())
        <div class="px-6 py-12 text-center text-sm text-gray-400 dark:text-gray-500">
            No hay registros de actividad aún.
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30 text-gray-400 dark:text-gray-500">
                        <th class="px-4 py-3 text-left font-medium">Fecha / Hora</th>
                        <th class="px-4 py-3 text-left font-medium">Método</th>
                        <th class="px-4 py-3 text-left font-medium">Ruta</th>
                        <th class="px-4 py-3 text-left font-medium">IP</th>
                        <th class="px-4 py-3 text-left font-medium">Clave</th>
                        <th class="px-4 py-3 text-left font-medium">Status</th>
                        <th class="px-4 py-3 text-right font-medium">ms</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                    @foreach($logs as $log)
                    @php $scolor = $log->statusColor(); @endphp
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                        <td class="px-4 py-2.5 font-mono text-gray-400 dark:text-gray-500 whitespace-nowrap">
                            {{ $log->created_at->format('d/m/Y H:i:s') }}
                        </td>
                        <td class="px-4 py-2.5">
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-mono font-bold bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                                {{ $log->method }}
                            </span>
                        </td>
                        <td class="px-4 py-2.5 font-mono text-gray-500 dark:text-gray-400 max-w-[200px]">
                            <span class="truncate block" title="{{ $log->path_info }}{{ $log->query_string ? '?' . $log->query_string : '' }}">
                                /{{ $log->path_info }}{{ $log->query_string ? '?' . Str::limit($log->query_string, 40) : '' }}
                            </span>
                        </td>
                        <td class="px-4 py-2.5 font-mono text-gray-500 dark:text-gray-400 whitespace-nowrap">{{ $log->ip_address }}</td>
                        <td class="px-4 py-2.5 text-gray-500 dark:text-gray-400 max-w-[130px]">
                            <span class="truncate block" title="{{ $log->gatewayKey?->name }}">
                                {{ $log->gatewayKey?->name ?? '—' }}
                            </span>
                        </td>
                        <td class="px-4 py-2.5">
                            @if($log->response_status)
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-mono font-bold
                                         bg-{{ $scolor }}-100 dark:bg-{{ $scolor }}-900/40 text-{{ $scolor }}-700 dark:text-{{ $scolor }}-300">
                                {{ $log->response_status }}
                            </span>
                            @else
                            <span class="text-xs text-red-500">ERR</span>
                            @endif
                            @if($log->error_message)
                            <span class="ml-1 text-gray-400 dark:text-gray-500" title="{{ $log->error_message }}">⚠</span>
                            @endif
                        </td>
                        <td class="px-4 py-2.5 text-right font-mono text-gray-400 dark:text-gray-500">
                            {{ $log->response_time_ms ?? '—' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
            {{ $logs->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
