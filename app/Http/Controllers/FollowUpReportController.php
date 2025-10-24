<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\FollowUpHistory;
use App\Models\Member;
use App\Models\Team;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FollowUpReportController extends Controller
{
    /**
     * Follow-up Reports & Analytics (role-scoped)
     */
    public function index()
    {
        $user = Auth::user();
        $role = $user->role->name ?? '';

        // -------- Base query (scoped by role) ----------
        $base = Assignment::query();

        if ($role === 'Team Member') {
            // Only my assignments
            $base->where('assigned_to', $user->id);
        } elseif ($role === 'Team Leader') {
            // Prefer using assignment.team_id (added recently)
            // Resolve the team the leader leads OR fallback to user's team_id
            $leaderTeamId = optional(Team::where('leader_id', $user->id)->first())->id;
            $teamId = $leaderTeamId ?? $user->team_id;

            if ($teamId) {
                $base->where('team_id', $teamId);
            } else {
                // No team found => no data
                $base->whereRaw('1=0');
            }
        }
        // Admin / Pastor / Staff => see all (no extra filter)

        // -------- KPI counters ----------
        $totalAssignments        = (clone $base)->count();
        $activeAssignments       = (clone $base)->where('status', 'Active')->count();
        $reassignedAssignments   = (clone $base)->where('status', 'Reassigned')->count();
        $completedAssignments    = (clone $base)->where('status', 'Completed')->count();
        $pendingAssignments      = $activeAssignments + $reassignedAssignments;

        $successRate = $totalAssignments > 0
            ? round(($completedAssignments / $totalAssignments) * 100)
            : 0;

        // -------- Conversion among members in scope ----------
        $memberIds = (clone $base)->pluck('member_id')->filter()->unique();
        $converted = Member::whereIn('id', $memberIds)
            ->where('foundation_class_completed', true)
            ->count();

        $conversionRate = $memberIds->count() > 0
            ? round(($converted / $memberIds->count()) * 100)
            : 0;

        // -------- Method usage (only follow-ups for scoped assignments) ----------
        $assignmentIds = (clone $base)->pluck('id');
        $methodStats = FollowUpHistory::whereIn('assignment_id', $assignmentIds)
            ->select('method', DB::raw('COUNT(*) as count'))
            ->groupBy('method')
            ->orderByDesc('count')
            ->get();

        // -------- Team performance (completed per assignee) ----------
        $teamPerformance = (clone $base)
            ->where('status', 'Completed')
            ->join('users', 'assignments.assigned_to', '=', 'users.id')
            ->select('users.name as team_member', DB::raw('COUNT(assignments.id) as completed'))
            ->groupBy('users.name')
            ->orderByDesc('completed')
            ->get();

        // IMPORTANT: keep this view path if your file is resources/views/reports/followup/dashboard.blade.php
        return view('reports.followup.dashboard', compact(
            'totalAssignments',
            'activeAssignments',
            'reassignedAssignments',
            'completedAssignments',
            'pendingAssignments',
            'successRate',
            'conversionRate',
            'methodStats',
            'teamPerformance'
        ));
    }
}
