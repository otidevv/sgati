<?php

namespace App\Exports;

use App\Models\System;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithProperties;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;

class SystemsExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, WithTitle, WithProperties
{
    private array $statusColors = [
        'Activo'        => ['bg' => 'D1FAE5', 'fg' => '065F46'],
        'En desarrollo' => ['bg' => 'DBEAFE', 'fg' => '1E40AF'],
        'Mantenimiento' => ['bg' => 'FEF3C7', 'fg' => '92400E'],
        'Inactivo'      => ['bg' => 'FEE2E2', 'fg' => '991B1B'],
    ];

    public function collection()
    {
        return System::with([
            'area',
            'infrastructure.server.ips',
            'databases',
            'repositories',
            'responsibles.persona',
        ])->orderBy('name')->get()->map(function ($s) {
            $infra      = $s->infrastructure;
            $server     = $infra?->server;
            $publicIp   = $infra?->public_ip ?? $server?->publicIps->first()?->ip_address;
            $privateIp  = $server?->privateIps->first()?->ip_address;
            $sslExpiry  = $infra?->ssl_expiry;
            $activeResp = $s->responsibles->where('is_active', true)->first();
            $respName   = $activeResp
                ? trim(($activeResp->persona->apellido_paterno ?? '') . ' ' . ($activeResp->persona->nombres ?? ''))
                : null;
            $respRole   = $activeResp
                ? \App\Models\SystemResponsible::levelLabel(
                    is_array($activeResp->level) ? ($activeResp->level[0] ?? '') : (string) $activeResp->level
                  )
                : null;

            return [
                $s->name,
                $s->acronym,
                $s->status->label(),
                $s->area?->name,
                $s->description,
                $infra?->environment?->label(),
                $server?->name,
                $publicIp,
                $privateIp,
                $infra?->port ? (string) $infra->port : null,
                $infra?->system_url,
                $infra?->web_server,
                $infra?->ssl_enabled ? 'Sí' : 'No',
                $sslExpiry?->format('d/m/Y'),
                $respName,
                $respRole,
                is_array($s->tech_stack) ? implode(', ', $s->tech_stack) : ($s->tech_stack ?? ''),
                $s->databases->count() ?: 0,
                $s->repositories->count() ?: 0,
                $s->observations,
                $s->created_at?->format('d/m/Y'),
                $s->updated_at?->format('d/m/Y'),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Nombre', 'Sigla', 'Estado', 'Área', 'Descripción',
            'Ambiente', 'Servidor', 'IP Pública', 'IP Privada', 'Puerto',
            'URL', 'Web Server', 'SSL', 'SSL Vence',
            'Responsable', 'Rol',
            'Stack Tecnológico', 'Bases de datos', 'Repositorios',
            'Observaciones', 'Creado', 'Actualizado',
        ];
    }

    public function title(): string
    {
        return 'Sistemas';
    }

    public function properties(): array
    {
        return [
            'creator'     => 'SGATI — OTI UNAMAD',
            'title'       => 'Inventario de Sistemas TI',
            'description' => 'Exportado desde SGATI',
            'company'     => 'Universidad Nacional Madre de Dios',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 32,  // Nombre
            'B' => 10,  // Sigla
            'C' => 14,  // Estado
            'D' => 22,  // Área
            'E' => 40,  // Descripción
            'F' => 14,  // Ambiente
            'G' => 20,  // Servidor
            'H' => 16,  // IP Pública
            'I' => 16,  // IP Privada
            'J' => 8,   // Puerto
            'K' => 35,  // URL
            'L' => 12,  // Web Server
            'M' => 6,   // SSL
            'N' => 12,  // SSL Vence
            'O' => 28,  // Responsable
            'P' => 18,  // Rol
            'Q' => 40,  // Stack Tecnológico
            'R' => 14,  // BDs
            'S' => 14,  // Repos
            'T' => 40,  // Observaciones
            'U' => 11,  // Creado
            'V' => 11,  // Actualizado
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow  = $sheet->getHighestRow();
        $lastCol  = 'V';
        $navyHex  = '1B3A5C';
        $borderColor = ['argb' => 'FFD4D4D4'];

        // ── Fila de encabezado ───────────────────────────────────────
        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 10],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => "FF{$navyHex}"]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF1B3A5C']]],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(22);

        // ── Filas de datos ───────────────────────────────────────────
        for ($row = 2; $row <= $lastRow; $row++) {
            $bg = $row % 2 === 0 ? 'FFF6F6F6' : 'FFFFFFFF';

            $sheet->getStyle("A{$row}:{$lastCol}{$row}")->applyFromArray([
                'font'      => ['size' => 9],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $bg]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => false],
                'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => $borderColor]],
            ]);
            $sheet->getRowDimension($row)->setRowHeight(16);

            // Color de celda por estado (columna C)
            $status = $sheet->getCell("C{$row}")->getValue();
            if (isset($this->statusColors[$status])) {
                $colors = $this->statusColors[$status];
                $sheet->getStyle("C{$row}")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['argb' => "FF{$colors['fg']}"]],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => "FF{$colors['bg']}"]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
            }

            // SSL "No" en gris
            $ssl = $sheet->getCell("M{$row}")->getValue();
            if ($ssl === 'No') {
                $sheet->getStyle("M{$row}")->getFont()->getColor()->setARGB('FF9A9A9A');
            }

            // Columnas numéricas centradas (BDs, Repos, Puerto)
            foreach (['J', 'R', 'S'] as $col) {
                $sheet->getStyle("{$col}{$row}")->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }
        }

        // ── Borde exterior del rango completo ────────────────────────
        $sheet->getStyle("A1:{$lastCol}{$lastRow}")->applyFromArray([
            'borders' => [
                'outline' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => "FF{$navyHex}"]],
            ],
        ]);

        // ── Anclar primera fila ──────────────────────────────────────
        $sheet->freezePane('A2');

        // ── Ajuste de texto en columnas largas ───────────────────────
        foreach (['E', 'K', 'Q', 'T'] as $col) {
            $sheet->getStyle("{$col}2:{$col}{$lastRow}")
                ->getAlignment()->setWrapText(true);
        }

        return [];
    }
}
