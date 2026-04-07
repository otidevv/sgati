@extends('layouts.app')

@section('title', 'Usuarios')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Gestión de Usuarios</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Cuentas de acceso al sistema</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
            Nuevo Usuario
        </a>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 px-3 py-2.5"><p class="text-[10px] font-medium text-gray-500 dark:text-gray-400 uppercase">Total</p><p class="mt-0.5 text-lg font-bold text-gray-900 dark:text-white">{{ $users->count() }}</p></div>
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 px-3 py-2.5"><p class="text-[10px] font-medium text-gray-500 dark:text-gray-400 uppercase">Activos</p><p class="mt-0.5 text-lg font-bold text-emerald-600 dark:text-emerald-400">{{ $users->where('is_active', true)->count() }}</p></div>
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 px-3 py-2.5"><p class="text-[10px] font-medium text-gray-500 dark:text-gray-400 uppercase">Inactivos</p><p class="mt-0.5 text-lg font-bold text-red-600 dark:text-red-400">{{ $users->where('is_active', false)->count() }}</p></div>
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 px-3 py-2.5"><p class="text-[10px] font-medium text-gray-500 dark:text-gray-400 uppercase">Sin Rol</p><p class="mt-0.5 text-lg font-bold text-gray-600 dark:text-gray-400">{{ $users->whereNull('role_id')->count() }}</p></div>
    </div>

    <div class="flex items-start gap-3 p-3 sm:p-4 bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800 rounded-lg">
        <svg class="w-5 h-5 text-blue-500 dark:text-blue-400 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        <div><p class="text-sm font-medium text-blue-900 dark:text-blue-200">Requisito previo</p><p class="mt-0.5 text-sm text-blue-700 dark:text-blue-300">Para crear un usuario, primero registra a la persona en <a href="{{ route('admin.personas.index') }}" class="underline font-semibold hover:text-blue-900 dark:hover:text-blue-100">Personas</a>.</p></div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table id="users-table" class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700/50"><tr class="bg-gray-50 dark:bg-gray-700/50"><th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Usuario</th><th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Email</th><th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Rol</th><th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Área</th><th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Estado</th><th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Acciones</th></tr></thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                    @forelse($users as $user)
                    <tr class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td><div class="flex items-center gap-3"><div class="flex-shrink-0 w-9 h-9 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-semibold text-xs">{{ strtoupper(substr($user->name, 0, 2)) }}</div><div class="min-w-0"><p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $user->name }}</p>@if($user->persona)<p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $user->persona->nombre_corto }}</p>@endif</div></div></td>
                        <td><span class="text-sm text-gray-700 dark:text-gray-300">{{ $user->email }}</span></td>
                        <td>@if($user->role)<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300">{{ $user->role->label }}</span>@else<span class="text-gray-400 dark:text-gray-500 text-xs">—</span>@endif</td>
                        <td><span class="text-sm text-gray-700 dark:text-gray-300">{{ $user->area->name ?? '—' }}</span></td>
                        <td class="text-center">@if($user->is_active)<span class="inline-flex items-center gap-1 text-xs text-emerald-700 dark:text-emerald-400"><span class="w-2 h-2 rounded-full bg-emerald-500 dark:bg-emerald-400"></span>Activo</span>@else<span class="inline-flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400"><span class="w-2 h-2 rounded-full bg-gray-400 dark:bg-gray-500"></span>Inactivo</span>@endif</td>
                        <td class="text-right"><div class="inline-flex items-center gap-1"><a href="{{ route('admin.users.edit', $user) }}" class="inline-flex items-center justify-center w-7 h-7 rounded text-gray-400 dark:text-gray-500 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-all"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg></a>@if($user->id !== auth()->id())<form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline">@csrf @method('DELETE')<button type="button" x-data @click.prevent="if(confirm('¿Eliminar este usuario?')) $el.closest('form').submit()" class="inline-flex items-center justify-center w-7 h-7 rounded text-gray-400 dark:text-gray-500 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 transition-all"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg></button></form>@endif</div></td>
                    </tr>
                    @empty
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->isEmpty())
        <div class="text-center py-16 bg-white dark:bg-gray-800"><div class="mx-auto w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center"><svg class="w-8 h-8 text-gray-300 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg></div><h3 class="mt-4 text-sm font-semibold text-gray-900 dark:text-white">No hay usuarios registrados</h3><p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Primero registra personas, luego crea sus cuentas.</p><div class="mt-6"><a href="{{ route('admin.personas.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors shadow-sm"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>Registrar Persona</a></div></div>
        @endif
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const table = document.getElementById('users-table');
    if (table && table.rows.length > 1) {
        new DataTable(table, {
            pageLength: 10,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'Todos']],
            order: [[0, 'asc']],
            autoWidth: false,
            columnDefs: [{ orderable: false, targets: [5] }],
            language: {
                search: '',
                searchPlaceholder: 'Buscar usuario…',
                lengthMenu: 'Mostrar _MENU_ filas',
                info: '_START_–_END_ de _TOTAL_',
                infoEmpty: 'Sin resultados',
                infoFiltered: '(filtrado de _MAX_)',
                zeroRecords: 'No se encontraron resultados',
                emptyTable: 'No hay usuarios registrados',
                paginate: { first: '«', last: '»', next: '›', previous: '‹' },
            },
        });
    }
});
</script>
@endpush

@push('styles')
<style>
/* DataTables Dark Mode Support */
.dark .dataTables_wrapper .dataTables_length,
.dark .dataTables_wrapper .dataTables_filter,
.dark .dataTables_wrapper .dataTables_info,
.dark .dataTables_wrapper .dataTables_processing,
.dark .dataTables_wrapper .dataTables_paginate {
    color: #9ca3af;
}

.dark .dataTables_wrapper .dataTables_paginate .paginate_button {
    color: #9ca3af !important;
}

.dark .dataTables_wrapper .dataTables_paginate .paginate_button.current,
.dark .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
    background: #374151 !important;
    color: #ffffff !important;
    border-color: #4b5563 !important;
}

.dark .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    background: #4b5563 !important;
    color: #ffffff !important;
    border-color: #6b7280 !important;
}

.dark .dataTables_wrapper .dataTables_filter input {
    background-color: #374151;
    border-color: #4b5563;
    color: #e5e7eb;
}

.dark .dataTables_wrapper .dataTables_filter input::placeholder {
    color: #9ca3af;
}

.dark .dataTables_wrapper .dataTables_length select {
    background-color: #374151;
    border-color: #4b5563;
    color: #e5e7eb;
}

.dark table.dataTable tbody tr {
    background-color: #1f2937;
}

.dark table.dataTable tbody tr:hover {
    background-color: #374151 !important;
}

.dark table.dataTable tbody td {
    color: #e5e7eb;
}
</style>
@endpush
@endsection
