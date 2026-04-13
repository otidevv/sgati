<?php

namespace App\Http\Controllers;

use App\Models\System;
use App\Models\SystemInfrastructure;
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
            ->limit(5)
            ->get();

        $sslExpiring = SystemInfrastructure::with('system')
            ->where('ssl_enabled', true)
            ->whereNotNull('ssl_expiry')
            ->whereDate('ssl_expiry', '<=', now()->addDays(30))
            ->orderBy('ssl_expiry')
            ->get();

        return view('dashboard.index', compact('stats', 'recentSystems', 'sslExpiring'));
    }
}
