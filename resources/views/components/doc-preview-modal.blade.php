{{--
    Componente: doc-preview-modal
    ─────────────────────────────
    Modal reutilizable para previsualizar documentos (PDF, imágenes) o
    mostrar un fallback con botón de descarga para otros formatos.

    Uso en cualquier vista:
        <x-doc-preview-modal />

    Invocar desde JS:
        openDocPreview({
            name:        'archivo.pdf',          // nombre a mostrar
            description: 'Descripción opcional', // puede ser null / ''
            previewUrl:  '{{ route(...) }}',      // URL del método preview
            downloadUrl: '{{ route(...) }}',      // URL del método download
            ext:         'pdf',                  // extensión del archivo
        });
--}}

<div id="modal-doc-preview"
     class="hidden fixed inset-0 z-[70] items-center justify-center p-4"
     role="dialog" aria-modal="true" aria-labelledby="dp-name">

    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm transition-opacity"
         onclick="closeDocPreview()"></div>

    {{-- Panel --}}
    <div class="relative bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-4xl flex flex-col overflow-hidden
                transition-all duration-200"
         style="height: min(90vh, 820px)">

        {{-- ── Header ─────────────────────────────────────────── --}}
        <div class="flex items-center gap-3 px-5 py-3.5 border-b border-gray-200 dark:border-gray-700 flex-shrink-0">

            {{-- Ícono dinámico por tipo --}}
            <div id="dp-icon"
                 class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0
                        bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
            </div>

            {{-- Nombre + descripción --}}
            <div class="flex-1 min-w-0">
                <p id="dp-name"
                   class="text-sm font-semibold text-gray-800 dark:text-gray-100 truncate leading-tight"></p>
                <p id="dp-description"
                   class="text-xs text-gray-400 dark:text-gray-500 truncate mt-0.5 hidden"></p>
            </div>

            {{-- Acciones --}}
            <div class="flex items-center gap-2 flex-shrink-0">
                {{-- Abrir en nueva pestaña --}}
                <a id="dp-open-tab" href="#" target="_blank" rel="noopener"
                   title="Abrir en nueva pestaña"
                   class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 dark:text-gray-500
                          hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                </a>

                {{-- Descargar --}}
                <a id="dp-download" href="#"
                   title="Descargar"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-white
                          bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600
                          rounded-lg transition-colors shadow-sm">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Descargar
                </a>

                {{-- Cerrar --}}
                <button onclick="closeDocPreview()"
                        title="Cerrar"
                        class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 dark:text-gray-500
                               hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- ── Cuerpo ───────────────────────────────────────────── --}}
        <div class="flex-1 overflow-hidden bg-gray-100 dark:bg-gray-800 relative">

            {{-- Spinner de carga --}}
            <div id="dp-loading"
                 class="absolute inset-0 flex items-center justify-center bg-gray-100 dark:bg-gray-800 z-10">
                <svg class="w-9 h-9 animate-spin text-indigo-500" xmlns="http://www.w3.org/2000/svg"
                     fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor"
                          d="M4 12a8 8 0 018-8V0C5.373 0 22 6.477 22 12h-4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                </svg>
            </div>

            {{-- PDF (iframe) --}}
            <iframe id="dp-frame"
                    src=""
                    class="hidden w-full h-full border-0"
                    title="Vista previa del documento"></iframe>

            {{-- Imagen --}}
            <div id="dp-img-wrap"
                 class="hidden w-full h-full flex items-center justify-center p-6 overflow-auto">
                <img id="dp-img" src="" alt="Vista previa"
                     class="max-w-full max-h-full object-contain rounded-xl shadow-lg">
            </div>

            {{-- Fallback: tipo no previsualizable --}}
            <div id="dp-fallback"
                 class="hidden w-full h-full flex flex-col items-center justify-center gap-4 text-center p-10">
                <div class="w-20 h-20 rounded-2xl bg-white dark:bg-gray-700 shadow-sm flex items-center justify-center">
                    <svg class="w-10 h-10 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">Vista previa no disponible</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                        Este tipo de archivo no puede mostrarse aquí.<br>Descárgalo para abrirlo con la aplicación correspondiente.
                    </p>
                </div>
                <a id="dp-fallback-dl" href="#"
                   class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-white
                          bg-indigo-600 hover:bg-indigo-700 rounded-xl transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Descargar archivo
                </a>
            </div>
        </div>

    </div>{{-- /panel --}}
