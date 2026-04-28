import { pdfMake, wrapDoc } from './builder.js';
import { C } from './theme.js';

// ── Role color map ─────────────────────────────────────────────────────

const roleColors = {
    principal:      { color: '#1b3a5c', fill: '#e8f0f7', border: '#1b3a5c' },
    soporte:        { color: '#374151', fill: '#f3f4f6', border: '#9ca3af' },
    supervision:    { color: '#5b21b6', fill: '#ede9fe', border: '#7c3aed' },
    operador:       { color: '#92400e', fill: '#fef3c7', border: '#d97706' },
    lider_proyecto: { color: '#1d4ed8', fill: '#dbeafe', border: '#3b82f6' },
    desarrollador:  { color: '#065f46', fill: '#d1fae5', border: '#10b981' },
    mantenimiento:  { color: '#92400e', fill: '#fef3c7', border: '#d97706' },
    administrador:  { color: '#3730a3', fill: '#e0e7ff', border: '#6366f1' },
    analista:       { color: '#164e63', fill: '#cffafe', border: '#06b6d4' },
    lider_tecnico:  { color: '#1d4ed8', fill: '#dbeafe', border: '#3b82f6' },
    tester:         { color: '#065f46', fill: '#d1fae5', border: '#10b981' },
    despliegue:     { color: '#7c2d12', fill: '#ffedd5', border: '#f97316' },
    aprobador:      { color: '#5b21b6', fill: '#ede9fe', border: '#7c3aed' },
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
                margin: [7, 2, 7, 2],
            }]],
        },
    };
}

function sectionTitle(title, lineWidth = 515) {
    return {
        stack: [
            { text: title, fontSize: 8.5, bold: true, color: C.navy, margin: [0, 0, 0, 4] },
            { canvas: [{ type: 'line', x1: 0, y1: 0, x2: lineWidth, y2: 0, lineWidth: 0.6, lineColor: C.navy }] },
        ],
        margin: [0, 18, 0, 10],
    };
}

function field(label, value, labelWidth = 130) {
    return {
        columns: [
            { text: label + ':', fontSize: 8, bold: true, color: C.muted, width: labelWidth },
            { text: value ?? '—', fontSize: 8, color: C.dark },
        ],
        margin: [0, 3, 0, 3],
    };
}

// ── Document builder ───────────────────────────────────────────────────

