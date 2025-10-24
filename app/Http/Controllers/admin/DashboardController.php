<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomecellReport;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // ✅ Calculate total attendance metrics
        $totals = [
            'males' => HomecellReport::sum('males'),
            'females' => HomecellReport::sum('females'),
            'first_timers' => HomecellReport::sum('first_timers'),
            'new_converts' => HomecellReport::sum('new_converts'),
        ];

        // ✅ Attendance grouped by zone (now without children)
        $attendanceByZone = HomecellReport::join('zones', 'homecell_reports.zone_id', '=', 'zones.id')
            ->select('zones.name as zone_name', DB::raw('SUM(males + females) as total'))
            ->groupBy('zones.name')
            ->get();

        // ✅ Return view with computed data
        return view('dashboards.admin', compact('totals', 'attendanceByZone'));
    }
}
