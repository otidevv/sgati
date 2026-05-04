import { pdfMake, wrapDoc } from './builder.js';
import { C, kv, kvMono, sectionHeader, rule, tableLayout, th, td, pill } from './theme.js';

// ── Helpers ──────────────────────────────────────────────────────────
const statusDot = (active) => ({
    columns: [
        {
            canvas: [{ type: 'ellipse', x: 4, y: 4, r1: 3, r2: 3, color: active ? '#16a34a' : '#9ca3af' }],
            width: 10,
        },
        {
            text: active ? 'Activo' : 'Inactivo',
            fontSize: 7,
            color: active ? '#16a34a' : C.light,
            margin: [0, 1, 0, 0],
        },
    ],
    columnGap: 2,
    margin: [0, 2, 0, 2],
});

function buildHeader(d) {
    return [
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
                        { text: 'FICHA TÉCNICA DE SERVIDOR', fontSize: 11, bold: true, color: C.navy, alignment: 'right' },
                        { text: `${d.generated_at}  ·  ${d.generated_by}`, fontSize: 5.5, color: C.muted, alignment: 'right', margin: [0, 1.5, 0, 0] },
                    ],
                    width: 'auto',
                },
            ],
            margin: [0, 0, 0, 4],
        },
        { canvas: [{ type: 'line', x1: 0, y1: 0, x2: 515, y2: 0, lineWidth: 2, lineColor: C.navy }], margin: [0, 0, 0, 10] },
    ];
}

const AUTH_TYPE_META = {
    credentials: { label: 'Usuario y contraseña',       color: '#1d4ed8' },
    windows:     { label: 'Windows (SSPI / AD)',         color: '#7c3aed' },
    kerberos:    { label: 'Kerberos / LDAP',             color: '#92400e' },
    iam:         { label: 'IAM / Cloud',                 color: '#c2410c' },
    trusted:     { label: 'Confianza local',             color: '#065f46' },
};

