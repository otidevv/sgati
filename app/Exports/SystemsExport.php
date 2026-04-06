<?php

namespace App\Exports;

use App\Models\System;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SystemsExport implements FromCollection, WithHeadings, WithStyles
{
    public function collection()
    {
        return System::with(['area', 'responsible', 'infrastructure'])
            ->get()
            ->map(fn($s) => [
                'Nombre'      => $s->name,
                'Sigla'       => $s->acronym,
                'Estado'      => $s->status->label(),
                'Area'        => $s->area?->name,
                'Responsable' => $s->responsible?->name,
                'Tecnologia'  => $s->tech_stack,
                'URL'         => $s->infrastructure?->system_url,
                'IP Servidor' => $s->infrastructure?->server_ip,
                'IP Publica'  => $s->infrastructure?->public_ip,
                'SSL Vence'   => $s->infrastructure?->ssl_expiry?->format('d/m/Y'),
                'Actualizado' => $s->updated_at->format('d/m/Y'),
            ]);
    }

    public function headings(): array
    {
        return [
            'Nombre', 'Sigla', 'Estado', 'Area', 'Responsable',
            'Tecnologia', 'URL', 'IP Servidor', 'IP Publica', 'SSL Vence', 'Actualizado',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [1 => ['font' => ['bold' => true]]];
    }
}
