@extends('layouts.app')

@section('title', 'Áreas')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Gestión de Áreas</h1>
            <p class="mt-1 text-sm text-gray-500">Unidades organizativas de UNAMAD</p>
        </div>
        <a href="{{ route('admin.areas.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
            Nueva Área
        </a>
    </div>

    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="w-full">
            <table id="areas-table" class="min-w-full divide-y divide-gray-200" style="width:100%">
                <thead class="bg-gray-50"><tr><th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Área</th><th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Siglas</th><th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descripción</th><th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Sistemas</th><th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th></tr></thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($areas as $area)
                    <tr>
                        <td><div class="flex items-center gap-3"><div class="flex-shrink-0 w-9 h-9 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold text-xs">{{ strtoupper(substr($area->acronym ?? $area->name, 0, 2)) }}</div><p class="text-sm font-medium text-gray-900">{{ $area->name }}</p></div></td>
                        <td><span class="text-sm font-semibold text-gray-700">{{ $area->acronym ?? '—' }}</span></td>
                        <td><span class="text-sm text-gray-500 truncate max-w-xs block">{{ $area->description ?? '—' }}</span></td>
                        <td class="text-center"><span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-blue-50 text-blue-700 text-xs font-semibold">{{ $area->systems_count }}</span></td>
                        <td class="text-right"><div class="inline-flex items-center gap-1"><a href="{{ route('admin.areas.edit', $area) }}" class="inline-flex items-center justify-center w-7 h-7 rounded text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition-all"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg></a><form action="{{ route('admin.areas.destroy', $area) }}" method="POST" class="inline">@csrf @method('DELETE')<button type="button" x-data @click.prevent="if(confirm('¿Eliminar esta área?')) $el.closest('form').submit()" class="inline-flex items-center justify-center w-7 h-7 rounded text-gray-400 hover:text-red-600 hover:bg-red-50 transition-all"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg></button></form></div></td>
                    </tr>
                    @empty
                    @endforelse
                </tbody>
            </table>
            @if($areas->isEmpty())
            <div class="text-center py-16"><div class="mx-auto w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center"><svg class="w-8 h-8 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg></div><h3 class="mt-4 text-sm font-semibold text-gray-900">No hay áreas registradas</h3><p class="mt-1 text-sm text-gray-500">Comienza registrando las unidades organizativas.</p><div class="mt-6"><a href="{{ route('admin.areas.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors shadow-sm"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>Registrar primera área</a></div></div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const table = document.getElementById('areas-table');
    if (table && table.rows.length > 1) {
        new DataTable(table, {
            pageLength: 10,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'Todos']],
            order: [[0, 'asc']],
            autoWidth: false,
            columnDefs: [{ orderable: false, targets: [4] }],
            language: {
                search: '',
                searchPlaceholder: 'Buscar área…',
                lengthMenu: 'Mostrar _MENU_ filas',
                info: '_START_–_END_ de _TOTAL_',
                infoEmpty: 'Sin resultados',
                infoFiltered: '(filtrado de _MAX_)',
                zeroRecords: 'No se encontraron resultados',
                emptyTable: 'No hay áreas registradas',
                paginate: { first: '«', last: '»', next: '›', previous: '‹' },
            },
        });
    }
});
</script>
@endpush
@endsection
