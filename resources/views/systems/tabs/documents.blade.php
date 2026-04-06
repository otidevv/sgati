<div class="space-y-5">
    <div class="flex items-center justify-between">
        <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">
            Documentos ({{ $system->documents->count() }})
        </h3>
    </div>

    {{-- Formulario de subida --}}
    @can('documents.upload')
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4" x-data="{ open: false }">
        <button @click="open = !open" type="button"
                class="flex items-center gap-2 text-sm font-medium text-blue-700 hover:text-blue-800 transition-colors w-full">
            <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-45' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <span x-text="open ? 'Cancelar' : 'Subir nuevo documento'"></span>
        </button>

        <div x-show="open" x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
             class="mt-4">
            <form action="{{ route('systems.documents.store', $system) }}" method="POST" enctype="multipart/form-data"
                  class="space-y-3">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-blue-800 mb-1">Título <span class="text-red-500">*</span></label>
                        <input type="text" name="title" required placeholder="Nombre del documento"
                               class="block w-full rounded-lg border-blue-200 bg-white shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-blue-800 mb-1">Tipo <span class="text-red-500">*</span></label>
                        <select name="doc_type" required class="block w-full rounded-lg border-blue-200 bg-white shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">Seleccionar…</option>
                            <option value="manual_user">Manual de usuario</option>
                            <option value="manual_technical">Manual técnico</option>
                            <option value="oficio">Oficio</option>
                            <option value="resolution">Resolución</option>
                            <option value="acta">Acta</option>
                            <option value="contract">Contrato</option>
                            <option value="diagram">Diagrama</option>
                            <option value="other">Otro</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-blue-800 mb-1">N° Documento</label>
                        <input type="text" name="doc_number" placeholder="Ej: OFI-2024-001"
                               class="block w-full rounded-lg border-blue-200 bg-white shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-blue-800 mb-1">Fecha de Emisión</label>
                        <input type="date" name="issue_date"
                               class="block w-full rounded-lg border-blue-200 bg-white shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-blue-800 mb-1">Archivo <span class="text-red-500">*</span></label>
                    <input type="file" name="file" required accept=".pdf,.doc,.docx,.xls,.xlsx,.png,.jpg,.jpeg"
                           class="block w-full text-sm text-blue-700 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-blue-100 file:text-blue-700 hover:file:bg-blue-200 cursor-pointer">
                    <p class="mt-1 text-xs text-blue-600/70">PDF, Word, Excel, imágenes — máx. 50 MB</p>
                </div>
                <div class="flex justify-end">
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        Subir Documento
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endcan

    {{-- Lista de documentos --}}
    @forelse($system->documents as $doc)
    @php
    $typeIcon = match($doc->doc_type->value) {
        'manual_user', 'manual_technical' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        'diagram'  => 'M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01',
        default    => 'M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z',
    };
    $sizeKb = $doc->file_size ? round($doc->file_size / 1024, 1) : null;
    @endphp
    <div class="bg-white rounded-lg border border-gray-200 p-3.5 flex items-start gap-3 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex-shrink-0 w-9 h-9 rounded-lg bg-gray-100 flex items-center justify-center text-gray-500">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $typeIcon }}"/>
            </svg>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-sm font-medium text-gray-900 truncate">{{ $doc->title }}</p>
            <div class="flex flex-wrap gap-x-3 gap-y-0.5 mt-0.5 text-xs text-gray-400">
                <span class="text-blue-600">{{ $doc->doc_type->label() }}</span>
                @if($doc->doc_number)<span>{{ $doc->doc_number }}</span>@endif
                @if($doc->issue_date)<span>{{ $doc->issue_date->format('d/m/Y') }}</span>@endif
                @if($sizeKb)<span>{{ $sizeKb }} KB</span>@endif
                @if($doc->uploadedBy)<span>↑ {{ $doc->uploadedBy->name }}</span>@endif
            </div>
        </div>
        <div class="flex items-center gap-1 flex-shrink-0">
            @can('documents.download')
            <a href="{{ route('systems.documents.download', [$system, $doc]) }}"
               class="inline-flex items-center justify-center w-7 h-7 rounded text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition-all"
               title="Descargar">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
            </a>
            @endcan
            @can('documents.delete')
            <form action="{{ route('systems.documents.destroy', [$system, $doc]) }}" method="POST" class="inline">
                @csrf @method('DELETE')
                <button type="button" x-data @click.prevent="if(confirm('¿Eliminar el documento?')) $el.closest('form').submit()"
                        class="inline-flex items-center justify-center w-7 h-7 rounded text-gray-400 hover:text-red-600 hover:bg-red-50 transition-all"
                        title="Eliminar">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
            </form>
            @endcan
        </div>
    </div>
    @empty
    <div class="text-center py-12 text-gray-400">
        <svg class="mx-auto w-10 h-10 text-gray-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        <p class="text-sm">No hay documentos adjuntos.</p>
    </div>
    @endforelse
</div>
