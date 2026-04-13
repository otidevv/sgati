{{--
    Partial: búsqueda de persona (solicitado por / responsable)
    Requiere Alpine.js activo en el componente padre.
    Actualiza el modelo Alpine `requested_by_persona_id` y `requester_name`
    mediante el evento personalizado `requester-selected`.
--}}
<div>
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
        Solicitado / Responsable
    </label>
    <div class="relative" id="requester-search-wrap">
        <div class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" id="requester-search-input" autocomplete="off"
                   placeholder="Buscar por DNI o nombre…"
                   class="w-full pl-9 pr-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600
                          bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm
                          focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
        </div>
        <div id="requester-dropdown"
             class="hidden absolute z-50 w-full mt-1 bg-white dark:bg-gray-800 border border-gray-200
                    dark:border-gray-600 rounded-lg shadow-xl max-h-48 overflow-y-auto text-sm">
        </div>
        {{-- Persona seleccionada --}}
        <div id="requester-selected"
             class="hidden mt-2 items-center gap-2 px-3 py-2 rounded-lg
                    bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700/40">
            <svg class="w-4 h-4 text-blue-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            <span id="requester-selected-name"
                  class="flex-1 text-sm font-medium text-blue-700 dark:text-blue-300 truncate"></span>
            <button type="button" id="requester-clear-btn"
                    class="text-blue-400 hover:text-red-500 transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">Escribe al menos 4 caracteres para buscar</p>
    </div>
</div>
