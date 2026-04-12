@extends('layouts.app')

@section('title', 'Nuevo Certificado SSL')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <div class="flex items-center gap-4">
        <a href="{{ route('admin.ssl-certificates.index') }}"
           class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-700
                  text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Nuevo Certificado SSL</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Registrar un certificado en el repositorio de la OTI</p>
        </div>
    </div>

    <form action="{{ route('admin.ssl-certificates.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf

        @include('admin.ssl-certificates._form')

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.ssl-certificates.index') }}"
               class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700
                      border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                Cancelar
            </a>
            <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2 text-sm font-medium text-white
                           bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Registrar Certificado
            </button>
        </div>
    </form>
</div>
@endsection
