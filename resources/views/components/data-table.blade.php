@props([
    'id'                => 'data-table',
    'searchPlaceholder' => 'Buscar...',
])

{{-- ══ Wrapper card ══ --}}
<div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">

    {{-- Toolbar --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4
                border-b border-gray-200 dark:border-gray-700">

        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <input data-tbl-search="{{ $id }}" type="text"
                   placeholder="{{ $searchPlaceholder }}"
                   class="pl-9 pr-4 py-2 text-sm bg-gray-50 dark:bg-gray-700
                          border border-gray-200 dark:border-gray-600 rounded-lg
                          text-gray-800 dark:text-gray-200
                          placeholder-gray-400 dark:placeholder-gray-500
                          focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent
                          w-full sm:w-64 transition">
        </div>

        <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
            <span>Mostrar</span>
            <select data-tbl-perpage="{{ $id }}"
                    class="px-2 py-1.5 bg-gray-50 dark:bg-gray-700
                           border border-gray-200 dark:border-gray-600 rounded-lg
                           text-gray-700 dark:text-gray-300
                           focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="0">Todos</option>
            </select>
            <span>filas</span>
        </div>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto">
        <table data-tbl="{{ $id }}" class="min-w-full">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-700/50">
                    {{ $thead }}
                </tr>
            </thead>
            <tbody data-tbl-body="{{ $id }}"
                   class="divide-y divide-gray-100 dark:divide-gray-700">
                {{ $tbody }}
            </tbody>
        </table>
    </div>

    {{-- Empty state --}}
    @if(isset($empty))
    <div data-tbl-empty="{{ $id }}">
        {{ $empty }}
    </div>
    @endif

    {{-- Footer: info + pagination --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3
                px-5 py-3 border-t border-gray-200 dark:border-gray-700">
        <p data-tbl-info="{{ $id }}" class="text-sm text-gray-500 dark:text-gray-400"></p>
        <div data-tbl-pagination="{{ $id }}" class="flex items-center gap-1"></div>
    </div>
</div>

{{-- ══ Delete confirmation modal (one instance per page) ══ --}}
@once
<div id="dt-modal-delete"
     class="hidden fixed inset-0 z-50 items-center justify-center p-4"
     role="dialog" aria-modal="true">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"
         onclick="dtCloseModal()"></div>
    <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-sm w-full p-6">
        <div class="flex items-center gap-4 mb-5">
            <div class="flex-shrink-0 w-12 h-12 rounded-full bg-red-100 dark:bg-red-900/30
                        flex items-center justify-center">
                <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94
                             a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">
                    Confirmar eliminación
                </h3>
                <p id="dt-modal-msg"
                   class="text-sm text-gray-500 dark:text-gray-400 mt-0.5"></p>
            </div>
        </div>
        <div class="flex gap-3 justify-end">
            <button onclick="dtCloseModal()"
                    class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300
                           bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200
                           dark:hover:bg-gray-600 transition-colors">
                Cancelar
            </button>
            <button id="dt-modal-confirm"
                    class="px-4 py-2 text-sm font-medium text-white bg-red-600
                           rounded-lg hover:bg-red-700 transition-colors shadow-sm">
                Sí, eliminar
            </button>
        </div>
    </div>
</div>
@endonce

{{-- ══ DataTable JS ══ --}}
@once
@push('scripts')
<script>
// ── Delete modal helpers ──────────────────────────────────────────────
function dtConfirmDelete(formId, entityName) {
    document.getElementById('dt-modal-msg').textContent =
        '¿Estás seguro de eliminar "' + entityName + '"? Esta acción no se puede deshacer.';
    document.getElementById('dt-modal-confirm').onclick = () => {
        document.getElementById(formId).submit();
    };
    const modal = document.getElementById('dt-modal-delete');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}
function dtCloseModal() {
    const modal = document.getElementById('dt-modal-delete');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

// ── DataTable factory ─────────────────────────────────────────────────
function initDataTable(id) {
    const tbody   = document.querySelector(`[data-tbl-body="${id}"]`);
    const searchEl = document.querySelector(`[data-tbl-search="${id}"]`);
    const ppEl    = document.querySelector(`[data-tbl-perpage="${id}"]`);
    const infoEl  = document.querySelector(`[data-tbl-info="${id}"]`);
    const pagEl   = document.querySelector(`[data-tbl-pagination="${id}"]`);
    const emptyEl = document.querySelector(`[data-tbl-empty="${id}"]`);

    if (!tbody) return;

    const allRows = Array.from(tbody.querySelectorAll('tr.tbl-row'));
    let filtered  = [...allRows];
    let page      = 1;
    let perPage   = 10;
    let sortCol   = -1;
    let sortAsc   = true;

    function cellText(row, col) {
        return (row.querySelectorAll('td')[col]?.innerText ?? '').trim().toLowerCase();
    }

    function searchableCols(row) {
        // all td except the last (actions)
        return Array.from(row.querySelectorAll('td')).slice(0, -1);
    }

    function applyFilter() {
        const q = searchEl.value.toLowerCase().trim();
        filtered = allRows.filter(row =>
            searchableCols(row).some(td => td.innerText.toLowerCase().includes(q))
        );
        page = 1;
        render();
    }

    function applySort(col) {
        sortCol === col ? (sortAsc = !sortAsc) : (sortCol = col, sortAsc = true);
        filtered.sort((a, b) => {
            const va = cellText(a, col), vb = cellText(b, col);
            return sortAsc ? va.localeCompare(vb, 'es') : vb.localeCompare(va, 'es');
        });
        updateSortIcons();
        render();
    }

    function updateSortIcons() {
        document.querySelectorAll(`[data-tbl="${id}"] .tbl-sort`).forEach(th => {
            const col  = parseInt(th.dataset.col);
            const icon = th.querySelector('.sort-icon');
            if (!icon) return;
            if (col === sortCol) {
                icon.innerHTML = sortAsc
                    ? `<svg class="w-3.5 h-3.5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>`
                    : `<svg class="w-3.5 h-3.5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>`;
            } else {
                icon.innerHTML = `<svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l4-4m-4 4l-4-4"/></svg>`;
            }
        });
    }

    function render() {
        const total = filtered.length;
        const pp    = perPage === 0 ? total : perPage;
        const pages = pp > 0 ? Math.ceil(total / pp) : 1;
        if (page > pages) page = Math.max(1, pages);

        const start = pp > 0 ? (page - 1) * pp : 0;
        const end   = pp > 0 ? Math.min(start + pp, total) : total;

        allRows.forEach(r => (r.style.display = 'none'));
        filtered.forEach((r, i) => {
            r.style.display = (i >= start && i < end) ? '' : 'none';
            tbody.appendChild(r); // maintain sort order
        });

        // empty state
        if (emptyEl) emptyEl.style.display = total === 0 && allRows.length > 0 ? '' : 'none';

        infoEl.textContent = total === 0
            ? 'No se encontraron resultados'
            : `Mostrando ${start + 1}–${end} de ${total} registro${total !== 1 ? 's' : ''}`;

        renderPagination(page, pages);
    }

    function renderPagination(current, total) {
        pagEl.innerHTML = '';
        if (total <= 1) return;

        const btn = (label, p, disabled, active) => {
            const b = document.createElement('button');
            b.innerHTML = label;
            b.disabled  = disabled;
            b.className = [
                'px-2.5 py-1.5 text-xs rounded-lg font-medium transition-all',
                active    ? 'bg-blue-600 text-white shadow-sm'
                : disabled ? 'text-gray-300 dark:text-gray-600 cursor-not-allowed'
                           : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700',
            ].join(' ');
            if (!disabled) b.onclick = () => { page = p; render(); };
            return b;
        };

        pagEl.appendChild(btn('‹', current - 1, current === 1, false));
        let s = Math.max(1, current - 2), e = Math.min(total, s + 4);
        if (e - s < 4) s = Math.max(1, e - 4);
        for (let p = s; p <= e; p++) pagEl.appendChild(btn(p, p, false, p === current));
        pagEl.appendChild(btn('›', current + 1, current === total, false));
    }

    // Event listeners
    searchEl.addEventListener('input', applyFilter);
    ppEl.addEventListener('change', () => { perPage = parseInt(ppEl.value); page = 1; render(); });
    document.querySelectorAll(`[data-tbl="${id}"] .tbl-sort`).forEach(th =>
        th.addEventListener('click', () => applySort(parseInt(th.dataset.col)))
    );

    render();
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-tbl]').forEach(t => initDataTable(t.dataset.tbl));
});
</script>
@endpush
@endonce
