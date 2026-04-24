import { pdfMake, wrapDoc } from './builder.js';
import { C, rule, kv, sectionHeader } from './theme.js';

// ── Helpers ───────────────────────────────────────────────────────────

const levelColors = {
    principal:   { color: '#1b3a5c', fill: '#e8f0f7', border: '#1b3a5c' },
    soporte:     { color: '#374151', fill: '#f3f4f6', border: '#9ca3af' },
    supervision: { color: '#5b21b6', fill: '#ede9fe', border: '#7c3aed' },
    operador:    { color: '#92400e', fill: '#fef3c7', border: '#d97706' },
};

function levelBadge(level, label) {
    const s = levelColors[level] ?? { color: C.muted, fill: null, border: C.borderSoft };
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
            { text: label,    fontSize: 7.5, bold: true,   color: C.dark,   margin: [0, 4, 0, 1] },
            { text: sublabel, fontSize: 7,   bold: false,  color: C.muted,  margin: [0, 0, 0, 0] },
        ],
        margin: [0, 0, 0, 0],
    };
}

// ── Document builder ─────────────────────────────────────────────────

function buildContent(d) {
    const srv  = d.server;
    const resp = d.responsible;

    const sections = [
        // ── Encabezado institucional ─────────────────────────────────
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
                        { text: 'RESPONSABILIDAD DE SERVIDOR', fontSize: 8.5, bold: true, color: C.navy, alignment: 'right', margin: [0, 1, 0, 0] },
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
                { text: ' de la Universidad Nacional Madre de Dios, deja constancia de la asignación formal de responsabilidad sobre el servidor indicado a continuación.', fontSize: 8, color: C.dark },
            ],
            margin: [0, 0, 0, 16],
            lineHeight: 1.4,
        },

        // ── Datos del servidor ────────────────────────────────────────
        sectionHeader('Datos del Servidor'),
        {
            columns: [
                {
                    stack: [
                        kv('Nombre del servidor', srv.name),
                        kv('Sistema operativo',   srv.os   ?? '—'),
                        kv('Tipo de host',         srv.host_type ?? '—'),
                    ],
                    width: '50%',
                },
                {
                    stack: [
                        kv('Función',           srv.function  ?? '—'),
                        kv('IP principal',       srv.primary_ip ?? '—'),
                        ...(srv.public_ips?.length
                            ? [kv('IPs públicas', srv.public_ips.join(', '))]
                            : []),
                    ],
                    width: '50%',
                },
            ],
            columnGap: 20,
            margin: [0, 0, 0, 4],
        },

        ...(srv.services?.length ? [
            {
                columns: [
                    { text: 'Servicios instalados', fontSize: 7, bold: true, color: C.muted, width: 105 },
                    { text: srv.services.join(' · '), fontSize: 7, color: C.dark },
                ],
                margin: [0, 2, 0, 2],
            },
        ] : []),

        ...(srv.notes ? [
            {
                columns: [
                    { text: 'Notas', fontSize: 7, bold: true, color: C.muted, width: 105 },
                    { text: srv.notes, fontSize: 7, color: C.medium, italics: true },
                ],
                margin: [0, 2, 0, 2],
            },
        ] : []),

        // ── Datos del responsable ─────────────────────────────────────
        sectionHeader('Responsable Asignado'),
        {
            columns: [
                {
                    stack: [
                        kv('Apellidos y nombres', resp.nombre_completo),
                        kv('N° DNI',              resp.dni  ?? '—'),
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
                                { stack: [levelBadge(resp.level, resp.level_label)] },
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
            ul: [
                { text: 'Velar por el correcto funcionamiento, disponibilidad y seguridad del servidor asignado.', fontSize: 7.5, color: C.dark, margin: [0, 2, 0, 2] },
                { text: 'Notificar oportunamente cualquier incidente, falla o anomalía que afecte al servidor.', fontSize: 7.5, color: C.dark, margin: [0, 2, 0, 2] },
                { text: 'Mantener actualizados el sistema operativo y los servicios instalados conforme a las políticas de seguridad de la OTI.', fontSize: 7.5, color: C.dark, margin: [0, 2, 0, 2] },
                { text: 'Gestionar los accesos, credenciales y configuraciones del servidor con criterios de mínimo privilegio y seguridad.', fontSize: 7.5, color: C.dark, margin: [0, 2, 0, 2] },
                { text: 'Coordinar con la OTI antes de realizar cambios estructurales, migraciones o configuraciones críticas en el servidor.', fontSize: 7.5, color: C.dark, margin: [0, 2, 0, 2] },
                { text: 'Custodiar la información alojada en el servidor bajo los principios de confidencialidad, integridad y disponibilidad.', fontSize: 7.5, color: C.dark, margin: [0, 2, 0, 2] },
            ],
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
                    stack: [
                        signatureBlock(
                            resp.nombre_completo,
                            resp.level_label + (resp.dni ? '  ·  DNI ' + resp.dni : ''),
                        ),
                    ],
                    width: '*',
                    alignment: 'center',
                },
                { text: '', width: 40 },
                {
                    stack: [
                        signatureBlock(
                            'Jefe de la OTI',
                            'Oficina de Tecnologías de la Información',
                        ),
                    ],
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

    return sections;
}

// ── Global function ───────────────────────────────────────────────────

window.downloadResponsibleActa = async function downloadResponsibleActa(serverId, responsibleId, btnEl) {
    const originalHTML = btnEl?.innerHTML ?? '';
    if (btnEl) {
        btnEl.disabled = true;
        btnEl.innerHTML = `<svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>`;
    }
    try {
        const url  = `/admin/servers/${serverId}/responsibles/${responsibleId}/pdf-data`;
        const res  = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
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
}
