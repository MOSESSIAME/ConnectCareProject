<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Zone;
use App\Models\Homecell;
use App\Models\Member;
use App\Models\HomecellReport;

class ZonalDashboardController extends Controller
{
    /**
     * Display a dashboard view for Zonal Leaders.
     */
    public function index()
    {
        $user = Auth::user();

        // Ensure user has a zone assigned
        if (!$user->zone_id) {
            return view('dashboards.zone', [
                'zone' => null,
                'homecellCount' => 0,
                'memberCount' => 0,
                'recentReports' => collect(),
                'summary' => [
                    'first_timers' => 0,
                    'new_converts' => 0,
                    'attendance_total' => 0,
                ],
                'error' => 'No zone assigned to your account. Contact the administrator.',
            ]);
        }

        $zone = Zone::find($user->zone_id);

        // Fetch zone-specific statistics
        $homecellCount = Homecell::where('zone_id', $zone->id)->count();
        $memberCount = Member::whereHas('homecell', fn($q) => $q->where('zone_id', $zone->id))->count();

        // Get recent reports submitted for this zone
        $recentReports = HomecellReport::with('homecell')
            ->where('zone_id', $zone->id)
            ->latest()
            ->take(5)
            ->get();

        // Summary totals (from all reports in zone)
        $summary = [
            'first_timers' => HomecellReport::where('zone_id', $zone->id)->sum('first_timers'),
            'new_converts' => HomecellReport::where('zone_id', $zone->id)->sum('new_converts'),
            'attendance_total' => HomecellReport::where('zone_id', $zone->id)
                ->selectRaw('SUM(males + females) as total')
                ->value('total') ?? 0,
        ];

        return view('dashboards.zone', compact(
            'zone',
            'homecellCount',
            'memberCount',
            'recentReports',
            'summary'
        ));
    }
}
