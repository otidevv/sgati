<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Repository;
use App\Models\Server;
use App\Models\System;
use App\Models\SystemDatabase;
use App\Models\SystemInfrastructure;
use App\Models\SystemVersion;
use App\Enums\SystemStatus;

class DashboardController extends Controller
{
    public function index()
    {
        $counts = System::selectRaw("
            count(*) as total,
            count(*) filter (where status = ?) as active,
            count(*) filter (where status = ?) as development,
            count(*) filter (where status = ?) as maintenance,
            count(*) filter (where status = ?) as inactive
        ", [
            SystemStatus::Active->value,
            SystemStatus::Development->value,
            SystemStatus::Maintenance->value,
            SystemStatus::Inactive->value,
        ])->first();

        $stats = [
            'total'       => (int) $counts->total,
            'active'      => (int) $counts->active,
            'development' => (int) $counts->development,
            'maintenance' => (int) $counts->maintenance,
            'inactive'    => (int) $counts->inactive,
        ];

        $recentSystems = System::with('area')
            ->orderByDesc('updated_at')
            ->limit(8)
            ->get();

        // SSL ya vencido
        $sslExpired = SystemInfrastructure::with('system')
            ->where('ssl_enabled', true)
            ->where(function ($q) {
                $q->where(function ($q2) {
                    $q2->whereNull('ssl_custom_expiry')->whereDate('ssl_expiry', '<', now());
                })->orWhereDate('ssl_custom_expiry', '<', now());
            })
            ->orderBy('ssl_expiry')
            ->get();

        // SSL próximo a vencer (próximos 30 días, aún no vencido)
        $sslExpiring = SystemInfrastructure::with('system')
            ->where('ssl_enabled', true)
            ->where(function ($q) {
                $q->where(function ($q2) {
                    $q2->whereNull('ssl_custom_expiry')
                       ->whereDate('ssl_expiry', '>=', now())
                       ->whereDate('ssl_expiry', '<=', now()->addDays(30));
                })->orWhere(function ($q2) {
                    $q2->whereDate('ssl_custom_expiry', '>=', now())
                       ->whereDate('ssl_custom_expiry', '<=', now()->addDays(30));
                });
            })
            ->orderBy('ssl_expiry')
            ->get();

        // Infraestructura
        $serverStats = [
            'total'  => Server::count(),
            'active' => Server::where('is_active', true)->count(),
        ];

        $repoCount = Repository::where('is_active', true)->count();
        $dbCount   = SystemDatabase::count();

        // Distribución por área (solo áreas con sistemas)
        $areaStats = Area::withCount('systems')
            ->orderByDesc('systems_count')
            ->get()
            ->where('systems_count', '>', 0)
            ->take(8);

        // Últimos despliegues
        $recentVersions = SystemVersion::with('system')
            ->orderByDesc('release_date')
            ->orderByDesc('created_at')
            ->limit(6)
            ->get();

        // Sistemas activos sin infraestructura registrada
        $systemsWithoutInfra = System::where('status', SystemStatus::Active->value)
            ->whereDoesntHave('infrastructure')
            ->count();

        return view('dashboard.index', compact(
            'stats',
            'recentSystems',
            'sslExpired',
            'sslExpiring',
            'serverStats',
            'repoCount',
            'dbCount',
            'areaStats',
            'recentVersions',
            'systemsWithoutInfra'
        ));
    }
}
