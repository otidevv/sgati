@extends('layouts.app')

@section('title', 'Personas')

@section('content')
<div class="space-y-6">
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Gestión de Personas</h1>
            <p class="mt-1 text-sm text-gray-500">Datos civiles del personal</p>
        </div>
        <a href="{{ route('admin.personas.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Nueva Persona
        </a>
    </div>

    {{-- Stats Bar --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg border border-gray-200 px-4 py-3">
            <p class="text-xs font-medium text-gray-500 uppercase">Total</p>
            <p class="mt-0.5 text-xl font-bold text-gray-900">{{ $personas->total() }}</p>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 px-4 py-3">
            <p class="text-xs font-medium text-gray-500 uppercase">Con Usuario</p>
            <p class="mt-0.5 text-xl font-bold text-emerald-600">{{ $personas->where('user', '!=', null)->count() }}</p>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 px-4 py-3">
            <p class="text-xs font-medium text-gray-500 uppercase">Sin Usuario</p>
            <p class="mt-0.5 text-xl font-bold text-amber-600">{{ $personas->where('user', null)->count() }}</p>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 px-4 py-3">
            <p class="text-xs font-medium text-gray-500 uppercase">Página</p>
            <p class="mt-0.5 text-xl font-bold text-gray-900">{{ $personas->currentPage() }}</p>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Persona</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">DNI</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contacto</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Sexo</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($personas as $persona)
                    <tr class="hover:bg-gray-50 transition-colors group">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <div class="flex-shrink-0 w-9 h-9 rounded-lg bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 flex items-center justify-center text-white font-semibold text-xs">
                                    {{ strtoupper(substr($persona->nombres, 0, 1) . substr($persona->apellido_paterno, 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $persona->nombre_completo }}</p>
                                    @if($persona->fecha_nacimiento)
                                    <p class="text-xs text-gray-500">{{ $persona->fecha_nacimiento->format('d/m/Y') }} ({{ $persona->fecha_nacimiento->age }} años)</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-sm font-mono text-gray-700">{{ $persona->dni }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="text-sm">
                                @if($persona->email_personal)
                                <p class="text-gray-700 truncate max-w-[180px]">{{ $persona->email_personal }}</p>
                                @endif
                                @if($persona->telefono)
                                <p class="text-gray-500 text-xs">{{ $persona->telefono }}</p>
                                @endif
                                @if(!$persona->email_personal && !$persona->telefono)
                                <span class="text-gray-400 text-xs">—</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($persona->sexo)
                            <span class="inline-flex items-center justify-center w-7 h-7 rounded {{ $persona->sexo === 'M' ? 'bg-blue-100 text-blue-700' : 'bg-pink-100 text-pink-700' }} text-xs font-semibold">
                                {{ $persona->sexo }}
                            </span>
                            @else
                            <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($persona->user)
                            <span class="inline-flex items-center gap-1 text-xs text-emerald-700">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                Vinculado
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1 text-xs text-amber-700">
                                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                Sin cuenta
                            </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="inline-flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                <a href="{{ route('admin.personas.edit', $persona) }}" class="inline-flex items-center justify-center w-7 h-7 rounded text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition-all" title="Editar">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </a>
                                @if(!$persona->user)
                                <form action="{{ route('admin.personas.destroy', $persona) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="button" x-data @click.prevent="if(confirm('¿Eliminar esta persona?')) $el.closest('form').submit()" class="inline-flex items-center justify-center w-7 h-7 rounded text-gray-400 hover:text-red-600 hover:bg-red-50 transition-all" title="Eliminar">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16">
                            <div class="text-center max-w-md mx-auto">
                                <div class="mx-auto w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center">
                                    <svg class="w-8 h-8 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <h3 class="mt-4 text-sm font-semibold text-gray-900">No hay personas registradas</h3>
                                <p class="mt-1 text-sm text-gray-500">Comienza registrando los datos civiles del personal.</p>
                                <div class="mt-6">
                                    <a href="{{ route('admin.personas.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                        Registrar primera persona
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($personas->hasPages())
        <div class="px-4 py-3 border-t border-gray-200 bg-gray-50">
            {{ $personas->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
