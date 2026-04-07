@extends('layouts.app')
@section('title', 'Editar Versión')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('systems.show', $system) }}"
           class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Editar Versión <span class="font-mono text-blue-600 dark:text-blue-400">v{{ $version->version }}</span></h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $system->name }}</p>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
        <form action="{{ route('systems.versions.update', [$system, $version]) }}" method="POST" class="p-6 space-y-5">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="version" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Versión <span class="text-red-500">*</span></label>
                    <input type="text" id="version" name="version" value="{{ old('version', $version->version) }}" required
                           class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm font-mono">
                    @error('version')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="release_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fecha <span class="text-red-500">*</span></label>
                    <input type="date" id="release_date" name="release_date"
                           value="{{ old('release_date', $version->release_date?->format('Y-m-d')) }}" required
                           class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    @error('release_date')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="environment" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Ambiente <span class="text-red-500">*</span></label>
                    <select id="environment" name="environment" required
                            class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="production"  {{ old('environment', $version->environment?->value) === 'production'  ? 'selected' : '' }}>Producción</option>
                        <option value="staging"     {{ old('environment', $version->environment?->value) === 'staging'     ? 'selected' : '' }}>Staging</option>
                        <option value="development" {{ old('environment', $version->environment?->value) === 'development' ? 'selected' : '' }}>Desarrollo</option>
                    </select>
                    @error('environment')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                </div>
            </div>
            <div>
                <label for="changes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cambios</label>
                <textarea id="changes" name="changes" rows="4"
                          class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">{{ old('changes', $version->changes) }}</textarea>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="git_branch" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Branch</label>
                    <input type="text" id="git_branch" name="git_branch" value="{{ old('git_branch', $version->git_branch) }}"
                           class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm font-mono">
                </div>
                <div>
                    <label for="git_commit" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Commit Hash</label>
                    <input type="text" id="git_commit" name="git_commit" value="{{ old('git_commit', $version->git_commit) }}"
                           class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm font-mono">
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-100 dark:border-gray-700">
                <a href="{{ route('systems.show', $system) }}"
                   class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">Cancelar</a>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
