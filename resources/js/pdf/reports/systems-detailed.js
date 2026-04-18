import { C } from '../theme.js';

// ── Layout con bordes completos ──────────────────────────────────────
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
// A4 landscape con márgenes [25,38,25,44] → ancho útil = 841.89 - 50 ≈ 791pt
// Suma de columnas: 108+62+47+50+65+72+*+44+50+20+22+105  (* = auto para URL)

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

// Celda de dos líneas: principal + secundaria más pequeña
const tdStack = (primary, secondary, opts = {}) => ({
    stack: [
        { text: primary ?? '—', fontSize: 6.5, bold: !!opts.bold, color: opts.color ?? C.dark },
        ...(secondary
            ? [{ text: secondary, fontSize: 5.5, color: C.muted, margin: [0, 0.5, 0, 0] }]
            : []),
    ],
});

// Celda SSL compacta
const tdSsl = (enabled, expiry) => {
    if (!enabled) return td('No', { color: C.light, italics: true, alignment: 'center', fontSize: 6 });
    return {
        stack: [
            { text: 'Sí', fontSize: 6.5, bold: true, color: '#16a34a', alignment: 'center' },
            ...(expiry
                ? [{ text: expiry, fontSize: 5.5, color: C.muted, alignment: 'center', margin: [0, 0.5, 0, 0] }]
                : []),
        ],
    };
};

// ── Cabecera compacta para landscape ────────────────────────────────
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
export function buildContent(d) {
    return [
        ...header('LISTA DE SISTEMAS — DETALLADO', d.generated_at, d.generated_by),
        {
            table: {
                widths: [108, 62, 47, 50, 65, 72, '*', 44, 50, 20, 22, 105],
                headerRows: 1,
                dontBreakRows: true,
                body: [
                    [
                        th('Sistema'),
                        th('Área'),
                        th('Estado'),
                        th('Ambiente'),
                        th('Servidor'),
                        th('IP : Puerto'),
                        th('URL'),
                        th('Web Server'),
                        th('SSL'),
                        th('BDs'),
                        th('Rep.'),
                        th('Responsable'),
                    ],
                    ...d.systems.map(s => [
                        tdStack(s.name, s.acronym, { bold: true }),
                        td(s.area, { fontSize: 6.5 }),
                        td(s.status, { alignment: 'center', fontSize: 6 }),
                        td(s.environment, { alignment: 'center', fontSize: 6 }),
                        td(s.server, { fontSize: 6.5 }),
                        td(s.ip_port, { fontSize: 6, color: C.muted }),
                        td(s.url, { fontSize: 5.5, color: C.muted }),
                        td(s.web_server, { fontSize: 6, alignment: 'center' }),
                        tdSsl(s.ssl_enabled, s.ssl_expiry),
                        td(s.db_count   || '—', { alignment: 'center', fontSize: 6.5 }),
                        td(s.repo_count || '—', { alignment: 'center', fontSize: 6.5 }),
                        tdStack(s.responsible, s.resp_role),
                    ]),
                ],
            },
            layout,
        },
        {
            text: `Total: ${d.systems.length} sistemas`,
            fontSize: 6.5,
            color: C.muted,
            margin: [0, 5, 0, 0],
            alignment: 'right',
        },
    ];
}
