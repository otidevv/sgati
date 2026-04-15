import pdfMake from 'pdfmake/build/pdfmake';
import pdfFonts from 'pdfmake/build/vfs_fonts';

if (pdfFonts?.vfs)           pdfMake.vfs = pdfFonts.vfs;
else if (pdfFonts?.pdfMake)  pdfMake.vfs = pdfFonts.pdfMake.vfs;

// ── Paleta ───────────────────────────────────────────────────────────
const C = {
    navy:       '#0f2d5e',   // header principal
    navyDark:   '#091e42',   // header más oscuro
    blue:       '#1d4ed8',   // acento secciones
    blueMid:    '#3b82f6',   // acento medio
    blueLight:  '#dbeafe',   // fondo etiquetas tech
    blueUltra:  '#eff6ff',   // fondo filas alternas
    infoBar:    '#f0f7ff',   // barra de métricas
    teal:       '#0d9488',   // acento secundario
    text:       '#111827',
    textSoft:   '#374151',
    muted:      '#6b7280',
    mutedLight: '#9ca3af',
    border:     '#e5e7eb',
    borderDark: '#d1d5db',
    white:      '#ffffff',
    green:      '#166534',
    greenBg:    '#dcfce7',
    red:        '#991b1b',
    redBg:      '#fee2e2',
    amber:      '#92400e',
    amberBg:    '#fef3c7',
    rowAlt:     '#f8fafc',
};

// ── Helpers ──────────────────────────────────────────────────────────
const th = (text, w) => ({
    text,
    fontSize: 7.5,
    bold: true,
    color: C.white,
    fillColor: C.blue,
    margin: [5, 4, 5, 4],
    border: [false, false, false, false],
    ...(w ? { width: w } : {}),
});

const td = (text, opts = {}) => ({
    text: text ?? '—',
    fontSize: 8,
    color: C.textSoft,
    margin: [5, 3, 5, 3],
    border: [false, false, false, false],
    ...opts,
});

const tableLayout = {
    hLineWidth: (i, node) => (i === 0 || i === node.table.body.length) ? 0 : 0.5,
    vLineWidth: () => 0,
    hLineColor: () => C.border,
    fillColor:  (row) => row === 0 ? null : row % 2 === 0 ? C.rowAlt : null,
    paddingLeft:   () => 0,
    paddingRight:  () => 0,
    paddingTop:    () => 0,
    paddingBottom: () => 0,
};


const sectionHeader = (title) => ({
    columns: [
        {
            canvas: [{ type: 'rect', x: 0, y: 0, w: 4, h: 13, r: 2, color: C.blueMid }],
            width: 10,
            margin: [0, 1, 0, 0],
        },
        {
            text: title.toUpperCase(),
            fontSize: 8.5,
            bold: true,
            color: C.navy,
            letterSpacing: 0.8,
            margin: [2, 1, 0, 0],
        },
    ],
    margin: [0, 14, 0, 6],
});