// Renders one DatabaseServer block (header + responsibles + databases table)
function buildDatabaseServerBlock(ds) {
    const blocks = [];

    const authMeta  = AUTH_TYPE_META[ds.auth_type] ?? AUTH_TYPE_META.credentials;
    const showUser  = ['credentials', 'kerberos', 'iam'].includes(ds.auth_type);

    // Sub-header: engine label + host + status
    blocks.push({
        columns: [
            {
                stack: [
                    { text: ds.name || ds.engine_label, fontSize: 8.5, bold: true, color: C.navy },
                    { text: ds.engine_label + (ds.name && ds.name !== ds.engine_label ? '' : ''), fontSize: 7, color: C.muted, margin: [0, 1, 0, 0] },
                ],
                width: '*',
            },
            {
                stack: [
                    statusDot(ds.is_active),
                    { text: ds.host, fontSize: 6.5, color: C.medium, margin: [0, 2, 0, 0] },
                ],
                width: 'auto',
                alignment: 'right',
            },
        ],
        margin: [0, 6, 0, 2],
    });

    // Auth type + optional admin user row
    blocks.push({
        columns: [
            {
                text: [
                    { text: 'Autenticación: ', fontSize: 6.5, color: C.muted },
                    { text: authMeta.label, fontSize: 6.5, bold: true, color: authMeta.color },
                ],
                width: '*',
            },
            ...(showUser && ds.admin_user ? [{
                text: [
                    { text: 'Usuario: ', fontSize: 6.5, color: C.muted },
                    { text: ds.admin_user, fontSize: 6.5, color: C.dark },
                ],
                width: 'auto',
                alignment: 'right',
            }] : []),
        ],
        margin: [0, 0, 0, 4],
    });

    // Responsibles of this database server (active only, 3 per row)
    const activeResp = (ds.responsibles ?? []).filter(r => r.active);
    if (activeResp.length) {
        for (let i = 0; i < activeResp.length; i += 3) {
            const chunk = activeResp.slice(i, i + 3);
            blocks.push({
                columns: [
                    ...chunk.map(r => ({
                        stack: [
                            { text: r.name, fontSize: 7, bold: true, color: C.dark },
                            { text: r.level, fontSize: 6, color: C.muted, margin: [0, 1, 0, 0] },
                        ],
                        width: '*',
                        margin: [0, 0, 10, 0],
                    })),
                    // fill empty slots so columns stay aligned
                    ...Array(3 - chunk.length).fill({ text: '', width: '*' }),
                ],
                margin: [0, 0, 0, 4],
            });
        }
    }

    // Notes
    if (ds.notes) {
        blocks.push({ text: ds.notes, fontSize: 6.5, color: C.muted, italics: true, margin: [0, 0, 0, 4] });
    }

    // Databases table
    if (ds.databases?.length) {
        const envCell = (env) => {
            const map = {
                producción:  { color: '#1b3a5c', fill: '#e8f0f7', border: '#1b3a5c' },
                staging:     { color: '#92400e', fill: '#fef3c7', border: '#d97706' },
                desarrollo:  { color: '#065f46', fill: '#d1fae5', border: '#059669' },
            };
            const key = (env ?? '').toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, '');
            const s = Object.entries(map).find(([k]) => key.includes(k.normalize('NFD').replace(/[̀-ͯ]/g, '')))?.[1]
                   ?? { color: C.muted, fill: null, border: C.borderSoft };
            return {
                stack: [{
                    table: { widths: ['auto'], body: [[{
                        text: (env ?? '—').toUpperCase(),
                        fontSize: 6, bold: true,
                        color: s.color, fillColor: s.fill,
                        border: [true, true, true, true],
                        borderColor: [s.border, s.border, s.border, s.border],
                        margin: [4, 2, 4, 2],
                    }]] },
                }],
                border: [false, false, false, true],
                borderColor: [null, null, null, C.borderSoft],
                margin: [4, 3, 4, 3],
                alignment: 'center',
            };
        };
        blocks.push({
            table: {
                widths: ['*', 90, 65, 60],
                headerRows: 1,
                body: [
                    [th('Sistema'), th('Base de Datos'), th('Esquema'), th('Entorno')],
                    ...ds.databases.map(db => [
                        td(db.system ?? '—'),
                        td(db.name),
                        { ...td(db.schema ?? '—'), color: db.schema ? C.dark : C.light },
                        envCell(db.environment),
                    ]),
                ],
            },
            layout: tableLayout,
            margin: [0, 0, 0, 4],
        });
    } else {
        blocks.push({ text: 'Sin bases de datos registradas.', fontSize: 7, color: C.light, italics: true, margin: [0, 0, 0, 4] });
    }

    // Separator between gestores
    blocks.push({ canvas: [{ type: 'line', x1: 0, y1: 0, x2: 515, y2: 0, lineWidth: 0.3, lineColor: C.borderSoft }], margin: [0, 4, 0, 0] });

    return blocks;
}

