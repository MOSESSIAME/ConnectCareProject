<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HomecellReport;
use App\Models\Member;
use App\Models\FollowUpHistory;
use Illuminate\Support\Facades\DB;

class PastorDashboardController extends Controller
{
    public function index()
    {
        // High-level churchwide overview (read-only)
        $totals = [
            'males'         => (int) HomecellReport::sum('males'),
            'females'       => (int) HomecellReport::sum('females'),
            'first_timers'  => (int) HomecellReport::sum('first_timers'),
            'new_converts'  => (int) HomecellReport::sum('new_converts'),
            'members_total' => (int) Member::count(),
        ];

        $attendanceByZone = HomecellReport::join('zones','homecell_reports.zone_id','=','zones.id')
            ->select('zones.name as zone_name', DB::raw('SUM(males + females) as total'))
            ->groupBy('zones.name')
            ->orderBy('zones.name')
            ->get();

        $recentReports = HomecellReport::with('homecell')->latest()->take(10)->get();

        $followupProgress = [
            'completed' => (int) FollowUpHistory::where('status','Completed')->count(),
            'pending'   => (int) FollowUpHistory::where('status','Pending')->count(),
        ];

        return view('dashboards.pastor', compact('totals','attendanceByZone','recentReports','followupProgress'));
    }
}
