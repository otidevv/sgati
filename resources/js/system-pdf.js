import pdfMake from 'pdfmake/build/pdfmake';
import pdfFonts from 'pdfmake/build/vfs_fonts';

if (pdfFonts?.vfs)           pdfMake.vfs = pdfFonts.vfs;
else if (pdfFonts?.pdfMake)  pdfMake.vfs = pdfFonts.pdfMake.vfs;

// ── Paleta: base gris + acento azul marino ───────────────────────────
const C = {
    navy:       '#1b3a5c',   // acento principal — títulos, líneas, headers
    navyLight:  '#e8f0f7',   // fondo muy sutil azulado para th
    black:      '#0d0d0d',
    dark:       '#2a2a2a',
    medium:     '#4a4a4a',
    muted:      '#6e6e6e',
    light:      '#9a9a9a',
    border:     '#aaaaaa',
    borderSoft: '#d4d4d4',
    rowAlt:     '#f6f6f6',
    white:      '#ffffff',
};

// ── Helpers ──────────────────────────────────────────────────────────
const th = (text, w) => ({
    text,
    fontSize: 7,
    bold: true,
    color: C.navy,
    fillColor: C.navyLight,
    margin: [5, 4, 5, 4],
    border: [false, false, false, true],
    borderColor: [null, null, null, C.navy],
    ...(w ? { width: w } : {}),
});

const td = (text, opts = {}) => ({
    text: text ?? '—',
    fontSize: 7.5,
    color: C.dark,
    margin: [5, 3, 5, 3],
    border: [false, false, false, true],
    borderColor: [null, null, null, C.borderSoft],
    ...opts,
});

const tableLayout = {
    hLineWidth: (i, node) => (i === 0 || i === node.table.body.length) ? 0 : 0.5,
    vLineWidth: () => 0,
    hLineColor: () => C.borderSoft,
    fillColor:  (row) => row === 0 ? null : row % 2 === 0 ? C.rowAlt : null,
    paddingLeft:   () => 0,
    paddingRight:  () => 0,
    paddingTop:    () => 0,
    paddingBottom: () => 0,
};

const rule = (style = 'soft') => {
    if (style === 'thick')  return { canvas: [{ type: 'line', x1: 0, y1: 0, x2: 515, y2: 0, lineWidth: 2,   lineColor: C.navy }] };
    if (style === 'navy')   return { canvas: [{ type: 'line', x1: 0, y1: 0, x2: 515, y2: 0, lineWidth: 0.6, lineColor: C.navy }] };
    return                         { canvas: [{ type: 'line', x1: 0, y1: 0, x2: 515, y2: 0, lineWidth: 0.4, lineColor: C.borderSoft }] };
};

const sectionHeader = (title) => ({
    stack: [
        { ...rule('navy') },
        {
            text: title.toUpperCase(),
            fontSize: 7.5,
            bold: true,
            color: C.navy,
            letterSpacing: 1.2,
            margin: [0, 4, 0, 0],
        },
    ],
    margin: [0, 14, 0, 8],
});

const kv = (label, value, muted = false) => ({
    columns: [
        { text: label,        fontSize: 7, bold: true, color: C.muted, width: 105 },
        { text: value ?? '—', fontSize: 7,             color: muted ? C.light : C.dark },
    ],
    margin: [0, 2, 0, 2],
});

const badge = (text) => ({
    table: {
        widths: ['auto'],
        body: [[{
            text,
            fontSize: 6.5,
            bold: true,
            color: C.navy,
            border: [true, true, true, true],
            borderColor: [C.navy, C.navy, C.navy, C.navy],
            margin: [5, 2, 5, 2],
        }]],
    },
    layout: 'noBorders',
});

