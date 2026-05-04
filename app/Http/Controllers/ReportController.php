<?php

namespace App\Http\Controllers;

use App\Models\Server;
use App\Models\System;
use App\Models\SystemDatabase;
use App\Models\Repository;
use Illuminate\Http\JsonResponse;
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

    public function systemsListPdfData(): JsonResponse
    {
        $systems = System::with(['area', 'responsible'])
            ->orderBy('name')
            ->get();

        return response()->json([
            'generated_at' => now()->format('d/m/Y H:i:s'),
            'generated_by' => auth()->user()?->name ?? 'Sistema',
            'systems' => $systems->map(fn($s) => [
                'name'        => $s->name,
                'acronym'     => $s->acronym,
                'area'        => $s->area?->name,
                'responsible' => $s->responsible?->name,
                'status'      => $s->status->label(),
            ]),
        ]);
    }

    public function systemsDetailedPdfData(): JsonResponse
    {
        $systems = System::with([
            'area',
            'infrastructure.server.ips',
            'databases',
            'repositories',
            'responsibles.persona',
        ])->orderBy('name')->get();

        return response()->json([
            'generated_at' => now()->format('d/m/Y H:i:s'),
            'generated_by' => auth()->user()?->name ?? 'Sistema',
            'systems' => $systems->map(function ($s) {
                $infra      = $s->infrastructure;
                $server     = $infra?->server;
                $publicIp   = $infra?->public_ip ?? $server?->publicIps->first()?->ip_address;
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
                $ipPort = $publicIp && $infra?->port
                    ? "{$publicIp}:{$infra->port}"
                    : ($publicIp ?? ($infra?->port ? ":{$infra->port}" : null));

                return [
                    'name'        => $s->name,
                    'acronym'     => $s->acronym,
                    'area'        => $s->area?->name,
                    'status'      => $s->status->label(),
                    'environment' => $infra?->environment?->label() ?? null,
                    'server'      => $server?->name,
                    'ip_port'     => $ipPort,
                    'url'         => $infra?->system_url,
                    'web_server'  => $infra?->web_server,
                    'ssl_enabled' => $infra?->ssl_enabled ?? false,
                    'ssl_expiry'  => $sslExpiry?->format('d/m/Y'),
                    'db_count'    => $s->databases->count(),
                    'repo_count'  => $s->repositories->count(),
                    'responsible' => $respName,
                    'resp_role'   => $respRole,
                ];
            }),
        ]);
    }

    public function serverPdfData(Server $server): JsonResponse
    {
        $server->load([
            'ips',
            'deployments.system.area',
            'deployments.system.responsible',
            'deployments.exposedIps',
            'deployments.serverIp',
            'databaseServers.databases.system',
            'databaseServers.responsibles.persona',
            'activeContainers',
            'responsibles.persona',
        ]);

        return response()->json([
            'generated_at' => now()->format('d/m/Y H:i:s'),
            'generated_by' => auth()->user()?->name ?? 'Sistema',
            'name'         => $server->name,
            'is_active'    => $server->is_active,
            'os'           => $server->operating_system,
            'function'     => $server->function?->label(),
            'host_type'    => $server->host_type,
            'cloud'        => collect([$server->cloud_provider, $server->cloud_region, $server->cloud_instance])->filter()->join(' / '),
            'cpu'          => $server->cpu_cores,
            'ram'          => $server->ram_gb,
            'storage'      => $server->storage_gb,
            'web_root'     => $server->web_root,
            'ssh_user'     => $server->ssh_user,
            'public_ips'   => $server->publicIps->map(fn($ip) => [
                'ip'         => $ip->ip_address,
                'interface'  => $ip->interface,
                'is_primary' => $ip->is_primary,
                'notes'      => $ip->notes,
                'systems'    => $server->deployments
                    ->filter(fn($infra) => $infra->exposedIps->contains('id', $ip->id))
                    ->map(fn($infra) => [
                        'name'    => $infra->system?->name,
                        'acronym' => $infra->system?->acronym,
                        'url'     => $infra->system_url,
                        'port'    => $infra->exposedIps->find($ip->id)?->pivot?->port,
                    ])
                    ->filter(fn($s) => $s['name'])
                    ->values(),
            ])->values(),
            'private_ips'  => $server->privateIps->map(fn($ip) => [
                'ip'         => $ip->ip_address,
                'interface'  => $ip->interface,
                'is_primary' => $ip->is_primary,
                'notes'      => $ip->notes,
            ])->values(),
            'notes'        => $server->notes,
            'installed_services' => $server->installed_services ?? [],
            'systems' => $server->deployments->map(fn($infra) => [
                'name'        => $infra->system?->name,
                'acronym'     => $infra->system?->acronym,
                'area'        => $infra->system?->area?->name,
                'responsible' => $infra->system?->responsible?->name,
                'status'      => $infra->system?->status?->label(),
                'private_ip'   => $infra->serverIp?->ip_address ?? $infra->public_ip,
                'private_port' => $infra->port,
                'exposed'      => $infra->exposedIps->map(fn($ip) => [
                    'ip'   => $ip->ip_address,
                    'port' => $ip->pivot->port,
                ])->values(),
            ])->filter(fn($s) => $s['name'])->values(),
            'database_servers' => $server->databaseServers->map(fn($ds) => [
                'name'         => $ds->name,
                'engine_label' => $ds->engine_label,
                'host'         => $ds->connection_string,
                'is_active'    => $ds->is_active,
                'notes'        => $ds->notes,
                'responsibles' => $ds->responsibles->map(fn($r) => [
                    'name'   => $r->persona
                        ? trim(($r->persona->nombres ?? '') . ' ' . ($r->persona->apellido_paterno ?? '') . ' ' . ($r->persona->apellido_materno ?? ''))
                        : '—',
                    'level'  => \App\Models\DatabaseServerResponsible::levelLabel((string) $r->level),
                    'active' => $r->is_active,
                ])->values(),
                'databases' => $ds->databases->map(fn($d) => [
                    'name'        => $d->db_name,
                    'schema'      => $d->schema_name,
                    'environment' => $d->environment?->label(),
                    'system'      => $d->system?->name,
                    'system_acronym' => $d->system?->acronym,
                ])->values(),
            ])->values(),
            'containers' => $server->activeContainers->count(),
            'responsibles' => $server->responsibles->map(fn($r) => [
                'name'  => $r->persona
                    ? trim(($r->persona->nombres ?? '') . ' ' . ($r->persona->apellido_paterno ?? '') . ' ' . ($r->persona->apellido_materno ?? ''))
                    : '—',
                'level' => \App\Models\DatabaseServerResponsible::levelLabel((string) $r->level),
                'active'=> $r->is_active,
            ]),
        ]);
    }

    public function serversPdfData(): JsonResponse
    {
        $servers = Server::with(['ips', 'systems', 'activeContainers'])
            ->orderBy('name')
            ->get();

        return response()->json([
            'generated_at' => now()->format('d/m/Y H:i:s'),
            'generated_by' => auth()->user()?->name ?? 'Sistema',
            'servers' => $servers->map(fn($srv) => [
                'name'          => $srv->name,
                'is_active'     => $srv->is_active,
                'os'            => $srv->operating_system,
                'function'      => $srv->function?->label(),
                'host_type'     => $srv->host_type,
                'cloud'         => collect([$srv->cloud_provider, $srv->cloud_region])->filter()->join(' / '),
                'public_ips'    => $srv->publicIps->pluck('ip_address')->join("\n"),
                'private_ips'   => $srv->privateIps->pluck('ip_address')->join("\n"),
                'cpu'           => $srv->cpu_cores,
                'ram'           => $srv->ram_gb,
                'storage'       => $srv->storage_gb,
                'containers'    => $srv->activeContainers->count(),
                'systems_count' => $srv->systems->count(),
                'systems'       => $srv->systems->map(fn($s) => $s->acronym ?: $s->name)->join(', '),
            ]),
        ]);
    }

    public function systemPdfData(System $system)
    {
        $system->load([
            'area',
            'responsible',
            'infrastructure.server.ips',
            'infrastructure.sslCertificate',
            'versions',
            'databases.databaseServer',
            'services.gatewayKeys.requestingSystem',
            'services.gatewayKeys.persona',
            'repositories',
            'integrationsFrom.targetSystem',
            'integrationsTo.sourceSystem',
            'responsibles.persona',
            'responsibles.documents',
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
                'system_url'       => $infra->system_url,
                'port'             => $infra->port,
                'web_server'       => $infra->web_server,
                'environment'      => $infra->environment?->label(),
                'ssl_enabled'      => $infra->ssl_enabled,
                'server_name'      => $server?->name,
                'operating_system' => $server?->operating_system,
                'server_ip'        => $infra->public_ip ?? $server?->ips->where('type', 'public')->first()?->ip_address,
                'internal_ip'      => $server?->ips->where('type', 'private')->first()?->ip_address,
                'ssl_cert'         => $infra->sslCertificate?->name,
                'notes'            => $infra->notes,
            ] : null,

            'versions' => $system->versions->map(fn($v) => [
                'version'      => $v->version,
                'release_date' => $v->release_date?->format('d/m/Y'),
                'notes'        => $v->notes,
            ]),

            'databases' => $system->databases->map(fn($d) => [
                'name'        => $d->db_name,
                'engine'      => $d->databaseServer?->engine_label ?? strtoupper($d->engine),
                'environment' => $d->environment?->label(),
                'host'        => $d->databaseServer?->connection_string,
                'schema'      => $d->schema_name,
                'notes'       => $d->notes,
            ]),

            'services' => $system->services->map(fn($s) => [
                'name'        => $s->service_name,
                'type'        => strtoupper(str_replace('_', ' ', $s->service_type)),
                'direction'   => $s->direction === 'consumed' ? 'Consumido' : 'Expuesto',
                'endpoint'    => $s->endpoint_url,
                'auth'        => $s->auth_type,
                'environment' => match($s->environment) {
                    'production'  => 'Producción',
                    'staging'     => 'Staging',
                    'development' => 'Desarrollo',
                    default       => $s->environment,
                },
                'version'     => $s->version,
                'description' => $s->description,
                'active'      => $s->is_active,
                'consumers'   => $s->direction === 'exposed'
                    ? $s->gatewayKeys->map(fn($k) => [
                        'name'        => $k->name,
                        'type'        => match($k->consumer_type ?? 'external') {
                            'internal' => 'Sistema',
                            'person'   => 'Persona',
                            default    => 'Ext.',
                        },
                        'organization'=> $k->requestingSystem?->acronym
                            ?? $k->requestingSystem?->name
                            ?? ($k->persona ? trim(($k->persona->apellido_paterno ?? '') . ' ' . ($k->persona->nombres ?? $k->persona->name ?? '')) : null)
                            ?? $k->consumer_organization,
                        'auth'        => match($k->auth_type ?? 'bearer') {
                            'bearer'      => 'Bearer',
                            'api_key'     => 'API Key',
                            'query_param' => 'Query',
                            'none'        => 'Sin auth',
                            default       => $k->auth_type,
                        },
                        'gateway_url' => $k->gateway_slug ? $k->gatewayUrl() : null,
                        'active'      => $k->is_active && !$k->isExpired(),
                        'total'       => $k->total_requests,
                        'last_used'   => $k->last_used_at?->format('d/m/Y'),
                    ])->values()
                    : [],
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

            'responsibles' => $system->responsibles->map(function ($r) {
                $docLabels = [
                    'resolucion_directoral' => 'R.D.',
                    'resolucion_jefatural'  => 'R.J.',
                    'memorando'             => 'Memo.',
                    'oficio'                => 'Oficio',
                    'contrato'              => 'Contrato',
                    'acta'                  => 'Acta',
                    'otro'                  => 'Doc.',
                ];
                $docs = $r->documents->map(function ($doc) use ($docLabels) {
                    $metaParts = array_filter([
                        $docLabels[$doc->document_type] ?? $doc->document_type,
                        $doc->document_number,
                        $doc->document_date?->format('d/m/Y'),
                    ]);
                    if ($metaParts) {
                        return implode(' · ', $metaParts);
                    }
                    return $doc->description ?: $doc->original_name ?: null;
                })->filter()->values()->toArray();

                return [
                    'name'        => $r->persona
                        ? trim(($r->persona->nombres ?? '') . ' ' . ($r->persona->apellido_paterno ?? '') . ' ' . ($r->persona->apellido_materno ?? ''))
                        : '—',
                    'level'       => is_array($r->level)
                        ? implode(' · ', array_map(fn($l) => \App\Models\SystemResponsible::levelLabel($l), $r->level))
                        : \App\Models\SystemResponsible::levelLabel((string) $r->level),
                    'assigned_at' => $r->assigned_at?->format('d/m/Y'),
                    'active'      => $r->is_active,
                    'documents'   => $docs,
                ];
            }),

            'status_logs' => $system->statusLogs->take(10)->map(fn($l) => [
                'status' => $l->new_status ?? '—',
                'date'   => $l->created_at?->format('d/m/Y'),
                'notes'  => $l->reason ?? null,
            ]),
        ]);
    }
}