export function buildContent(d) {
    const totalDbs = d.database_servers?.reduce((sum, ds) => sum + (ds.databases?.length ?? 0), 0) ?? 0;

    const sections = [
        ...buildHeader(d),

        // ── Nombre + estado ──────────────────────────────────────────
        {
            columns: [
                {
                    stack: [
                        { text: d.name, fontSize: 16, bold: true, color: C.navy },
                        statusDot(d.is_active),
                    ],
                    width: '*',
                },
                ...(d.function ? [{
                    text: d.function.toUpperCase(),
                    fontSize: 7.5,
                    bold: true,
                    color: C.navy,
                    fillColor: C.navyLight,
                    margin: [8, 4, 8, 4],
                    alignment: 'center',
                    width: 'auto',
                }] : []),
            ],
            margin: [0, 0, 0, 12],
        },

        // ── Información general ──────────────────────────────────────
        sectionHeader('Información General'),
        {
            columns: [
                {
                    stack: [
                        kv('Sistema Operativo', d.os),
                        kv('Tipo de host', d.host_type),
                        ...(d.cloud ? [kv('Cloud', d.cloud)] : []),
                        ...(d.web_root ? [kv('Directorio web', d.web_root)] : []),
                        ...(d.ssh_user ? [kv('Usuario SSH', d.ssh_user)] : []),
                    ],
                    width: '50%',
                },
                {
                    stack: [
                        kv('CPU', d.cpu ? `${d.cpu} núcleos` : null),
                        kv('RAM', d.ram ? `${d.ram} GB` : null),
                        kv('Almacenamiento', d.storage ? `${d.storage} GB` : null),
                        kv('Sistemas alojados', String(d.systems?.length ?? 0)),
                        kv('Gestores de BD', String(d.database_servers?.length ?? 0)),
                        ...(d.containers > 0 ? [kv('Contenedores activos', String(d.containers))] : []),
                    ],
                    width: '50%',
                },
            ],
            columnGap: 20,
            margin: [0, 0, 0, 4],
        },

        // ── Servicios instalados ─────────────────────────────────────
        ...(d.installed_services?.length ? [
            sectionHeader('Servicios Instalados'),
            {
                columns: d.installed_services.map(svc => ({
                    stack: [pill(svc, true)],
                    margin: [0, 0, 6, 0],
                })),
                margin: [0, 0, 0, 4],
            },
        ] : []),

        // ── IPs ──────────────────────────────────────────────────────
        sectionHeader('Direcciones IP'),
        {
            columns: [
                {
                    stack: [
                        { text: 'Acceso al servidor (privadas)', fontSize: 7, bold: true, color: C.muted, margin: [0, 0, 0, 4] },
                        ...(d.private_ips?.length
                            ? d.private_ips.map(ip => {
                                const tag = [ip.interface, ip.is_primary ? 'principal' : null].filter(Boolean).join(' · ');
                                return {
                                    stack: [
                                        {
                                            columns: [
                                                { text: ip.ip, fontSize: ip.is_primary ? 8 : 7.5, bold: ip.is_primary, color: ip.is_primary ? C.navy : C.dark, width: '*' },
                                                ...(tag ? [{ text: tag, fontSize: 6, color: C.muted, width: 'auto', margin: [4, 1, 0, 0] }] : []),
                                            ],
                                        },
                                        ...(ip.notes ? [{ text: ip.notes, fontSize: 6, color: C.light, italics: true, margin: [0, 1, 0, 0] }] : []),
                                    ],
                                    margin: [0, 0, 0, 4],
                                };
                              })
                            : [{ text: '—', fontSize: 7, color: C.light }]
                        ),
                    ],
                    width: '50%',
                },
                {
                    stack: [
                        { text: 'Exposición pública', fontSize: 7, bold: true, color: C.muted, margin: [0, 0, 0, 4] },
                        ...(d.public_ips?.length
                            ? d.public_ips.map(ip => {
                                const tag = [ip.interface, ip.is_primary ? 'principal' : null].filter(Boolean).join(' · ');
                                return {
                                    columns: [
                                        { text: ip.ip, fontSize: 7.5, color: C.dark, width: '*' },
                                        ...(tag ? [{ text: tag, fontSize: 6, color: C.muted, width: 'auto', margin: [4, 1, 0, 0] }] : []),
                                    ],
                                    margin: [0, 0, 0, 4],
                                };
                              })
                            : [{ text: '—', fontSize: 7, color: C.light }]
                        ),
                        { text: 'El uso por sistema se detalla en Sistemas Alojados.', fontSize: 6, color: C.light, italics: true, margin: [0, 2, 0, 0] },
                    ],
                    width: '50%',
                },
            ],
            columnGap: 20,
            margin: [0, 0, 0, 4],
        },

        // ── Responsables del servidor ────────────────────────────────
        ...(d.responsibles?.length ? [
            sectionHeader('Responsables del Servidor'),
            {
                table: {
                    widths: ['*', 160, 50],
                    headerRows: 1,
                    body: [
                        [th('Nombre'), th('Rol / Nivel'), th('Estado')],
                        ...d.responsibles.map(r => [
                            td(r.name),
                            td(r.level),
                            td(r.active ? 'Activo' : 'Baja'),
                        ]),
                    ],
                },
                layout: tableLayout,
                margin: [0, 0, 0, 0],
            },
        ] : []),

        // ── Sistemas alojados ────────────────────────────────────────
        ...(d.systems?.length ? [
            sectionHeader(`Sistemas Alojados (${d.systems.length})`),
            {
                table: {
                    widths: ['*', 195, 115, 54],
                    headerRows: 1,
                    body: [
                        [th('Sistema'), th('Área'), th('IPs'), th('Estado')],
                        ...d.systems.map(s => {
                            const fs = 6.5;
                            const ipTag = (label, color, ip, port) => ({
                                columns: [
                                    { text: label, fontSize: 5.5, bold: true, color, width: 14, margin: [0, 1, 0, 0] },
                                    { text: ip + (port ? `:${port}` : ''), fontSize: fs, color: color === '#6b7280' ? '#374151' : '#1b3a5c', width: '*' },
                                ],
                                margin: [5, 1.5, 5, 1.5],
                            });
                            const ipStack = [
                                ...(s.private_ip ? [ipTag('INT', '#6b7280', s.private_ip, s.private_port)] : []),
                                ...(s.exposed?.map(e => ipTag('PUB', '#0f766e', e.ip, e.port)) ?? []),
                            ];
                            return [
                                {
                                    text: s.name,
                                    fontSize: fs, color: '#1f2937', margin: [5, 3, 5, 3],
                                    border: [false, false, false, true],
                                    borderColor: [null, null, null, '#d4d4d4'],
                                },
                                { ...td(s.area), fontSize: fs },
                                {
                                    stack: ipStack.length
                                        ? ipStack
                                        : [{ text: '—', fontSize: fs, color: '#9a9a9a', margin: [5, 3, 5, 3] }],
                                    border: [false, false, false, true],
                                    borderColor: [null, null, null, '#d4d4d4'],
                                },
                                { ...td(s.status), fontSize: fs },
                            ];
                        }),
                    ],
                },
                layout: tableLayout,
                margin: [0, 0, 0, 0],
            },
        ] : []),

        // ── Gestores de Bases de Datos ───────────────────────────────
        ...(d.database_servers?.length ? [
            sectionHeader(`Gestores de Bases de Datos (${d.database_servers.length} gestores · ${totalDbs} bases de datos)`),
            ...d.database_servers.flatMap(ds => buildDatabaseServerBlock(ds)),
        ] : []),

        // ── Notas ────────────────────────────────────────────────────
        ...(d.notes ? [
            sectionHeader('Notas'),
            { text: d.notes, fontSize: 7, color: C.medium, italics: true },
        ] : []),
    ];

    return sections;
}

export async function downloadServerPdf(serverId, btnEl) {
    const originalHTML = btnEl?.innerHTML ?? '';
    if (btnEl) {
        btnEl.disabled = true;
        btnEl.innerHTML = `<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>`;
    }
    try {
        const res  = await fetch(`/reports/pdf-data/servers/${serverId}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        const data = await res.json();
        const doc  = wrapDoc(buildContent(data), { orientation: 'portrait' });
        const slug = data.name?.toLowerCase().replace(/\s+/g, '_') ?? serverId;
        pdfMake.createPdf(doc).download(`servidor_${slug}_${new Date().toISOString().slice(0,10)}.pdf`);
    } catch (e) {
        console.error('PDF error:', e);
        if (window.sgToast) sgToast('error', 'No se pudo generar el PDF del servidor');
    } finally {
        if (btnEl) { btnEl.disabled = false; btnEl.innerHTML = originalHTML; }
    }
}
