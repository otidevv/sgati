import { C } from '../theme.js';

// ── Layout con bordes completos (mismo que systems-detailed) ─────────
const layout = {
    hLineWidth: (i, node) => {
        if (i === 0 || i === node.table.body.length) return 0.8;
        if (i === 1) return 1;
        return 0.3;
    },
    vLineWidth: (i, node) => {
        if (i === 0 || i === node.table.widths.length) return 0.8;
        return 0.3;
    },
    hLineColor: (i) => i === 1 ? C.navy : C.borderSoft,
    vLineColor: () => C.borderSoft,
    fillColor:  (row) => row === 0 ? C.navyLight : row % 2 === 0 ? C.rowAlt : null,
    paddingLeft:   () => 3,
    paddingRight:  () => 3,
    paddingTop:    () => 2,
    paddingBottom: () => 2,
};

// ── Helpers de celda ─────────────────────────────────────────────────
const th = (text) => ({
    text,
    fontSize: 6.5,
    bold: true,
    color: C.navy,
    alignment: 'center',
    margin: [0, 2, 0, 2],
});

const td = (text, opts = {}) => ({
    text: text ?? '—',
    fontSize: 6.5,
    color: C.dark,
    ...opts,
});

// Celda de dos líneas
const tdStack = (primary, secondary, opts = {}) => ({
    stack: [
        { text: primary ?? '—', fontSize: 6.5, bold: !!opts.bold, color: opts.color ?? C.dark },
        ...(secondary
            ? [{ text: secondary, fontSize: 5.5, color: C.muted, margin: [0, 0.5, 0, 0] }]
            : []),
    ],
});

// Servidor + indicador activo/inactivo
const tdServer = (name, isActive) => ({
    stack: [
        { text: name ?? '—', fontSize: 6.5, bold: true, color: C.dark },
        {
            text: isActive ? '● Activo' : '○ Inactivo',
            fontSize: 5.5,
            color: isActive ? '#16a34a' : C.light,
            margin: [0, 0.5, 0, 0],
        },
    ],
});

// Hardware: CPU / RAM / Disco apilados
const tdHardware = (cpu, ram, storage) => {
    const lines = [
        cpu     ? `CPU  ${cpu}c`        : null,
        ram     ? `RAM  ${ram} GB`      : null,
        storage ? `HDD  ${storage} GB`  : null,
    ].filter(Boolean);

    if (!lines.length) return td('—', { alignment: 'center', color: C.light });

    return {
        stack: lines.map((l, i) => ({
            text: l,
            fontSize: i === 0 ? 6.5 : 5.5,
            color: i === 0 ? C.dark : C.muted,
            ...(i > 0 ? { margin: [0, 0.5, 0, 0] } : {}),
        })),
    };
};

// ── Cabecera compacta landscape ──────────────────────────────────────
function header(title, generatedAt, generatedBy) {
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
                        { text: title, fontSize: 11, bold: true, color: C.navy, alignment: 'right' },
                        {
                            text: `${generatedAt}  ·  ${generatedBy}`,
                            fontSize: 5.5,
                            color: C.muted,
                            alignment: 'right',
                            margin: [0, 1.5, 0, 0],
                        },
                    ],
                    width: 'auto',
                },
            ],
            margin: [0, 0, 0, 6],
        },
        {
            canvas: [{ type: 'line', x1: 0, y1: 0, x2: 791, y2: 0, lineWidth: 2, lineColor: C.navy }],
            margin: [0, 0, 0, 8],
        },
    ];
}

// ── Contenido principal ──────────────────────────────────────────────
// A4 landscape, márgenes [25,38,25,44] → ancho útil ≈ 791pt
// Columnas: Servidor | S.O. | Función | Tipo/Cloud | IPs Públicas | IPs Privadas | Hardware | Cont. | Sist. | Sistemas alojados
// Widths:   115      | 75   | 60      | 70         | 80           | 75           | 62       | 24    | 24    | *
// Fijo: 115+75+60+70+80+75+62+24+24 = 585 → '*' ≈ 206pt

export function buildContent(d) {
    return [
        ...header('LISTA DE SERVIDORES', d.generated_at, d.generated_by),
        {
            table: {
                widths: [115, 75, 60, 70, 80, 75, 62, 24, 24, '*'],
                headerRows: 1,
                dontBreakRows: true,
                body: [
                    [
                        th('Servidor'),
                        th('Sistema Operativo'),
                        th('Función'),
                        th('Tipo / Cloud'),
                        th('IPs Públicas'),
                        th('IPs Privadas'),
                        th('Hardware'),
                        th('Cont.'),
                        th('Sist.'),
                        th('Sistemas alojados'),
                    ],
                    ...d.servers.map(s => [
                        tdServer(s.name, s.is_active),
                        td(s.os, { fontSize: 6.5 }),
                        td(s.function, { fontSize: 6.5 }),
                        tdStack(s.host_type, s.cloud || null),
                        td(s.public_ips  || '—', { fontSize: 6, color: C.muted }),
                        td(s.private_ips || '—', { fontSize: 6, color: C.muted }),
                        tdHardware(s.cpu, s.ram, s.storage),
                        td(s.containers    || '—', { alignment: 'center', fontSize: 6.5 }),
                        td(s.systems_count || '—', { alignment: 'center', fontSize: 6.5 }),
                        td(s.systems || '—', { fontSize: 6, color: C.muted }),
                    ]),
                ],
            },
            layout,
        },
        {
            text: `Total: ${d.servers.length} servidores`,
            fontSize: 6.5,
            color: C.muted,
            margin: [0, 5, 0, 0],
            alignment: 'right',
        },
    ];
}
