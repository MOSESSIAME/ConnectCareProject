<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use App\Models\Assignment;
use App\Models\FollowUpHistory;
use App\Models\Member;

class TeamDashboardController extends Controller
{
    public function index()
    {
        $leader = Auth::user();
        $team   = optional($leader)->leadsTeam; // requires ->leadsTeam relation on User
        $teamId = optional($team)->id;

        // --- KPI: Open / In Progress / Completed ---
        $openCount = Assignment::when($teamId, fn($q) => $q->where('team_id', $teamId))
            ->where('status', 'Active')
            ->count();

        $inProgressCount = Assignment::when($teamId, fn($q) => $q->where('team_id', $teamId))
            ->where('status', 'Reassigned')
            ->count();

        $completedCount = Assignment::when($teamId, fn($q) => $q->where('team_id', $teamId))
            ->where('status', 'Completed')
            ->count();

        // --- KPI: Overdue (robust to different column names) ---
        $overdueBase = FollowUpHistory::where('status', 'Pending')
            ->when($teamId, function ($q) use ($teamId) {
                $q->whereHas('assignment', fn($aq) => $aq->where('team_id', $teamId));
            });

        if (Schema::hasColumn('follow_up_histories', 'next_follow_up_date')) {
            $overdueQuery = (clone $overdueBase)
                ->whereDate('next_follow_up_date', '<', now()->toDateString());
        } elseif (Schema::hasColumn('follow_up_histories', 'next_follow_up')) {
            $overdueQuery = (clone $overdueBase)
                ->whereDate('next_follow_up', '<', now()->toDateString());
        } elseif (Schema::hasColumn('follow_up_histories', 'next_followup_date')) {
            $overdueQuery = (clone $overdueBase)
                ->whereDate('next_followup_date', '<', now()->toDateString());
        } else {
            // Fallback: consider "Pending" follow-ups older than 7 days as overdue
            $overdueQuery = (clone $overdueBase)
                ->whereDate('created_at', '<', now()->subDays(7)->toDateString());
        }

        $overdueCount = $overdueQuery->count();

        // --- KPI: Standby pool size (eligible members not actively assigned) ---
        $standbyCount = Member::whereIn('type', ['First-timer', 'New Convert'])
            ->whereDoesntHave('assignments', function ($q) {
                $q->whereIn('status', ['Active', 'Reassigned']);
            })
            ->count();

        // --- Recent team assignments ---
        $recentAssignments = Assignment::with(['member', 'assignedTo', 'team'])
            ->when($teamId, fn($q) => $q->where('team_id', $teamId))
            ->latest()
            ->take(10)
            ->get();

        // --- Team roster (QUALIFY columns to avoid ambiguity) ---
        $teamMembers = $team
            ? $team->members()
                ->select('users.id', 'users.name')     // 👈 qualified select
                ->orderBy('users.name')               // 👈 qualified order
                ->get()
            : collect();

        $stats = [
            'open'        => $openCount,
            'in_progress' => $inProgressCount,
            'completed'   => $completedCount,
            'overdue'     => $overdueCount,
            'standby'     => $standbyCount,
        ];

        return view('dashboards.team', compact('team', 'teamMembers', 'recentAssignments', 'stats'));
    }
}