// ── Función expuesta ─────────────────────────────────────────────────
window.downloadSystemPdf = async function (systemId, btnEl) {
    const originalHTML = btnEl?.innerHTML ?? '';
    if (btnEl) {
        btnEl.disabled = true;
        btnEl.innerHTML = `<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg>`;
    }
    try {
        const res  = await fetch(`/systems/${systemId}/pdf-data`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        const data = await res.json();
        const doc  = buildDoc(data);
        const filename = `ficha_${(data.acronym || data.id)}_${data.generated_at.slice(0,10).replace(/\//g,'')}.pdf`;
        pdfMake.createPdf(doc).download(filename);
    } catch (e) {
        console.error('PDF error:', e);
        if (window.sgToast) sgToast('error', 'No se pudo generar el PDF');
    } finally {
        if (btnEl) { btnEl.disabled = false; btnEl.innerHTML = originalHTML; }
    }
};

// ── Constructor principal ────────────────────────────────────────────
function buildDoc(d) {
    const generatedAt = d.generated_at ?? new Date().toLocaleString('es-PE');
    const generatedBy = d.generated_by ?? '—';
    const content = [];

    // ════════════════════════════════════════════════════════════════
    // TÍTULO DEL SISTEMA  (página 1)
    // ════════════════════════════════════════════════════════════════

    content.push({
        text: d.name,
        fontSize: 16,
        bold: true,
        color: C.navy,
        alignment: 'center',
        margin: [0, 0, 0, 4],
    });

    if (d.acronym) {
        content.push({
            text: `[ ${d.acronym} ]`,
            fontSize: 8.5,
            color: C.muted,
            alignment: 'center',
            margin: [0, 0, 0, 5],
        });
    }


    // ════════════════════════════════════════════════════════════════
    // INFORMACIÓN GENERAL
    // ════════════════════════════════════════════════════════════════
    content.push(sectionHeader('Información General'));
    content.push({
        columns: [
            {
                stack: [
                    kv('Sistema',     d.name),
                    kv('Acrónimo',    d.acronym),
                    kv('Área',        d.area),
                    kv('Responsable', d.responsible),
                    kv('Estado',      d.status),
                ],
                width: '50%',
            },
            {
                stack: [
                    kv('Entorno',      d.infrastructure?.environment),
                    kv('Servidor web', d.infrastructure?.web_server),
                    kv('SSL',          d.infrastructure?.ssl_enabled ? 'Habilitado' : 'No habilitado'),
                    kv('Creado',       d.created_at),
                    kv('Actualizado',  d.updated_at),
                ],
                width: '50%',
            },
        ],
    });

    if (d.description) {
        content.push({ text: 'Descripción', fontSize: 7, bold: true, color: C.navy, margin: [0, 8, 0, 3] });
        content.push({
            table: {
                widths: ['*'],
                body: [[{
                    text: d.description,
                    fontSize: 7.5,
                    color: C.medium,
                    italics: true,
                    margin: [10, 6, 10, 6],
                    border: [true, false, false, false],
                    borderColor: [C.navy, null, null, null],
                }]],
            },
            layout: {
                hLineWidth: () => 0,
                vLineWidth: (i) => i === 0 ? 2 : 0,
                vLineColor: () => C.navy,
            },
        });
    }

    // ════════════════════════════════════════════════════════════════
    // STACK TECNOLÓGICO
    // ════════════════════════════════════════════════════════════════
    if (d.tech_stack?.length > 0) {
        content.push(sectionHeader('Stack Tecnológico'));
        const chunkSize = 9;
        for (let i = 0; i < d.tech_stack.length; i += chunkSize) {
            const row    = d.tech_stack.slice(i, i + chunkSize);
            const padded = [...row, ...Array(chunkSize - row.length).fill(null)];
            content.push({
                table: {
                    widths: padded.map(() => `${100 / chunkSize}%`),
                    body: [[
                        ...padded.map(tag => tag
                            ? {
                                text: tag,
                                fontSize: 6.5,
                                color: C.navy,
                                alignment: 'center',
                                margin: [3, 3, 3, 3],
                                border: [true, true, true, true],
                                borderColor: [C.navy, C.navy, C.navy, C.navy],
                              }
                            : { text: '', border: [false, false, false, false] }
                        ),
                    ]],
                },
                layout: {
                    hLineWidth: () => 0.5,
                    vLineWidth: () => 0.5,
                    hLineColor: () => C.navy,
                    vLineColor: () => C.navy,
                    paddingLeft:   () => 2,
                    paddingRight:  () => 2,
                    paddingTop:    () => 2,
                    paddingBottom: () => 2,
                },
                margin: [0, 0, 0, 3],
            });
        }
    }

    // ════════════════════════════════════════════════════════════════
    // INFRAESTRUCTURA
    // ════════════════════════════════════════════════════════════════
    if (d.infrastructure) {
        content.push(sectionHeader('Infraestructura'));
        const infra = d.infrastructure;
        const portLabel = infra.port ? `Puerto :${infra.port}` : null;
        const accessStr = infra.public_ip && infra.port
            ? `${infra.public_ip}:${infra.port}`
            : (infra.public_ip || portLabel || '—');
        content.push({
            columns: [
                {
                    stack: [
                        kv('Servidor',    infra.server_name),
                        kv('IP expuesta', infra.public_ip ?? infra.server_ip),
                        kv('Puerto',      infra.port ? `:${infra.port}` : null),
                        kv('URL',         infra.system_url),
                    ],
                    width: '50%',
                },
                {
                    stack: [
                        kv('Acceso directo', accessStr !== '—' ? accessStr : null),
                        kv('SSL',            infra.ssl_enabled ? 'Habilitado' : 'No'),
                        kv('Certificado',    infra.ssl_cert),
                        kv('Notas',          infra.notes, true),
                    ],
                    width: '50%',
                },
            ],
        });
    }

    // ════════════════════════════════════════════════════════════════
    // RESPONSABLES
    // ════════════════════════════════════════════════════════════════
    if (d.responsibles?.length > 0) {
        content.push(sectionHeader('Responsables'));
        content.push({
            table: {
                widths: ['*', 130, 65, 50],
                headerRows: 1,
                body: [
                    [ th('Nombre'), th('Rol / Nivel'), th('Desde'), th('Estado') ],
                    ...d.responsibles.map((r) => [
                        td(r.name, { bold: true, color: C.dark }),
                        td(r.level),
                        td(r.assigned_at),
                        td(r.active ? 'Activo' : 'Inactivo', {
                            alignment: 'center',
                            bold: r.active,
                            color: r.active ? C.navy : C.light,
                            italics: !r.active,
                        }),
                    ]),
                ],
            },
            layout: tableLayout,
        });
    }

    // ════════════════════════════════════════════════════════════════
    // BASES DE DATOS
    // ════════════════════════════════════════════════════════════════
    if (d.databases?.length > 0) {
        content.push(sectionHeader('Bases de Datos'));
        content.push({
            table: {
                widths: ['*', 80, '*', 45],
                headerRows: 1,
                body: [
                    [ th('Nombre'), th('Motor'), th('Host'), th('Puerto') ],
                    ...d.databases.map(db => [
                        td(db.name, { bold: true }),
                        td(db.engine),
                        td(db.host, { fontSize: 7 }),
                        td(db.port?.toString(), { alignment: 'center' }),
                    ]),
                ],
            },
            layout: tableLayout,
        });
    }

    // ════════════════════════════════════════════════════════════════
    // SERVICIOS / APIs
    // ════════════════════════════════════════════════════════════════
    if (d.services?.length > 0) {
        content.push(sectionHeader('Servicios y APIs'));
        content.push({
            table: {
                widths: ['*', 50, 55, '*', 44, 30],
                headerRows: 1,
                body: [
                    [ th('Nombre'), th('Tipo'), th('Dirección'), th('Endpoint'), th('Auth'), th('Activo') ],
                    ...d.services.map(s => [
                        td(s.name, { bold: true }),
                        td(s.type),
                        td(s.direction),
                        td(s.endpoint, { fontSize: 6.5 }),
                        td(s.auth),
                        td(s.active ? 'Sí' : 'No', { alignment: 'center', bold: s.active, color: s.active ? C.navy : C.light }),
                    ]),
                ],
            },
            layout: tableLayout,
        });
    }

    // ════════════════════════════════════════════════════════════════
    // REPOSITORIOS
    // ════════════════════════════════════════════════════════════════
    if (d.repositories?.length > 0) {
        content.push(sectionHeader('Repositorios'));
        content.push({
            table: {
                widths: ['*', 70, '*', 80],
                headerRows: 1,
                body: [
                    [ th('Nombre'), th('Proveedor'), th('URL'), th('Rama') ],
                    ...d.repositories.map(r => [
                        td(r.name, { bold: true }),
                        td(r.provider),
                        td(r.url, { fontSize: 6.5 }),
                        td(r.branch),
                    ]),
                ],
            },
            layout: tableLayout,
        });
    }

    // ════════════════════════════════════════════════════════════════
    // INTEGRACIONES
    // ════════════════════════════════════════════════════════════════
    const hasFrom = d.integrations_from?.length > 0;
    const hasTo   = d.integrations_to?.length   > 0;
    if (hasFrom || hasTo) {
        content.push(sectionHeader('Integraciones'));
        content.push({
            columns: [
                hasFrom ? {
                    stack: [
                        { text: 'Recibe servicios de:', fontSize: 7, bold: true, color: C.navy, margin: [0, 0, 0, 4] },
                        ...d.integrations_from.map(i => ({
                            columns: [
                                { canvas: [{ type: 'ellipse', x: 3, y: 4, r1: 2, r2: 2, color: C.navy }], width: 10 },
                                { text: i.target + (i.protocol ? ` — ${i.protocol}` : ''), fontSize: 7.5, color: C.dark },
                            ],
                            margin: [0, 1.5, 0, 1.5],
                        })),
                    ],
                    width: '50%',
                } : { text: '', width: '50%' },
                hasTo ? {
                    stack: [
                        { text: 'Expone servicios a:', fontSize: 7, bold: true, color: C.navy, margin: [0, 0, 0, 4] },
                        ...d.integrations_to.map(i => ({
                            columns: [
                                { canvas: [{ type: 'ellipse', x: 3, y: 4, r1: 2, r2: 2, color: C.medium }], width: 10 },
                                { text: i.source + (i.protocol ? ` — ${i.protocol}` : ''), fontSize: 7.5, color: C.dark },
                            ],
                            margin: [0, 1.5, 0, 1.5],
                        })),
                    ],
                    width: '50%',
                } : { text: '', width: '50%' },
            ],
        });
    }

    // ════════════════════════════════════════════════════════════════
    // VERSIONES
    // ════════════════════════════════════════════════════════════════
    if (d.versions?.length > 0) {
        content.push(sectionHeader('Historial de Versiones'));
        content.push({
            table: {
                widths: [75, 75, '*'],
                headerRows: 1,
                body: [
                    [ th('Versión'), th('Fecha'), th('Notas') ],
                    ...d.versions.map(v => [
                        td(v.version, { bold: true, color: C.navy }),
                        td(v.release_date),
                        td(v.notes, { color: C.muted }),
                    ]),
                ],
            },
            layout: tableLayout,
        });
    }

    // ════════════════════════════════════════════════════════════════
    // HISTORIAL DE ESTADO
    // ════════════════════════════════════════════════════════════════
    if (d.status_logs?.length > 0) {
        content.push(sectionHeader('Historial de Estado'));
        content.push({
            table: {
                widths: [110, 75, '*'],
                headerRows: 1,
                body: [
                    [ th('Estado'), th('Fecha'), th('Motivo') ],
                    ...d.status_logs.map(l => [
                        td(l.status, { bold: true }),
                        td(l.date),
                        td(l.notes, { color: C.muted }),
                    ]),
                ],
            },
            layout: tableLayout,
        });
    }

    // ════════════════════════════════════════════════════════════════
    // OBSERVACIONES
    // ════════════════════════════════════════════════════════════════
    if (d.observations) {
        content.push(sectionHeader('Observaciones'));
        content.push({
            table: {
                widths: ['*'],
                body: [[{
                    text: d.observations,
                    fontSize: 7.5,
                    color: C.medium,
                    italics: true,
                    margin: [10, 6, 10, 6],
                    border: [true, false, false, false],
                    borderColor: [C.navy, null, null, null],
                }]],
            },
            layout: {
                hLineWidth: () => 0,
                vLineWidth: (i) => i === 0 ? 2 : 0,
                vLineColor: () => C.navy,
            },
        });
    }

    // ════════════════════════════════════════════════════════════════
    // DOCUMENT DEFINITION
    // ════════════════════════════════════════════════════════════════
    return {
        pageSize: 'A4',
        pageMargins: [40, 68, 40, 54],

        content,

        // ── HEADER (todas las páginas) ───────────────────────────────
        header: (currentPage) => ({
            stack: [
                {
                    canvas: [
                        { type: 'line', x1: 40, y1: 0,   x2: 555, y2: 0,   lineWidth: 2.2, lineColor: C.navy },
                        { type: 'line', x1: 40, y1: 4,   x2: 555, y2: 4,   lineWidth: 0.4, lineColor: C.borderSoft },
                    ],
                    margin: [0, 10, 0, 6],
                },
                {
                    columns: [
                        {
                            stack: [
                                { text: 'UNIVERSIDAD NACIONAL MADRE DE DIOS', fontSize: 8, bold: true, color: C.navy },
                                { text: 'Oficina de Tecnologías de la Información — OTI', fontSize: 6.5, color: C.muted, margin: [0, 1, 0, 0] },
                            ],
                            margin: [40, 0, 0, 0],
                        },
                        {
                            stack: [
                                { text: 'FICHA TÉCNICA DE SISTEMA', fontSize: 7, bold: true, color: C.navy, alignment: 'right', letterSpacing: 0.5 },
                                {
                                    text: currentPage > 1
                                        ? d.name.toUpperCase() + (d.acronym ? `  [${d.acronym}]` : '')
                                        : `SGATI  ·  Código: ${d.acronym ?? d.id}`,
                                    fontSize: 6.5,
                                    color: C.muted,
                                    alignment: 'right',
                                    margin: [0, 1, 0, 0],
                                },
                            ],
                            margin: [0, 0, 40, 0],
                        },
                    ],
                },
                {
                    canvas: [{ type: 'line', x1: 40, y1: 0, x2: 555, y2: 0, lineWidth: 0.4, lineColor: C.borderSoft }],
                    margin: [0, 5, 0, 0],
                },
            ],
        }),

        // ── FOOTER (todas las páginas) ───────────────────────────────
        footer: (currentPage, pageCount) => ({
            stack: [
                {
                    canvas: [
                        { type: 'line', x1: 40, y1: 0, x2: 555, y2: 0, lineWidth: 1.2, lineColor: C.navy },
                        { type: 'line', x1: 40, y1: 3, x2: 555, y2: 3, lineWidth: 0.3, lineColor: C.borderSoft },
                    ],
                    margin: [0, 0, 0, 6],
                },
                {
                    columns: [
                        {
                            stack: [
                                {
                                    text: [
                                        { text: 'Elaborado el  ', fontSize: 6, color: C.light },
                                        { text: generatedAt, fontSize: 6, color: C.medium, bold: true },
                                    ],
                                },
                                {
                                    text: [
                                        { text: 'Generado por  ', fontSize: 6, color: C.light },
                                        { text: generatedBy, fontSize: 6, color: C.navy, bold: true },
                                    ],
                                    margin: [0, 2, 0, 0],
                                },
                            ],
                            width: '60%',
                            margin: [40, 0, 0, 0],
                        },
                        {
                            stack: [
                                {
                                    text: 'SGATI — Oficina de Tecnologías de la Información · UNAMAD',
                                    fontSize: 5.5,
                                    color: C.light,
                                    alignment: 'right',
                                    italics: true,
                                },
                                {
                                    text: `Página ${currentPage} de ${pageCount}`,
                                    fontSize: 7,
                                    color: C.navy,
                                    bold: true,
                                    alignment: 'right',
                                    margin: [0, 2, 0, 0],
                                },
                            ],
                            width: '40%',
                            margin: [0, 0, 40, 0],
                        },
                    ],
                },
            ],
            margin: [0, 10, 0, 0],
        }),

        defaultStyle: {
            font: 'Roboto',
            fontSize: 8,
            color: C.dark,
        },
    };
}
