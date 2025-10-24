<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\HomecellReport;
use App\Models\Homecell;
use App\Models\Member;

class HomecellLeaderDashboardController extends Controller
{
    public function index()
    {
        // 🔹 Determine which homecell this leader belongs to or leads
        $homecellId = Auth::user()->homecell_id
            ?? Homecell::where('leader_id', Auth::id())->value('id');

        // 🔸 If not assigned to any homecell, show info message (avoids error)
        if (!$homecellId) {
            return view('dashboards.homecell', [
                'notAssigned' => true,
                'totals' => [
                    'males' => 0,
                    'females' => 0,
                    'first_timers' => 0,
                    'new_converts' => 0,
                ],
                'recentReports' => collect(),
                'members' => collect(),
            ]);
        }

        // 🔹 Summaries (Totals)
        $totals = [
            'males'        => (int) HomecellReport::where('homecell_id', $homecellId)->sum('males'),
            'females'      => (int) HomecellReport::where('homecell_id', $homecellId)->sum('females'),
            'first_timers' => (int) HomecellReport::where('homecell_id', $homecellId)->sum('first_timers'),
            'new_converts' => (int) HomecellReport::where('homecell_id', $homecellId)->sum('new_converts'),
        ];

        // 🔹 Recent Homecell Reports
        $recentReports = HomecellReport::where('homecell_id', $homecellId)
            ->latest()
            ->take(10)
            ->get();

        // 🔹 Homecell Members
        $members = Member::where('homecell_id', $homecellId)
            ->latest()
            ->take(10)
            ->get();

        // ✅ Return the dashboard view
        return view('dashboards.homecell', [
            'notAssigned'   => false,
            'totals'        => $totals,
            'recentReports' => $recentReports,
            'members'       => $members,
        ]);
    }
}
