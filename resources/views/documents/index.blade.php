@extends('layouts.app')
@section('title', 'Repositorio de Documentos')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Documentos</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Repositorio general de documentos de todos los sistemas</p>
        </div>
        <span class="text-sm text-gray-500 dark:text-gray-400">{{ $documents->total() }} documento{{ $documents->total() !== 1 ? 's' : '' }}</span>
    </div>

    {{-- Filtros --}}
    <form method="GET" class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-4 flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-48">
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Buscar</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Título del documento…"
                   class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
        </div>
        <div class="w-48">
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Tipo</label>
            <select name="doc_type"
                    class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                <option value="">Todos los tipos</option>
                @foreach($docTypes as $dt)
                <option value="{{ $dt->value }}" {{ request('doc_type') === $dt->value ? 'selected' : '' }}>{{ $dt->label() }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                Filtrar
            </button>
            @if(request('search') || request('doc_type'))
            <a href="{{ route('documents.repository') }}"
               class="inline-flex items-center gap-1 px-3 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Limpiar
            </a>
            @endif
        </div>
    </form>

    {{-- Tabla de documentos --}}
    @if($documents->isEmpty())
    <div class="text-center py-20 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
        <div class="p-3 bg-gray-100 dark:bg-gray-700 rounded-full w-fit mx-auto mb-4">
            <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
        </div>
        <h4 class="text-base font-semibold text-gray-700 dark:text-gray-300 mb-1">Sin documentos</h4>
        <p class="text-sm text-gray-500 dark:text-gray-400">No se encontraron documentos con los filtros aplicados.</p>
    </div>
    @else
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            @foreach($documents as $doc)
            @php
            $typeIcon = match($doc->doc_type->value) {
                'manual_user', 'manual_technical' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                'diagram' => 'M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01',
                default   => 'M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z',
            };
            $sizeKb = $doc->file_size ? round($doc->file_size / 1024, 1) : null;
            @endphp
            <div class="flex items-center gap-4 px-5 py-3.5 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                {{-- Icono --}}
                <div class="flex-shrink-0 w-9 h-9 rounded-lg bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center text-blue-500 dark:text-blue-400">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $typeIcon }}"/>
                    </svg>
                </div>

                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $doc->title }}</p>
                    <div class="flex flex-wrap gap-x-3 gap-y-0.5 mt-0.5 text-xs text-gray-400 dark:text-gray-500">
                        @if($doc->system)
                        <a href="{{ route('systems.show', $doc->system) }}"
                           class="text-blue-600 dark:text-blue-400 hover:underline font-medium">
                            {{ $doc->system->acronym ?? $doc->system->name }}
                        </a>
                        @endif
                        <span class="text-indigo-600 dark:text-indigo-400">{{ $doc->doc_type->label() }}</span>
                        @if($doc->doc_number)<span>{{ $doc->doc_number }}</span>@endif
                        @if($doc->issue_date)<span>{{ $doc->issue_date->format('d/m/Y') }}</span>@endif
                        @if($sizeKb)<span>{{ $sizeKb }} KB</span>@endif
                    </div>
                </div>

                {{-- Fecha subida + acciones --}}
                <div class="flex items-center gap-2 flex-shrink-0">
                    <span class="hidden sm:block text-xs text-gray-400 dark:text-gray-500">
                        {{ $doc->created_at->format('d/m/Y') }}
                    </span>
                    @can('documents.download')
                    <a href="{{ route('systems.documents.download', [$doc->system, $doc]) }}"
                       class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-400 dark:text-gray-500 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-all"
                       title="Descargar">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                    </a>
                    @endcan
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Paginación --}}
    @if($documents->hasPages())
    <div class="flex justify-center">
        {{ $documents->links() }}
    </div>
    @endif
    @endif

</div>
@endsection
