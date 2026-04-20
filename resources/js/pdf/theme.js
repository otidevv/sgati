// Paleta de colores
export const C = {
    navy:       '#1b3a5c',
    navyLight:  '#e8f0f7',
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

export const th = (text, w) => ({
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

export const td = (text, opts = {}) => ({
    text: text ?? '—',
    fontSize: 7.5,
    color: C.dark,
    margin: [5, 3, 5, 3],
    border: [false, false, false, true],
    borderColor: [null, null, null, C.borderSoft],
    ...opts,
});

export const tableLayout = {
    hLineWidth: (i, node) => (i === 0 || i === node.table.body.length) ? 0 : 0.5,
    vLineWidth: () => 0,
    hLineColor: () => C.borderSoft,
    fillColor:  (row) => row === 0 ? null : row % 2 === 0 ? C.rowAlt : null,
    paddingLeft:   () => 0,
    paddingRight:  () => 0,
    paddingTop:    () => 0,
    paddingBottom: () => 0,
};

export const rule = (style = 'soft') => {
    if (style === 'thick') return { canvas: [{ type: 'line', x1: 0, y1: 0, x2: 515, y2: 0, lineWidth: 2,   lineColor: C.navy }] };
    if (style === 'navy')  return { canvas: [{ type: 'line', x1: 0, y1: 0, x2: 515, y2: 0, lineWidth: 0.6, lineColor: C.navy }] };
    return                        { canvas: [{ type: 'line', x1: 0, y1: 0, x2: 515, y2: 0, lineWidth: 0.4, lineColor: C.borderSoft }] };
};

export const sectionHeader = (title) => ({
    stack: [
        rule('navy'),
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

export const kv = (label, value, muted = false) => ({
    columns: [
        { text: label,        fontSize: 7, bold: true, color: C.muted, width: 105 },
        { text: value ?? '—', fontSize: 7,             color: muted ? C.light : C.dark },
    ],
    margin: [0, 2, 0, 2],
});

// KV with monospace-style value (IPs, ports, URLs, dates)
export const kvMono = (label, value, muted = false) => ({
    columns: [
        { text: label,        fontSize: 7, bold: true, color: C.muted, width: 105 },
        { text: value ?? '—', fontSize: 7,             color: muted ? C.light : C.medium },
    ],
    margin: [0, 2, 0, 2],
});

// Pill badge (for standalone use)
export const pill = (text, active = true) => ({
    table: {
        widths: ['auto'],
        body: [[{
            text: (text ?? '—').toUpperCase(),
            fontSize: 6.5,
            bold: true,
            color: active ? C.navy : C.light,
            fillColor: active ? C.navyLight : null,
            border: [true, true, true, true],
            borderColor: active
                ? [C.navy, C.navy, C.navy, C.navy]
                : [C.borderSoft, C.borderSoft, C.borderSoft, C.borderSoft],
            margin: [5, 2, 5, 2],
        }]],
    },
});

// KV row where the value is a pill badge
export const kvPill = (label, text, active = true) => ({
    columns: [
        { text: label, fontSize: 7, bold: true, color: C.muted, width: 105 },
        { stack: [pill(text, active)] },
    ],
    margin: [0, 2, 0, 2],
});

// Table cell containing a centered pill badge
export const pillCell = (text, active = true) => ({
    stack: [pill(text, active)],
    border: [false, false, false, true],
    borderColor: [null, null, null, C.borderSoft],
    margin: [3, 2, 3, 2],
    alignment: 'center',
});

export const badge = (text) => ({
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
