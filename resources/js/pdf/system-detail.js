import { pdfMake } from './builder.js';
import { C, th, td, tableLayout, rule, sectionHeader, kv, kvMono, kvPill, pillCell } from './theme.js';

const statusActive = (s) => s && !/(inactivo|descontinuado|archivado|suspendido)/i.test(s);

function buildDoc(d, logoBase64 = null) {
    const generatedAt = d.generated_at ?? new Date().toLocaleString('es-PE');
    const generatedBy = d.generated_by ?? '—';
    const content = [];

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

    // ── Información General ──────────────────────────────────────────
    content.push(sectionHeader('Información General'));
    content.push({
        columns: [
            {
                stack: [
                    kv('Sistema',     d.name),
                    kv('Acrónimo',    d.acronym),
                    kv('Área',        d.area),
                    kv('Responsables', (() => {
                        const n = d.responsibles?.length ?? 0;
                        return `${n} registrado${n !== 1 ? 's' : ''}`;
                    })()),
                    kv('Estado', d.status, !statusActive(d.status)),
                ],
                width: '50%',
            },
            {
                stack: [
                    kv('Entorno',      d.infrastructure?.environment),
                    kv('Servidor web', d.infrastructure?.web_server),
                    kv('SSL',          d.infrastructure?.ssl_enabled ? 'Habilitado' : 'No habilitado'),
                    kvMono('Creado',       d.created_at),
                    kvMono('Actualizado',  d.updated_at),
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

    // ── Infraestructura ──────────────────────────────────────────────
    if (d.infrastructure) {
        content.push(sectionHeader('Infraestructura'));
        const infra = d.infrastructure;
        content.push({
            columns: [
                {
                    stack: [
                        kvMono('Servidor',    infra.server_name),
                        kv('Sistema op.',     infra.operating_system),
                        kvMono('IP pública',  infra.server_ip   ? (infra.port ? `${infra.server_ip}:${infra.port}` : infra.server_ip)   : null),
                        kvMono('IP interna',  infra.internal_ip ? (infra.port ? `${infra.internal_ip}:${infra.port}` : infra.internal_ip) : null),
                        kvMono('URL',         infra.system_url),
                    ],
                    width: '50%',
                },
                {
                    stack: [
                        kv('Entorno',         infra.environment),
                        kv('Web server',      infra.web_server),
                        kv('SSL',             infra.ssl_enabled ? 'Habilitado' : 'No'),
                        kvMono('Certificado', infra.ssl_cert),
                        kv('Notas',           infra.notes, true),
                    ],
                    width: '50%',
                },
            ],
        });
    }

    // ── Stack Tecnológico ────────────────────────────────────────────
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
                            ? { text: tag, fontSize: 6.5, color: C.navy, alignment: 'center', margin: [3, 3, 3, 3], border: [true, true, true, true], borderColor: [C.navy, C.navy, C.navy, C.navy] }
                            : { text: '', border: [false, false, false, false] }
                        ),
                    ]],
                },
                layout: {
                    hLineWidth: () => 0.5, vLineWidth: () => 0.5,
                    hLineColor: () => C.navy, vLineColor: () => C.navy,
                    paddingLeft: () => 2, paddingRight: () => 2,
                    paddingTop:  () => 2, paddingBottom: () => 2,
                },
                margin: [0, 0, 0, 3],
            });
        }
    }

    // ── Contenedores ─────────────────────────────────────────────────
    if (d.containers?.length > 0) {
        content.push(sectionHeader('Contenedores'));
        content.push({
            table: {
                widths: ['*', '*', 55, 55, '*'],
                headerRows: 1,
                body: [
                    [th('Contenedor'), th('Imagen'), th('Puerto'), th('Estado'), th('Rol')],
                    ...d.containers.map(c => [
                        td(c.name,   { bold: true, color: C.navy }),
                        td(c.image,  { fontSize: 7, color: C.medium }),
                        td(c.port ?? '—', { alignment: 'center', fontSize: 7, color: C.medium }),
                        pillCell(c.status ?? '—', statusActive(c.status)),
                        td(c.role),
                    ]),
                ],
            },
            layout: tableLayout,
        });
    }

    // ── Responsables ─────────────────────────────────────────────────
    if (d.responsibles?.length > 0) {
        content.push(sectionHeader('Responsables'));
        content.push({
            table: {
                widths: ['*', 110, 100, 50, 42],
                headerRows: 1,
                body: [
                    [th('Nombre'), th('Nivel(es)'), th('Documento(s) de asignación'), th('Desde'), th('Estado')],
                    ...d.responsibles.map(r => {
                        const docs = r.documents ?? [];
                        const docsCell = docs.length > 0
                            ? {
                                stack: docs.map((doc, i) => ({
                                    text: doc,
                                    fontSize: 6.5,
                                    color: C.medium,
                                    margin: [0, i > 0 ? 2 : 0, 0, 0],
                                })),
                                margin: [5, 3, 5, 3],
                                border: [false, false, false, true],
                                borderColor: [null, null, null, C.borderSoft],
                            }
                            : td('—', { fontSize: 6.5, color: C.light, italics: true });
                        return [
                            td(r.name, { bold: true, color: C.dark }),
                            td(r.level, { fontSize: 7 }),
                            docsCell,
                            td(r.assigned_at),
                            td(r.active ? 'Activo' : 'Inactivo', {
                                alignment: 'center',
                                bold: r.active,
                                color: r.active ? C.navy : C.light,
                                italics: !r.active,
                            }),
                        ];
                    }),
                ],
            },
            layout: tableLayout,
        });
    }

    // ── Bases de Datos ───────────────────────────────────────────────
    if (d.databases?.length > 0) {
        content.push(sectionHeader('Bases de Datos'));
        content.push({
            table: {
                widths: ['*', 70, 55, '*', 55],
                headerRows: 1,
                body: [
                    [th('Nombre'), th('Motor'), th('Ambiente'), th('Host / Servidor'), th('Schema')],
                    ...d.databases.map(db => [
                        td(db.name,        { bold: true }),
                        td(db.engine),
                        td(db.environment),
                        td(db.host,        { fontSize: 7, color: C.medium }),
                        td(db.schema,      { fontSize: 7, color: C.medium }),
                    ]),
                ],
            },
            layout: tableLayout,
        });
    }

    // ── Servicios / APIs ─────────────────────────────────────────────
    if (d.services?.length > 0) {
        content.push(sectionHeader('Servicios y APIs'));
        content.push({
            table: {
                widths: ['*', 52, 52, '*', 40, 50, 31],
                headerRows: 1,
                body: [
                    [th('Nombre'), th('Tipo'), th('Dirección'), th('Endpoint'), th('Auth'), th('Entorno'), th('Activo')],
                    ...d.services.map(s => [
                        {
                            stack: [
                                { text: s.name, fontSize: 7.5, bold: true, color: C.dark },
                                ...(s.description ? [{ text: s.description, fontSize: 6.5, color: C.muted, italics: true, margin: [0, 1, 0, 0] }] : []),
                                ...(s.version    ? [{ text: `v${s.version}`, fontSize: 6.5, color: C.light, margin: [0, 1, 0, 0] }] : []),
                            ],
                            border: [false, false, false, true],
                            borderColor: [null, null, null, C.borderSoft],
                            margin: [5, 3, 5, 3],
                        },
                        td(s.type),
                        td(s.direction),
                        td(s.endpoint, { fontSize: 6.5, color: C.medium }),
                        td(s.auth),
                        td(s.environment),
                        td(s.active ? 'Sí' : 'No', {
                            alignment: 'center',
                            bold: s.active,
                            color: s.active ? C.navy : C.light,
                        }),
                    ]),
                ],
            },
            layout: tableLayout,
        });

        // Sub-sección: consumidores por cada servicio expuesto
        const exposed = d.services.filter(s => s.direction === 'Expuesto' && s.consumers?.length > 0);
        for (const s of exposed) {
            content.push({
                text: `Solicitantes / Consumidores — ${s.name}`,
                fontSize: 6.5,
                bold: true,
                color: C.navy,
                margin: [0, 8, 0, 3],
            });
            content.push({
                table: {
                    widths: ['*', 38, 60, '*', 42, 40],
                    headerRows: 1,
                    body: [
                        [
                            th('Solicitado por'),
                            th('Tipo'),
                            th('Auth'),
                            th('URL Gateway'),
                            th('Consultas'),
                            th('Estado'),
                        ],
                        ...s.consumers.map(c => [
                            td(c.organization, { fontSize: 6.5, color: C.medium }),
                            td(c.type,         { fontSize: 6.5 }),
                            
                            td(c.auth,         { fontSize: 6.5 }),
                            td(c.gateway_url,  { fontSize: 5.5, color: C.medium }),
                            td(c.total != null ? String(c.total) : '—', { fontSize: 6.5, alignment: 'center' }),
                            td(c.active ? 'Activo' : 'Inactivo', {
                                fontSize: 6.5,
                                alignment: 'center',
                                bold: c.active,
                                color: c.active ? C.navy : C.light,
                                italics: !c.active,
                            }),
                        ]),
                    ],
                },
                layout: tableLayout,
            });
        }
    }

    // ── Repositorios ─────────────────────────────────────────────────
    if (d.repositories?.length > 0) {
        content.push(sectionHeader('Repositorios'));
        content.push({
            table: {
                widths: ['*', 70, '*', 80],
                headerRows: 1,
                body: [
                    [th('Nombre'), th('Proveedor'), th('URL'), th('Rama')],
                    ...d.repositories.map(r => [
                        td(r.name, { bold: true }),
                        td(r.provider),
                        td(r.url, { fontSize: 6.5, color: C.medium }),
                        td(r.branch),
                    ]),
                ],
            },
            layout: tableLayout,
        });
    }

    // ── Integraciones ────────────────────────────────────────────────
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

    // ── Versiones ────────────────────────────────────────────────────
    if (d.versions?.length > 0) {
        content.push(sectionHeader('Historial de Versiones'));
        content.push({
            table: {
                widths: [75, 75, '*'],
                headerRows: 1,
                body: [
                    [th('Versión'), th('Fecha'), th('Notas')],
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

    // ── Historial de Estado ──────────────────────────────────────────
    if (d.status_logs?.length > 0) {
        content.push(sectionHeader('Historial de Estado'));
        content.push({
            table: {
                widths: [110, 75, '*'],
                headerRows: 1,
                body: [
                    [th('Estado'), th('Fecha'), th('Motivo')],
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

    // ── Observaciones ────────────────────────────────────────────────
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

    return {
        pageSize: 'A4',
        pageMargins: [40, 68, 40, 54],
        content,
        header: (currentPage) => ({
            stack: [
                {
                    canvas: [
                        { type: 'line', x1: 40, y1: 0, x2: 555, y2: 0, lineWidth: 2.2, lineColor: C.navy },
                        { type: 'line', x1: 40, y1: 4, x2: 555, y2: 4, lineWidth: 0.4, lineColor: C.borderSoft },
                    ],
                    margin: [0, 10, 0, 6],
                },
                {
                    columns: [
                        {
                            columns: [
                                logoBase64 ? {
                                    image: logoBase64,
                                    width: 30,
                                    height: 30,
                                    margin: [0, 0, 8, 0],
                                } : {
                                    table: {
                                        widths: [26],
                                        body: [[{
                                            text: 'OTI',
                                            fontSize: 8,
                                            bold: true,
                                            color: C.navy,
                                            alignment: 'center',
                                            fillColor: C.navyLight,
                                            border: [true, true, true, true],
                                            borderColor: [C.navy, C.navy, C.navy, C.navy],
                                            margin: [0, 7, 0, 7],
                                        }]],
                                    },
                                    width: 'auto',
                                    margin: [0, 0, 8, 0],
                                },
                                {
                                    stack: [
                                        { text: 'UNIVERSIDAD NACIONAL AMAZÓNICA DE MADRE DE DIOS', fontSize: 8, bold: true, color: C.navy },
                                        { text: 'Oficina de Tecnologías de la Información — OTI', fontSize: 6.5, color: C.muted, margin: [0, 1, 0, 0] },
                                    ],
                                },
                            ],
                            margin: [40, 0, 0, 0],
                        },
                        {
                            stack: [
                                { text: 'FICHA TÉCNICA DE SISTEMA', fontSize: 7, bold: true, color: C.navy, alignment: 'right', letterSpacing: 0.5 },
                                {
                                    text: currentPage > 1
                                        ? d.name.toUpperCase() + (d.acronym ? `  [${d.acronym}]` : '')
                                        : `${window._appConfig?.name ?? ''}  ·  Código: ${d.acronym ?? d.id}`,
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
                                { text: [{ text: 'Elaborado el  ', fontSize: 6, color: C.light }, { text: generatedAt, fontSize: 6, color: C.medium, bold: true }] },
                                { text: [{ text: 'Generado por  ', fontSize: 6, color: C.light }, { text: generatedBy, fontSize: 6, color: C.navy, bold: true }], margin: [0, 2, 0, 0] },
                            ],
                            width: '60%',
                            margin: [40, 0, 0, 0],
                        },
                        {
                            stack: [
                                { text: `${window._appConfig?.name ?? ''} — Oficina de Tecnologías de la Información · UNAMAD`, fontSize: 5.5, color: C.light, alignment: 'right', italics: true },
                                { text: `Página ${currentPage} de ${pageCount}`, fontSize: 7, color: C.navy, bold: true, alignment: 'right', margin: [0, 2, 0, 0] },
                            ],
                            width: '40%',
                            margin: [0, 0, 40, 0],
                        },
                    ],
                },
            ],
            margin: [0, 10, 0, 0],
        }),
        defaultStyle: { font: 'Roboto', fontSize: 8, color: C.dark },
    };
}

window.downloadSystemPdf = async function (systemId, btnEl) {
    const originalHTML = btnEl?.innerHTML ?? '';
    if (btnEl) {
        btnEl.disabled = true;
        btnEl.innerHTML = `<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg>`;
    }
    try {
        const [res, logoBase64] = await Promise.all([
            fetch(`/systems/${systemId}/pdf-data`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } }),
            fetch('/images/sistema/logo.png')
                .then(r => r.ok ? r.blob() : null)
                .then(blob => blob ? new Promise(resolve => {
                    const reader = new FileReader();
                    reader.onload = () => resolve(reader.result);
                    reader.readAsDataURL(blob);
                }) : null)
                .catch(() => null),
        ]);
        const data = await res.json();
        const filename = `ficha_${(data.acronym || data.id)}_${data.generated_at.slice(0,10).replace(/\//g,'')}.pdf`;
        pdfMake.createPdf(buildDoc(data, logoBase64)).download(filename);
    } catch (e) {
        console.error('PDF error:', e);
        if (window.sgToast) sgToast('error', 'No se pudo generar el PDF');
    } finally {
        if (btnEl) { btnEl.disabled = false; btnEl.innerHTML = originalHTML; }
    }
};
