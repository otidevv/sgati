import { pdfMake, wrapDoc } from './builder.js';
import { C, rule, kv, sectionHeader } from './theme.js';

// ── Role color map (covers all contexts: server, system, database, version, repo) ─────

const roleColors = {
    // Servidor / BD server
    principal:      { color: '#1b3a5c', fill: '#e8f0f7', border: '#1b3a5c' },
    soporte:        { color: '#374151', fill: '#f3f4f6', border: '#9ca3af' },
    supervision:    { color: '#5b21b6', fill: '#ede9fe', border: '#7c3aed' },
    operador:       { color: '#92400e', fill: '#fef3c7', border: '#d97706' },
    // Sistema
    lider_proyecto: { color: '#1d4ed8', fill: '#dbeafe', border: '#3b82f6' },
    desarrollador:  { color: '#065f46', fill: '#d1fae5', border: '#10b981' },
    mantenimiento:  { color: '#92400e', fill: '#fef3c7', border: '#d97706' },
    administrador:  { color: '#3730a3', fill: '#e0e7ff', border: '#6366f1' },
    analista:       { color: '#164e63', fill: '#cffafe', border: '#06b6d4' },
    // Versión
    lider_tecnico:  { color: '#1d4ed8', fill: '#dbeafe', border: '#3b82f6' },
    tester:         { color: '#065f46', fill: '#d1fae5', border: '#10b981' },
    despliegue:     { color: '#7c2d12', fill: '#ffedd5', border: '#f97316' },
    aprobador:      { color: '#5b21b6', fill: '#ede9fe', border: '#7c3aed' },
    // Repositorio
    owner:          { color: '#1b3a5c', fill: '#e8f0f7', border: '#1b3a5c' },
    maintainer:     { color: '#5b21b6', fill: '#ede9fe', border: '#7c3aed' },
    developer:      { color: '#065f46', fill: '#d1fae5', border: '#10b981' },
    reader:         { color: '#374151', fill: '#f3f4f6', border: '#9ca3af' },
    deployer:       { color: '#92400e', fill: '#fef3c7', border: '#d97706' },
};

function roleBadge(role, label) {
    const s = roleColors[role] ?? { color: C.muted, fill: '#f9fafb', border: '#d1d5db' };
    return {
        table: {
            widths: ['auto'],
            body: [[{
                text: label.toUpperCase(),
                fontSize: 7,
                bold: true,
                color: s.color,
                fillColor: s.fill,
                border: [true, true, true, true],
                borderColor: [s.border, s.border, s.border, s.border],
                margin: [8, 3, 8, 3],
            }]],
        },
    };
}

function signatureBlock(label, sublabel = '') {
    return {
        stack: [
            { canvas: [{ type: 'line', x1: 0, y1: 0, x2: 170, y2: 0, lineWidth: 0.8, lineColor: C.dark }] },
            { text: label,    fontSize: 7.5, bold: true,  color: C.dark,  margin: [0, 4, 0, 1] },
            { text: sublabel, fontSize: 7,   bold: false, color: C.muted, margin: [0, 0, 0, 0] },
        ],
        margin: [0, 0, 0, 0],
    };
}

// ── Document builder ──────────────────────────────────────────────────

