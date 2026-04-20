@extends('layouts.app')
@section('title', 'Nuevo Repositorio — ' . $system->name)

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('systems.show', $system) }}"
           class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Nuevo Repositorio</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $system->name }}</p>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
        <form id="repo-create-form" action="{{ route('systems.repositories.store', $system) }}" method="POST" class="p-6 space-y-5">
            @csrf
            @include('systems.repositories._form', ['repository' => null])
            <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-100 dark:border-gray-700">
                <a href="{{ route('systems.show', $system) }}"
                   class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">Cancelar</a>
                <button id="repo-create-btn" type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                    <svg id="repo-create-icon" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <svg id="repo-create-spinner" class="hidden w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/>
                    </svg>
                    <span id="repo-create-label">Registrar Repositorio</span>
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
(function () {
    const form    = document.getElementById('repo-create-form');
    const btn     = document.getElementById('repo-create-btn');
    const icon    = document.getElementById('repo-create-icon');
    const spinner = document.getElementById('repo-create-spinner');
    const label   = document.getElementById('repo-create-label');
    let submitted = false;

    form.addEventListener('submit', function (e) {
        if (submitted) { e.preventDefault(); return; }
        if (!form.checkValidity()) return;
        submitted = true;
        btn.classList.add('pointer-events-none', 'opacity-75');
        icon.classList.add('hidden');
        spinner.classList.remove('hidden');
        label.textContent = 'Registrando…';
    });
})();
</script>
@endpush
@endsection
