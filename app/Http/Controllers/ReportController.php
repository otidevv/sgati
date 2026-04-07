<?php

namespace App\Http\Controllers;

use App\Models\Server;
use App\Models\System;
use App\Models\SystemDatabase;
use App\Models\Repository;
use App\Enums\SystemStatus;
use App\Exports\SystemsExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index()
    {
        // ── Sistemas ──────────────────────────────────────────────────
        $systems = System::with([
            'area', 'responsible',
            'infrastructure.server.ips',
            'databases',
            'repositories',
        ])->orderBy('name')->get();

        // Conteos por estado
        $byStatus = $systems->groupBy(fn($s) => $s->status->value);

        // Sistemas sin infraestructura/servidor asignado
        $withoutServer = $systems->filter(
            fn($s) => is_null($s->infrastructure?->server_id)
        );

        // Sistemas sin responsable
        $withoutResponsible = $systems->filter(fn($s) => is_null($s->responsible_id));

        // Sistemas sin repositorio
        $withoutRepo = $systems->filter(fn($s) => $s->repositories->isEmpty());

        // Sistemas con SSL próximo a vencer (< 60 días) o vencido
        $sslWarning = $systems->filter(function ($s) {
            $expiry = $s->infrastructure?->ssl_expiry;
            return $expiry && now()->diffInDays($expiry, false) < 60;
        })->sortBy(fn($s) => $s->infrastructure->ssl_expiry);

        // ── Servidores ────────────────────────────────────────────────
        $servers = Server::with([
            'ips',
            'systems.area',
            'databaseServers.databases',
            'activeContainers',
        ])->orderBy('name')->get();

        // Sistemas por servidor (top servidores)
        $systemsByServer = $servers
            ->filter(fn($srv) => $srv->systems->count() > 0)
            ->sortByDesc(fn($srv) => $srv->systems->count());

        // ── Bases de datos ────────────────────────────────────────────
        $dbsByEngine = SystemDatabase::selectRaw('engine, count(*) as total')
            ->groupBy('engine')
            ->orderByDesc('total')
            ->get();

        // ── Repositorios ──────────────────────────────────────────────
        $reposByProvider = Repository::selectRaw('provider, count(*) as total')
            ->groupBy('provider')
            ->orderByDesc('total')
            ->get();

        // ── IPs públicas con sistemas ─────────────────────────────────
        $publicIpSystems = $servers
            ->filter(fn($srv) => $srv->publicIps->count() > 0 && $srv->systems->count() > 0)
            ->sortBy('name');

        return view('reports.index', compact(
            'systems',
            'byStatus',
            'withoutServer',
            'withoutResponsible',
            'withoutRepo',
            'sslWarning',
            'servers',
            'systemsByServer',
            'dbsByEngine',
            'reposByProvider',
            'publicIpSystems',
        ));
    }

    public function exportExcel()
    {
        return Excel::download(new SystemsExport, 'sistemas_sgati_' . now()->format('Ymd') . '.xlsx');
    }

    public function exportPdf()
    {
        $systems = System::with(['area', 'responsible', 'infrastructure.server'])
            ->orderBy('name')
            ->get();

        $pdf = Pdf::loadView('pdf.systems-report', compact('systems'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('sistemas_sgati_' . now()->format('Ymd') . '.pdf');
    }
}
