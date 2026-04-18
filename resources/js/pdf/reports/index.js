import { pdfMake, wrapDoc } from '../builder.js';
import { buildContent as systemsListContent }     from './systems-list.js';
import { buildContent as systemsDetailedContent } from './systems-detailed.js';
import { buildContent as serversContent }         from './servers.js';

const today = () => new Date().toISOString().slice(0, 10);

const REPORTS = {
    'systems': {
        url:         '/reports/pdf-data/systems',
        orientation: 'portrait',
        content:     systemsListContent,
        filename:    () => `lista_sistemas_${today()}.pdf`,
    },
    'systems-detailed': {
        url:          '/reports/pdf-data/systems-detailed',
        orientation:  'landscape',
        margins:      [25, 38, 25, 44],
        footerMargin: 25,
        content:      systemsDetailedContent,
        filename:     () => `sistemas_detallados_${today()}.pdf`,
    },
    'servers': {
        url:          '/reports/pdf-data/servers',
        orientation:  'landscape',
        margins:      [25, 38, 25, 44],
        footerMargin: 25,
        content:      serversContent,
        filename:     () => `lista_servidores_${today()}.pdf`,
    },
};

window.downloadReportPdf = async function (type, btnEl) {
    const cfg = REPORTS[type];
    if (!cfg) return;

    const originalHTML = btnEl?.innerHTML ?? '';
    if (btnEl) {
        btnEl.disabled = true;
        btnEl.innerHTML = `<svg class="w-4 h-4 animate-spin inline-block" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg> Generando…`;
    }
    try {
        const res  = await fetch(cfg.url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        const data = await res.json();
        const doc  = wrapDoc(cfg.content(data), {
            orientation:  cfg.orientation,
            margins:      cfg.margins,
            footerMargin: cfg.footerMargin,
        });
        pdfMake.createPdf(doc).download(cfg.filename());
    } catch (e) {
        console.error('PDF error:', e);
        if (window.sgToast) sgToast('error', 'No se pudo generar el PDF');
    } finally {
        if (btnEl) { btnEl.disabled = false; btnEl.innerHTML = originalHTML; }
    }
};