</div>

@once
@push('scripts')
<script>
// ── Doc Preview Modal ────────────────────────────────────────────────
const _dpImageExts = new Set(['png', 'jpg', 'jpeg', 'gif', 'webp', 'svg', 'bmp']);
const _dpIconSvgs  = {
    pdf: `<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
              d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
          </svg>`,
    img: `<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
              d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
          </svg>`,
};
const _dpIconColors = {
    pdf: 'bg-red-100 dark:bg-red-900/40 text-red-600 dark:text-red-400',
    img: 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400',
    def: 'bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400',
};

function openDocPreview(data) {
    const ext = (data.ext || '').toLowerCase();

    // Header: nombre
    document.getElementById('dp-name').textContent = data.name || 'Documento';

    // Header: descripción
    const descEl = document.getElementById('dp-description');
    if (data.description) {
        descEl.textContent = data.description;
        descEl.classList.remove('hidden');
    } else {
        descEl.classList.add('hidden');
    }

    // Header: ícono por tipo
    const iconEl    = document.getElementById('dp-icon');
    const iconColor = ext === 'pdf' ? _dpIconColors.pdf
                    : _dpImageExts.has(ext) ? _dpIconColors.img
                    : _dpIconColors.def;
    const iconSvg   = ext === 'pdf' ? _dpIconSvgs.pdf
                    : _dpImageExts.has(ext) ? _dpIconSvgs.img
                    : _dpIconSvgs.pdf; // doc genérico
    iconEl.className = `w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0 ${iconColor}`;
    iconEl.innerHTML = iconSvg;

    // Links de descarga / nueva pestaña
    document.getElementById('dp-download').href     = data.downloadUrl;
    document.getElementById('dp-fallback-dl').href  = data.downloadUrl;
    document.getElementById('dp-open-tab').href     = data.previewUrl;

    // Resetear paneles
    ['dp-frame', 'dp-img-wrap', 'dp-fallback'].forEach(id =>
        document.getElementById(id).classList.add('hidden')
    );
    document.getElementById('dp-loading').classList.remove('hidden');

    // Abrir modal
    const modal = document.getElementById('modal-doc-preview');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.style.overflow = 'hidden';

    // Mostrar contenido según tipo
    if (ext === 'pdf') {
        const frame = document.getElementById('dp-frame');
        frame.onload = () => document.getElementById('dp-loading').classList.add('hidden');
        frame.src = data.previewUrl;
        frame.classList.remove('hidden');
    } else if (_dpImageExts.has(ext)) {
        const img = document.getElementById('dp-img');
        const done = () => document.getElementById('dp-loading').classList.add('hidden');
        img.onload  = done;
        img.onerror = done;
        img.src = data.previewUrl;
        document.getElementById('dp-img-wrap').classList.remove('hidden');
    } else {
        document.getElementById('dp-loading').classList.add('hidden');
        document.getElementById('dp-fallback').classList.remove('hidden');
    }
}

function closeDocPreview() {
    const modal = document.getElementById('modal-doc-preview');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.style.overflow = '';

    // Limpiar para evitar que el iframe siga cargando
    const frame = document.getElementById('dp-frame');
    frame.src = '';
    frame.classList.add('hidden');
    const img = document.getElementById('dp-img');
    img.src = '';
    document.getElementById('dp-img-wrap').classList.add('hidden');
    document.getElementById('dp-fallback').classList.add('hidden');
    document.getElementById('dp-loading').classList.remove('hidden');
}

document.addEventListener('keydown', e => {
    if (e.key === 'Escape' && !document.getElementById('modal-doc-preview').classList.contains('hidden')) {
        closeDocPreview();
    }
});
</script>
@endpush
@endonce
