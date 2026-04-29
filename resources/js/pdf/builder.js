import pdfMake from 'pdfmake/build/pdfmake';
import pdfFonts from 'pdfmake/build/vfs_fonts';
import { C, rule } from './theme.js';

if (pdfFonts?.vfs)          pdfMake.vfs = pdfFonts.vfs;
else if (pdfFonts?.pdfMake) pdfMake.vfs = pdfFonts.pdfMake.vfs;

export { pdfMake };

// Cabecera institucional para reportes generales
export function reportHeader(title, generatedAt, generatedBy) {
    return [
        {
            text: 'UNIVERSIDAD NACIONAL MADRE DE DIOS',
            fontSize: 10,
            bold: true,
            color: C.navy,
            alignment: 'center',
            margin: [0, 0, 0, 2],
        },
        {
            text: 'Oficina de Tecnologías de la Información — OTI',
            fontSize: 7,
            color: C.muted,
            alignment: 'center',
            margin: [0, 0, 0, 8],
        },
        rule('thick'),
        {
            text: title,
            fontSize: 13,
            bold: true,
            color: C.navy,
            alignment: 'center',
            margin: [0, 8, 0, 4],
        },
        {
            columns: [
                { text: `Generado el: ${generatedAt}`, fontSize: 6.5, color: C.muted },
                { text: `Por: ${generatedBy}`,         fontSize: 6.5, color: C.muted, alignment: 'right' },
            ],
            margin: [0, 0, 0, 12],
        },
        rule('navy'),
    ];
}

// Pie de página estándar para reportes
export const reportFooter = (cp, pc, fm = 40) => ({
    columns: [
        { text: `${window._appConfig?.name ?? 'OTI'} — OTI UNAMAD`, fontSize: 6, color: C.light, italics: true, margin: [fm, 0, 0, 0] },
        { text: `Página ${cp} de ${pc}`, fontSize: 7, color: C.navy, bold: true, alignment: 'right', margin: [0, 0, fm, 0] },
    ],
    margin: [0, 10, 0, 0],
});

// Envuelve el content[] en un docDefinition completo
// opts: size, orientation, margins [l,t,r,b], footerMargin
export function wrapDoc(content, opts = {}) {
    const margins = opts.margins ?? [40, 40, 40, 50];
    const fm      = opts.footerMargin ?? margins[0];
    return {
        pageSize:        opts.size        ?? 'A4',
        pageOrientation: opts.orientation ?? 'portrait',
        pageMargins:     margins,
        content,
        footer: (cp, pc) => reportFooter(cp, pc, fm),
        defaultStyle: { font: 'Roboto', fontSize: 8, color: C.dark },
    };
}
