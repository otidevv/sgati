import { C, th, td, tableLayout } from '../theme.js';
import { reportHeader } from '../builder.js';

export function buildContent(d) {
    return [
        ...reportHeader('LISTA DE SISTEMAS', d.generated_at, d.generated_by),
        {
            table: {
                widths: ['*', 50, 110, 100, 60],
                headerRows: 1,
                body: [
                    [th('Sistema'), th('Sigla'), th('Área'), th('Responsable'), th('Estado')],
                    ...d.systems.map(s => [
                        td(s.name, { bold: true }),
                        td(s.acronym, { alignment: 'center', fontSize: 7, color: C.muted }),
                        td(s.area),
                        td(s.responsible),
                        td(s.status, { alignment: 'center' }),
                    ]),
                ],
            },
            layout: tableLayout,
        },
        { text: `Total: ${d.systems.length} sistemas`, fontSize: 7, color: C.muted, margin: [0, 6, 0, 0], alignment: 'right' },
    ];
}