const kv = (label, value, muted = false) => ({
    columns: [
        { text: label, fontSize: 7.5, bold: true, color: C.muted,    width: 110 },
        { text: value ?? '—', fontSize: 7.5,       color: muted ? C.muted : C.textSoft },
    ],
    margin: [0, 2.5, 0, 2.5],
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
    // HEADER PRINCIPAL  (fondo azul marino, texto blanco)
    // ════════════════════════════════════════════════════════════════
    content.push({
        table: {
            widths: ['*', 'auto'],
            body: [[
                // — columna izquierda —
                {
                    stack: [
                        {
                            text: 'FICHA TÉCNICA DE SISTEMA',
                            fontSize: 7,
                            bold: true,
                            color: '#93c5fd',
                            letterSpacing: 1.5,
                            margin: [0, 0, 0, 4],
                        },
                        {
                            text: d.name,
                            fontSize: 20,
                            bold: true,
                            color: C.white,
                            margin: [0, 0, 0, 4],
                        },
                        ...(d.acronym ? [{
                            table: {
                                widths: ['auto'],
                                body: [[{
                                    text: d.acronym,
                                    fontSize: 8,
                                    bold: true,
                                    color: C.navy,
                                    fillColor: '#93c5fd',
                                    margin: [6, 2, 6, 2],
                                    border: [false, false, false, false],
                                }]],
                            },
                            layout: 'noBorders',
                            margin: [0, 0, 0, 0],
                        }] : []),
                    ],
                    fillColor: C.navy,
                    border: [false, false, false, false],
                    margin: [20, 18, 10, 18],
                },
                // — columna derecha —
                {
                    stack: [
                        {
                            text: 'SGATI',
                            fontSize: 14,
                            bold: true,
                            color: '#93c5fd',
                            alignment: 'right',
                            margin: [0, 0, 0, 2],
                        },
                        {
                            text: 'OTI — UNAMAD',
                            fontSize: 7,
                            color: '#bfdbfe',
                            alignment: 'right',
                            margin: [0, 0, 0, 10],
                        },
                        {
                            table: {
                                widths: ['auto'],
                                body: [[{
                                    text: d.status,
                                    fontSize: 8,
                                    bold: true,
                                    color: C.navy,
                                    fillColor: '#6ee7b7',
                                    margin: [8, 3, 8, 3],
                                    alignment: 'center',
                                    border: [false, false, false, false],
                                }]],
                            },
                            layout: 'noBorders',
                        },
                    ],
                    fillColor: C.navy,
                    border: [false, false, false, false],
                    margin: [0, 18, 20, 18],
                },
            ]],
        },
        layout: 'noBorders',
        margin: [0, 0, 0, 0],
    });

    // ════════════════════════════════════════════════════════════════
    // BARRA DE MÉTRICAS CLAVE  (4 columnas)
    // ════════════════════════════════════════════════════════════════
    const metrics = [
        { label: 'ÁREA',       value: d.area        ?? '—' },
        { label: 'RESPONSABLE', value: d.responsible ?? '—' },
        { label: 'ENTORNO',    value: d.infrastructure?.environment ?? '—' },
        { label: 'TECNOLOGÍAS', value: d.tech_stack?.length ? `${d.tech_stack.length} registradas` : '—' },
    ];

    content.push({
        table: {
            widths: ['25%', '25%', '25%', '25%'],
            body: [[
                ...metrics.map((m, i) => ({
                    stack: [
                        { text: m.label, fontSize: 6.5, bold: true, color: C.muted, letterSpacing: 0.5, margin: [0, 0, 0, 2] },
                        { text: m.value, fontSize: 8.5, bold: true, color: C.navy },
                    ],
                    fillColor: C.infoBar,
                    border: [
                        i > 0, false, false, false,  // línea izq excepto primera
                    ],
                    borderColor: [C.borderDark, null, null, null],
                    margin: [12, 8, 12, 8],
                })),
            ]],
        },
        layout: {
            hLineWidth: () => 0,
            vLineWidth: (i) => i > 0 && i < 4 ? 0.5 : 0,
            vLineColor: () => C.borderDark,
        },
        margin: [0, 0, 0, 0],
    });

    // Separador accent
    content.push({
        canvas: [{ type: 'rect', x: 0, y: 0, w: 515, h: 2.5, color: C.blueMid }],
        margin: [0, 0, 0, 0],
    });

    // ════════════════════════════════════════════════════════════════
    // INFORMACIÓN GENERAL
    // ════════════════════════════════════════════════════════════════
    content.push(sectionHeader('Información General'));
    content.push({
        columns: [
            {
                stack: [
                    kv('Sistema',      d.name),
                    kv('Acrónimo',     d.acronym),
                    kv('Área',         d.area),
                    kv('Responsable',  d.responsible),
                    kv('Estado',       d.status),
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
        content.push({ text: 'Descripción', fontSize: 7.5, bold: true, color: C.muted, margin: [0, 8, 0, 2] });
        content.push({
            table: {
                widths: ['*'],
                body: [[{
                    text: d.description,
                    fontSize: 8,
                    color: C.textSoft,
                    margin: [8, 6, 8, 6],
                    border: [false, false, false, false],
                }]],
            },
            layout: {
                hLineWidth: () => 0,
                vLineWidth: (i) => i === 0 ? 2 : 0,
                vLineColor: () => C.blueMid,
            },
            margin: [0, 0, 0, 0],
        });
    }

    // ════════════════════════════════════════════════════════════════
    // STACK TECNOLÓGICO
    // ════════════════════════════════════════════════════════════════
    if (d.tech_stack?.length > 0) {
        content.push(sectionHeader('Stack Tecnológico'));
        // Chips en filas de hasta 8 por fila
        const chunkSize = 8;
        for (let i = 0; i < d.tech_stack.length; i += chunkSize) {
            const row = d.tech_stack.slice(i, i + chunkSize);
            const padded = [...row, ...Array(chunkSize - row.length).fill(null)];
            content.push({
                table: {
                    widths: padded.map(() => `${100 / chunkSize}%`),
                    body: [[
                        ...padded.map(tag => tag
                            ? {
                                text: tag,
                                fontSize: 7.5,
                                bold: true,
                                color: C.blue,
                                fillColor: C.blueLight,
                                alignment: 'center',
                                margin: [4, 3, 4, 3],
                                border: [false, false, false, false],
                              }
                            : { text: '', border: [false, false, false, false] }
                        ),
                    ]],
                },
                layout: {
                    hLineWidth: () => 0,
                    vLineWidth: () => 0,
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
        content.push({
            columns: [
                {
                    stack: [
                        kv('Servidor',   infra.server_name),
                        kv('IP Pública', infra.server_ip),
                        kv('URL',        infra.system_url),
                    ],
                    width: '50%',
                },
                {
                    stack: [
                        kv('SSL',         infra.ssl_enabled ? 'Habilitado' : 'No'),
                        kv('Certificado', infra.ssl_cert),
                        kv('Notas',       infra.notes, true),
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
                        td(r.name, { bold: true }),
                        td(r.level),
                        td(r.assigned_at),
                        {
                            text: r.active ? 'Activo' : 'Inactivo',
                            fontSize: 7,
                            bold: true,
                            color:     r.active ? C.green : C.red,
                            fillColor: r.active ? C.greenBg : C.redBg,
                            alignment: 'center',
                            margin: [4, 3, 4, 3],
                            border: [false, false, false, false],
                        },
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
                        td(db.host, { fontSize: 7.5 }),
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
                widths: ['*', 55, 60, '*', 48, 32],
                headerRows: 1,
                body: [
                    [ th('Nombre'), th('Tipo'), th('Dirección'), th('Endpoint'), th('Auth'), th('Activo') ],
                    ...d.services.map(s => [
                        td(s.name, { bold: true }),
                        td(s.type),
                        td(s.direction),
                        td(s.endpoint, { fontSize: 7 }),
                        td(s.auth),
                        {
                            text: s.active ? 'Sí' : 'No',
                            fontSize: 7,
                            bold: true,
                            color:     s.active ? C.green : C.muted,
                            fillColor: s.active ? C.greenBg : '#f3f4f6',
                            alignment: 'center',
                            margin: [4, 3, 4, 3],
                            border: [false, false, false, false],
                        },
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
                        td(r.url, { fontSize: 7 }),
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
                        { text: 'Consume servicios de:', fontSize: 7.5, bold: true, color: C.muted, margin: [0, 0, 0, 4] },
                        ...d.integrations_from.map(i => ({
                            columns: [
                                { canvas: [{ type: 'ellipse', x: 3, y: 4, r1: 2.5, r2: 2.5, color: C.blueMid }], width: 10 },
                                { text: i.target + (i.protocol ? ` — ${i.protocol}` : ''), fontSize: 8, color: C.textSoft },
                            ],
                            margin: [0, 1.5, 0, 1.5],
                        })),
                    ],
                    width: '50%',
                } : { text: '', width: '50%' },
                hasTo ? {
                    stack: [
                        { text: 'Expone servicios a:', fontSize: 7.5, bold: true, color: C.muted, margin: [0, 0, 0, 4] },
                        ...d.integrations_to.map(i => ({
                            columns: [
                                { canvas: [{ type: 'ellipse', x: 3, y: 4, r1: 2.5, r2: 2.5, color: C.teal }], width: 10 },
                                { text: i.source + (i.protocol ? ` — ${i.protocol}` : ''), fontSize: 8, color: C.textSoft },
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
                widths: [80, 80, '*'],
                headerRows: 1,
                body: [
                    [ th('Versión'), th('Fecha'), th('Notas') ],
                    ...d.versions.map(v => [
                        td(v.version, { bold: true, color: C.blue }),
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
                widths: [120, 80, '*'],
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
                    fontSize: 8,
                    color: C.textSoft,
                    italics: true,
                    margin: [8, 6, 8, 6],
                    border: [false, false, false, false],
                    fillColor: '#fffbeb',
                }]],
            },
            layout: {
                hLineWidth: () => 0,
                vLineWidth: (i) => i === 0 ? 2.5 : 0,
                vLineColor: () => '#f59e0b',
            },
        });
    }

    // ════════════════════════════════════════════════════════════════
    // DOCUMENT DEFINITION
    // ════════════════════════════════════════════════════════════════
    return {
        pageSize: 'A4',
        pageMargins: [30, 18, 30, 50],

        content,

        // ── HEADER  (solo pág > 1 para no pisar el hero) ────────────
        header: (currentPage) => {
            if (currentPage === 1) return null;
            return {
                columns: [
                    {
                        stack: [
                            { text: 'FICHA TÉCNICA — ' + d.name.toUpperCase(), fontSize: 7, bold: true, color: C.white },
                            ...(d.acronym ? [{ text: d.acronym, fontSize: 6, color: '#93c5fd' }] : []),
                        ],
                        margin: [30, 10, 0, 0],
                    },
                    {
                        text: 'SGATI · OTI UNAMAD',
                        fontSize: 7,
                        color: '#93c5fd',
                        alignment: 'right',
                        margin: [0, 10, 30, 0],
                    },
                ],
                background: C.navy,
            };
        },

        // ── FOOTER  (todas las páginas) ──────────────────────────────
        footer: (currentPage, pageCount) => ({
            stack: [
                // Línea accent
                {
                    canvas: [{ type: 'rect', x: 30, y: 0, w: 535, h: 1.5, color: C.blue }],
                    margin: [0, 0, 0, 4],
                },
                // Tres columnas
                {
                    columns: [
                        // Izquierda: fecha y hora
                        {
                            stack: [
                                { text: 'Elaborado el', fontSize: 6, color: C.mutedLight, bold: true, letterSpacing: 0.4 },
                                { text: generatedAt, fontSize: 7.5, color: C.textSoft, bold: true },
                            ],
                            width: '33%',
                            margin: [30, 0, 0, 0],
                        },
                        // Centro: quien generó
                        {
                            stack: [
                                { text: 'Generado por', fontSize: 6, color: C.mutedLight, bold: true, letterSpacing: 0.4, alignment: 'center' },
                                { text: generatedBy, fontSize: 7.5, color: C.navy, bold: true, alignment: 'center' },
                            ],
                            width: '34%',
                        },
                        // Derecha: número de página
                        {
                            stack: [
                                { text: 'PÁGINA', fontSize: 6, color: C.mutedLight, bold: true, letterSpacing: 0.4, alignment: 'right' },
                                {
                                    columns: [
                                        { text: `${currentPage}`, fontSize: 14, bold: true, color: C.blue, alignment: 'right', width: 'auto' },
                                        { text: ` / ${pageCount}`, fontSize: 8, color: C.muted, margin: [1, 6, 0, 0], width: 'auto' },
                                    ],
                                    alignment: 'right',
                                },
                            ],
                            width: '33%',
                            margin: [0, 0, 30, 0],
                        },
                    ],
                },
            ],
            margin: [0, 6, 0, 0],
        }),

        defaultStyle: {
            font: 'Roboto',
            fontSize: 9,
            color: C.text,
        },
    };
}
