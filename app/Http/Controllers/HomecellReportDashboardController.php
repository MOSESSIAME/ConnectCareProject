<?php

namespace App\Http\Controllers;

use App\Models\HomecellReport;
use App\Models\Zone;
use Illuminate\Support\Facades\Auth;

class HomecellReportDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Zonal Leader sees only their zone
        $query = HomecellReport::query();
        if ($user->role->name === 'Zonal Leader' && isset($user->zone_id)) {
            $query->where('zone_id', $user->zone_id);
        }

        // Totals
        $totals = [
            'males'        => $query->sum('males'),
            'females'      => $query->sum('females'),
            //'children'     => $query->sum('children'),
            'first_timers' => $query->sum('first_timers'),
            'new_converts' => $query->sum('new_converts'),
        ];

        // Data for charts
        $zonesData = Zone::withSum(['homecellReports' => function ($q) {
            $q->selectRaw('sum(males + females');
        }], 'males')->get();

        $attendanceByZone = HomecellReport::selectRaw('zones.name as zone_name, SUM(males + females) as total')
            ->join('zones', 'homecell_reports.zone_id', '=', 'zones.id')
            ->groupBy('zones.name')
            ->orderBy('zones.name')
            ->get();

        return view('reports.dashboard', compact('totals', 'attendanceByZone'));
    }
}
