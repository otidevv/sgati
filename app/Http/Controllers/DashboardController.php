<?php

namespace App\Http\Controllers;

use App\Models\System;
use App\Models\SystemInfrastructure;
use App\Enums\SystemStatus;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total'       => System::count(),
            'active'      => System::where('status', SystemStatus::Active)->count(),
            'development' => System::where('status', SystemStatus::Development)->count(),
            'maintenance' => System::where('status', SystemStatus::Maintenance)->count(),
            'inactive'    => System::where('status', SystemStatus::Inactive)->count(),
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