function buildContent(d) {
    const ctx  = d.context;
    const resp = d.responsible;

    return [
        // ── Encabezado institucional ──────────────────────────────────
        {
            columns: [
                {
                    stack: [
                        { text: 'UNIVERSIDAD NACIONAL MADRE DE DIOS', fontSize: 10, bold: true, color: C.navy },
                        { text: 'Oficina de Tecnologías de la Información — OTI', fontSize: 6.5, color: C.muted, margin: [0, 2, 0, 0] },
                        { text: 'Puerto Maldonado, Madre de Dios — Perú', fontSize: 6, color: C.light, margin: [0, 1, 0, 0] },
                    ],
                    width: '*',
                    margin: [0, 3, 0, 0],
                },
                {
                    stack: [
                        { text: 'ACTA DE COMPROMISO', fontSize: 9, bold: true, color: C.navy, alignment: 'right' },
                        { text: ctx.subtitle, fontSize: 8, bold: true, color: C.navy, alignment: 'right', margin: [0, 3, 0, 2] },
                        { text: `Emitido: ${d.generated_at}`, fontSize: 6, color: C.muted, alignment: 'right' },
                        { text: `Por: ${d.generated_by}`, fontSize: 6, color: C.muted, alignment: 'right', margin: [0, 1, 0, 0] },
                    ],
                    width: 'auto',
                },
            ],
            margin: [0, 0, 0, 6],
        },

        { canvas: [{ type: 'line', x1: 0, y1: 0, x2: 515, y2: 0, lineWidth: 2.5, lineColor: C.navy }], margin: [0, 0, 0, 2] },
        { canvas: [{ type: 'line', x1: 0, y1: 0, x2: 515, y2: 0, lineWidth: 0.5, lineColor: '#4a7aa8' }], margin: [0, 0, 0, 18] },

        // ── Párrafo de apertura ───────────────────────────────────────
        {
            text: [
                { text: 'En la ciudad de Puerto Maldonado, siendo la fecha indicada en el presente documento, la ' },
                { text: 'Oficina de Tecnologías de la Información (OTI)', bold: true, color: C.navy },
                { text: ' de la ' },
                { text: 'Universidad Nacional Madre de Dios', bold: true },
                { text: ', deja constancia formal de la asignación de responsabilidad sobre el recurso tecnológico descrito a continuación, conforme a las políticas institucionales de gestión de activos de tecnología.' },
            ],
            fontSize: 8.5,
            color: C.dark,
            lineHeight: 1.6,
            margin: [0, 0, 0, 4],
        },

        // ── Datos del recurso + Responsable (columnas paralelas) ─────
        {
            columns: [
                {
                    stack: [
                        sectionTitle(ctx.section_title, 237),
                        ...ctx.fields.map(f => field(f.label, f.value ?? '—', 100)),
                    ],
                    width: '*',
                },
                {
                    stack: [
                        sectionTitle('Responsable Asignado', 237),
                        field('Apellidos y nombres', resp.nombre_completo, 100),
                        field('N° DNI',              resp.dni ?? '—', 100),
                        ...(resp.email    ? [field('Correo electrónico', resp.email, 100)]    : []),
                        ...(resp.telefono ? [field('Teléfono',           resp.telefono, 100)] : []),
                        {
                            columns: [
                                { text: 'Nivel / Rol:', fontSize: 8, bold: true, color: C.muted, width: 100 },
                                { stack: [roleBadge(resp.role, resp.role_label)] },
                            ],
                            margin: [0, 3, 0, 3],
                        },
                        field('Fecha de asignación', resp.assigned_at ?? '—', 100),
                        field('Estado', resp.is_active ? 'Activo' : 'Inactivo', 100),
                    ],
                    width: '*',
                },
            ],
            columnGap: 24,
        },

        // ── Alcance de responsabilidades ──────────────────────────────
        sectionTitle('Alcance de Responsabilidades'),
        {
            text: 'El responsable asignado se compromete expresamente a cumplir con las siguientes obligaciones:',
            fontSize: 8.5,
            color: C.dark,
            lineHeight: 1.5,
            margin: [0, 0, 0, 8],
        },
        ...ctx.responsibilities.map((text, i) => ({
            columns: [
                { text: `${i + 1}.`, fontSize: 8, bold: true, color: C.navy, width: 16 },
                { text, fontSize: 8, color: C.dark, lineHeight: 1.5 },
            ],
            margin: [8, 2, 0, 2],
        })),

        // ── Párrafo de aceptación ─────────────────────────────────────
        {
            text: [
                { text: '\nDECLARACIÓN DE ACEPTACIÓN\n', bold: true, color: C.navy },
                {
                    text: 'Habiendo leído y comprendido el contenido del presente documento, el responsable asignado declara su conformidad y acepta expresamente las obligaciones detalladas, comprometiéndose a su cabal cumplimiento. El incumplimiento de las responsabilidades aquí establecidas podrá dar lugar a las medidas administrativas que correspondan conforme al reglamento institucional vigente.',
                },
            ],
            fontSize: 8,
            color: C.dark,
            lineHeight: 1.6,
            italics: true,
            margin: [0, 22, 0, 0],
        },

        // ── Firmas ────────────────────────────────────────────────────
        {
            columns: [
                {
                    stack: [
                        { text: '', margin: [0, 0, 0, 32] },
                        { canvas: [{ type: 'line', x1: 0, y1: 0, x2: 175, y2: 0, lineWidth: 0.8, lineColor: C.dark }] },
                        { text: resp.nombre_completo, fontSize: 8, bold: true, color: C.dark, margin: [0, 4, 0, 1] },
                        { text: resp.role_label + (resp.dni ? '  ·  DNI ' + resp.dni : ''), fontSize: 7, color: C.muted },
                        { text: 'Responsable Asignado', fontSize: 7, color: C.light, margin: [0, 1, 0, 0] },
                    ],
                    width: '*',
                    alignment: 'center',
                },
                { text: '', width: 40 },
                {
                    stack: [
                        { text: '', margin: [0, 0, 0, 32] },
                        { canvas: [{ type: 'line', x1: 0, y1: 0, x2: 175, y2: 0, lineWidth: 0.8, lineColor: C.dark }] },
                        { text: 'Jefe de la OTI', fontSize: 8, bold: true, color: C.dark, margin: [0, 4, 0, 1] },
                        { text: 'Oficina de Tecnologías de la Información', fontSize: 7, color: C.muted },
                        { text: 'UNAMAD', fontSize: 7, color: C.light, margin: [0, 1, 0, 0] },
                    ],
                    width: '*',
                    alignment: 'center',
                },
            ],
            margin: [0, 36, 0, 6],
        },

        // ── Lugar y fecha ─────────────────────────────────────────────
        {
            columns: [
                { text: `Puerto Maldonado, ${resp.assigned_at ?? '___/___/______'}`, fontSize: 7, color: C.muted, alignment: 'center' },
                { text: '', width: 40 },
                { text: `Puerto Maldonado, ${resp.assigned_at ?? '___/___/______'}`, fontSize: 7, color: C.muted, alignment: 'center' },
            ],
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
        const assignedAt = data.responsible?.assigned_at ?? '___/___/______';
        doc.footer = (cp, pc) => ({
            columns: [
                { text: `Puerto Maldonado — ${assignedAt}`, fontSize: 6.5, color: C.muted, margin: [40, 0, 0, 0] },
                { text: `Página ${cp} de ${pc}`, fontSize: 7, bold: true, color: C.navy, alignment: 'right', margin: [0, 0, 40, 0] },
            ],
            margin: [0, 10, 0, 0],
        });
        const slug = (data.responsible?.apellido_pat ?? 'responsable').toLowerCase().replace(/\s+/g, '_');
        pdfMake.createPdf(doc).download(`acta_compromiso_${slug}_${new Date().toISOString().slice(0, 10)}.pdf`);
    } catch (e) {
        console.error('Acta PDF error:', e);
        if (window.sgToast) sgToast('error', 'No se pudo generar el acta PDF');
    } finally {
        if (btnEl) { btnEl.disabled = false; btnEl.innerHTML = originalHTML; }
    }
};
