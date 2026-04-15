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

    public function systemPdf(System $system)
    {
        $system->load([
            'area',
            'responsible',
            'infrastructure.server.ips',
            'infrastructure.sslCertificate',
            'versions',
            'databases',
            'services.fields',
            'repositories',
            'integrationsFrom.targetSystem',
            'integrationsTo.sourceSystem',
            'documents',
            'responsibles.persona',
            'statusLogs',
        ]);

        $pdf = Pdf::loadView('pdf.system-detail', compact('system'))
            ->setPaper('a4', 'portrait');

        $filename = 'sistema_' . ($system->acronym ?? $system->id) . '_' . now()->format('Ymd') . '.pdf';

        return $pdf->download($filename);
    }

    public function systemPdfData(System $system)
    {
        $system->load([
            'area',
            'responsible',
            'infrastructure.server.ips',
            'infrastructure.sslCertificate',
            'versions',
            'databases',
            'services',
            'repositories',
            'integrationsFrom.targetSystem',
            'integrationsTo.sourceSystem',
            'responsibles.persona',
            'statusLogs',
        ]);

        $infra  = $system->infrastructure;
        $server = $infra?->server;

        $now = now();

        return response()->json([
            'id'           => $system->id,
            'name'         => $system->name,
            'acronym'      => $system->acronym,
            'description'  => $system->description,
            'status'       => $system->status->label(),
            'area'         => $system->area?->name,
            'responsible'  => $system->responsible?->name,
            'tech_stack'   => $system->tech_stack ?? [],
            'observations' => $system->observations,
            'generated_by' => auth()->user()?->name ?? 'Sistema',
            'generated_at' => $now->format('d/m/Y H:i:s'),
            'created_at'  => $system->created_at?->format('d/m/Y'),
            'updated_at'  => $system->updated_at?->format('d/m/Y'),

            'infrastructure' => $infra ? [
                'system_url'  => $infra->system_url,
                'web_server'  => $infra->web_server,
                'environment' => $infra->environment,
                'ssl_enabled' => $infra->ssl_enabled,
                'server_name' => $server?->name,
                'server_ip'   => $server?->publicIps->first()?->ip_address,
                'ssl_cert'    => $infra->sslCertificate?->name,
                'notes'       => $infra->notes,
            ] : null,

            'versions' => $system->versions->map(fn($v) => [
                'version'      => $v->version,
                'release_date' => $v->release_date?->format('d/m/Y'),
                'notes'        => $v->notes,
            ]),

            'databases' => $system->databases->map(fn($d) => [
                'name'   => $d->name,
                'engine' => $d->engine,
                'host'   => $d->host,
                'port'   => $d->port,
            ]),

            'services' => $system->services->map(fn($s) => [
                'name'      => $s->service_name,
                'type'      => $s->service_type,
                'direction' => $s->direction,
                'endpoint'  => $s->endpoint_url,
                'auth'      => $s->auth_type,
                'active'    => $s->is_active,
            ]),

            'repositories' => $system->repositories->map(fn($r) => [
                'name'     => $r->name,
                'provider' => $r->provider,
                'url'      => $r->repo_url,
                'branch'   => $r->default_branch,
            ]),

            'integrations_from' => $system->integrationsFrom->map(fn($i) => [
                'target'   => $i->targetSystem?->name,
                'protocol' => $i->protocol ?? null,
                'notes'    => $i->notes ?? null,
            ]),

            'integrations_to' => $system->integrationsTo->map(fn($i) => [
                'source'   => $i->sourceSystem?->name,
                'protocol' => $i->protocol ?? null,
                'notes'    => $i->notes ?? null,
            ]),

            'responsibles' => $system->responsibles->map(fn($r) => [
                'name'        => $r->persona
                    ? trim(($r->persona->nombres ?? '') . ' ' . ($r->persona->apellido_paterno ?? '') . ' ' . ($r->persona->apellido_materno ?? ''))
                    : '—',
                'level'       => \App\Models\SystemResponsible::levelLabel($r->level),
                'assigned_at' => $r->assigned_at?->format('d/m/Y'),
                'active'      => $r->is_active,
            ]),

            'status_logs' => $system->statusLogs->take(10)->map(fn($l) => [
                'status' => $l->new_status ?? '—',
                'date'   => $l->created_at?->format('d/m/Y'),
                'notes'  => $l->reason ?? null,
            ]),
        ]);
    }
}
