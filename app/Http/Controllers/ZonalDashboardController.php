<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Zone;
use App\Models\Member;
use App\Models\HomecellReport;

class ZonalDashboardController extends Controller
{
    /**
     * Zonal Leader dashboard.
     */
    public function index()
    {
        $user = Auth::user();

        // 1) Find the zone this user leads
        //    (requires zones.leader_id to be set to this user's id)
        $zone = Zone::with('homecells')
            ->where('leader_id', $user->id)
            ->first();

        if (!$zone) {
            // No zone attached to this leader
            return view('dashboards.zone', [
                'zone'          => null,
                'homecellCount' => 0,
                'memberCount'   => 0,
                'recentReports' => collect(),
                'summary'       => [
                    'first_timers'    => 0,
                    'new_converts'    => 0,
                    'attendance_total'=> 0,
                ],
                'error' => 'No zone assigned to your account. Contact the administrator.',
            ]);
        }

        // 2) IDs of homecells in this zone
        $homecellIds   = $zone->homecells->pluck('id');
        $homecellCount = $zone->homecells->count();

        // 3) Members under those homecells
        //    (If your schema stores zone_id directly on members, switch to ->where('zone_id', $zone->id))
        $memberCount = Member::whereIn('homecell_id', $homecellIds)->count();

        // 4) Recent reports for homecells in this zone
        $recentReports = HomecellReport::with('homecell')
            ->whereIn('homecell_id', $homecellIds)
            ->latest()
            ->take(10)
            ->get();

        // 5) Summary totals across all reports in these homecells
        $totals = HomecellReport::whereIn('homecell_id', $homecellIds)
            ->selectRaw('
                COALESCE(SUM(first_timers), 0)         AS ft,
                COALESCE(SUM(new_converts), 0)         AS nc,
                COALESCE(SUM(males + females), 0)      AS att
            ')
            ->first();

        $summary = [
            'first_timers'     => (int) ($totals->ft  ?? 0),
            'new_converts'     => (int) ($totals->nc  ?? 0),
            'attendance_total' => (int) ($totals->att ?? 0),
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