function buildContent(d) {
    const ctx  = d.context;
    const resp = d.responsible;

    return [
        // ── Encabezado institucional ──────────────────────────────────
        {
            columns: [
                {
                    stack: [
                        { text: 'UNIVERSIDAD NACIONAL MADRE DE DIOS', fontSize: 9, bold: true, color: C.navy },
                        { text: 'Oficina de Tecnologías de la Información — OTI', fontSize: 6.5, color: C.muted, margin: [0, 1, 0, 0] },
                    ],
                    width: '*',
                },
                {
                    stack: [
                        { text: 'ACTA DE ASIGNACIÓN', fontSize: 11, bold: true, color: C.navy, alignment: 'right' },
                        { text: ctx.subtitle, fontSize: 8.5, bold: true, color: C.navy, alignment: 'right', margin: [0, 1, 0, 0] },
                        { text: `Generado: ${d.generated_at}  ·  Por: ${d.generated_by}`, fontSize: 5.5, color: C.muted, alignment: 'right', margin: [0, 2, 0, 0] },
                    ],
                    width: 'auto',
                },
            ],
            margin: [0, 0, 0, 4],
        },
        { canvas: [{ type: 'line', x1: 0, y1: 0, x2: 515, y2: 0, lineWidth: 2, lineColor: C.navy }], margin: [0, 0, 0, 14] },

        // ── Texto declarativo ─────────────────────────────────────────
        {
            text: [
                { text: 'Por medio del presente documento, la ', fontSize: 8, color: C.dark },
                { text: 'Oficina de Tecnologías de la Información (OTI)', fontSize: 8, bold: true, color: C.navy },
                { text: ' de la Universidad Nacional Madre de Dios, deja constancia de la asignación formal de responsabilidad sobre el recurso indicado a continuación.', fontSize: 8, color: C.dark },
            ],
            margin: [0, 0, 0, 16],
            lineHeight: 1.4,
        },

        // ── Datos del recurso (dinámico) ──────────────────────────────
        sectionHeader(ctx.section_title),
        ...ctx.fields.map(f => kv(f.label, f.value ?? '—')),

        // ── Datos del responsable ─────────────────────────────────────
        sectionHeader('Responsable Asignado'),
        {
            columns: [
                {
                    stack: [
                        kv('Apellidos y nombres', resp.nombre_completo),
                        kv('N° DNI',              resp.dni      ?? '—'),
                        ...(resp.email    ? [kv('Correo electrónico', resp.email)]    : []),
                        ...(resp.telefono ? [kv('Teléfono',           resp.telefono)] : []),
                    ],
                    width: '55%',
                },
                {
                    stack: [
                        {
                            columns: [
                                { text: 'Nivel / Rol', fontSize: 7, bold: true, color: C.muted, width: 70 },
                                { stack: [roleBadge(resp.role, resp.role_label)] },
                            ],
                            margin: [0, 2, 0, 2],
                        },
                        kv('Fecha de asignación', resp.assigned_at ?? '—'),
                        kv('Estado',              resp.is_active ? 'Activo' : 'Inactivo'),
                    ],
                    width: '45%',
                },
            ],
            columnGap: 20,
            margin: [0, 0, 0, 4],
        },

        // ── Alcance de responsabilidades ──────────────────────────────
        sectionHeader('Alcance de Responsabilidades'),
        {
            ul: ctx.responsibilities.map(text => ({
                text,
                fontSize: 7.5,
                color: C.dark,
                margin: [0, 2, 0, 2],
            })),
            margin: [10, 0, 0, 16],
            lineHeight: 1.35,
        },

        // ── Aceptación ────────────────────────────────────────────────
        {
            text: 'En señal de conformidad y aceptación de las responsabilidades detalladas, el responsable asignado y el representante de la OTI suscriben el presente documento.',
            fontSize: 7.5,
            color: C.dark,
            italics: true,
            margin: [0, 0, 0, 40],
            lineHeight: 1.4,
        },

        // ── Firmas ────────────────────────────────────────────────────
        {
            columns: [
                {
                    stack: [signatureBlock(
                        resp.nombre_completo,
                        resp.role_label + (resp.dni ? '  ·  DNI ' + resp.dni : ''),
                    )],
                    width: '*',
                    alignment: 'center',
                },
                { text: '', width: 40 },
                {
                    stack: [signatureBlock('Jefe de la OTI', 'Oficina de Tecnologías de la Información')],
                    width: '*',
                    alignment: 'center',
                },
            ],
            margin: [0, 0, 0, 0],
        },

        // ── Pie informativo ───────────────────────────────────────────
        rule(),
        {
            columns: [
                { text: `Puerto Maldonado, ${resp.assigned_at ?? '___/___/______'}`, fontSize: 6.5, color: C.muted },
                { text: 'SGATI — Sistema de Gestión y Administración de Tecnologías de Información', fontSize: 6.5, color: C.light, alignment: 'right', italics: true },
            ],
            margin: [0, 6, 0, 0],
        },
    ];
}

// ── Global function ───────────────────────────────────────────────────
// Usage: downloadResponsibleActa(pdfDataUrl, btnEl)

window.downloadResponsibleActa = async function downloadResponsibleActa(pdfDataUrl, btnEl) {
    const originalHTML = btnEl?.innerHTML ?? '';
    if (btnEl) {
        btnEl.disabled = true;
        btnEl.innerHTML = `<svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>`;
    }
    try {
        const res  = await fetch(pdfDataUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        const data = await res.json();
        const doc  = wrapDoc(buildContent(data), { orientation: 'portrait' });
        const slug = (data.responsible?.apellido_pat ?? 'responsable').toLowerCase().replace(/\s+/g, '_');
        pdfMake.createPdf(doc).download(`acta_asignacion_${slug}_${new Date().toISOString().slice(0, 10)}.pdf`);
    } catch (e) {
        console.error('Acta PDF error:', e);
        if (window.sgToast) sgToast('error', 'No se pudo generar el acta PDF');
    } finally {
        if (btnEl) { btnEl.disabled = false; btnEl.innerHTML = originalHTML; }
    }
};
