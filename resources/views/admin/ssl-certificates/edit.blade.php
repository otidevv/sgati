@extends('layouts.app')

@section('title', 'Editar — ' . $sslCertificate->name)

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <div class="flex items-center gap-4">
        <a href="{{ route('admin.ssl-certificates.show', $sslCertificate) }}"
           class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-700
                  text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Editar Certificado SSL</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $sslCertificate->name }}</p>
        </div>
    </div>

    <form action="{{ route('admin.ssl-certificates.update', $sslCertificate) }}" method="POST"
          enctype="multipart/form-data" class="space-y-5">
        @csrf @method('PUT')

        @include('admin.ssl-certificates._form', ['cert' => $sslCertificate])

        {{-- Gestión de archivos existentes --}}
        @php
            $fileFields = [
                ['field' => 'cert_file_path',  'label' => 'Certificado (.crt / .pem)', 'key' => 'cert',  'input' => 'cert_file',  'color' => 'blue'],
                ['field' => 'key_file_path',   'label' => 'Llave privada (.key)',       'key' => 'key',   'input' => 'key_file',   'color' => 'amber'],
                ['field' => 'chain_file_path', 'label' => 'Cadena intermedia',          'key' => 'chain', 'input' => 'chain_file', 'color' => 'purple'],
            ];
        @endphp

        @foreach($fileFields as $ff)
        @if($sslCertificate->{$ff['field']})
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/30 flex items-center justify-between">
                <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-200">{{ $ff['label'] }} — actual</h2>
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium
                             bg-{{ $ff['color'] }}-100 dark:bg-{{ $ff['color'] }}-900/30
                             text-{{ $ff['color'] }}-700 dark:text-{{ $ff['color'] }}-300">
                    Archivo cargado
                </span>
            </div>
            <div class="p-4 flex items-center justify-between">
                <p class="text-sm text-gray-600 dark:text-gray-400 font-mono truncate">
                    {{ basename($sslCertificate->{$ff['field']}) }}
                </p>
                <label class="flex items-center gap-2 text-sm text-red-600 dark:text-red-400 cursor-pointer hover:text-red-700 dark:hover:text-red-300">
                    <input type="checkbox" name="remove_{{ $ff['key'] }}_file" value="1" class="rounded border-gray-300 dark:border-gray-600">
                    Eliminar archivo
                </label>
            </div>
        </div>
        @endif
        @endforeach

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.ssl-certificates.show', $sslCertificate) }}"
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
                Guardar Cambios
            </button>
        </div>
    </form>

    {{-- Zona de peligro --}}
    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-5">
        <h3 class="text-sm font-semibold text-red-800 dark:text-red-300 mb-2">Zona de peligro</h3>
        <p class="text-xs text-red-600 dark:text-red-400 mb-3">
            Eliminar el certificado borrará también los archivos almacenados y desvinculará los sistemas que lo referencian.
        </p>
        <form action="{{ route('admin.ssl-certificates.destroy', $sslCertificate) }}" method="POST"
              onsubmit="return confirm('¿Eliminar este certificado SSL y todos sus archivos?')">
            @csrf @method('DELETE')
            <button type="submit"
                    class="px-4 py-2 text-sm font-medium text-red-700 dark:text-red-300
                           bg-red-100 dark:bg-red-900/40 hover:bg-red-200 dark:hover:bg-red-900/60
                           border border-red-300 dark:border-red-700 rounded-lg transition-colors">
                Eliminar certificado
            </button>
        </form>
    </div>
</div>
@endsection
