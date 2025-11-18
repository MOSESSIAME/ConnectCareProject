<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Assignment;
use App\Models\User;
use Carbon\Carbon;

class TeamLeaderDashboardController extends Controller
{
    /**
     * Team leader dashboard.
     *
     * Summary cards:
     *  - active_reassigned: assignments for the team with status Active or Reassigned
     *  - unassigned_members: count of team members who do NOT have any non-completed assignment
     *  - completed: assignments for the team with status Completed
     *
     * Recent assignments shows latest 10 assignments for the team (including unassigned).
     */
    public function index()
    {
        $teamId = Auth::user()->team_id;

        // If the user does not belong to a team, show a fallback view
        if (!$teamId) {
            return view('dashboards.team', [
                'notAssigned' => true,
                'counts' => [
                    'active_reassigned' => 0,
                    'unassigned_members' => 0,
                    'completed' => 0,
                ],
                'myTeam' => collect(),
                'recentAssignments' => collect(),
            ]);
        }

        // Team members (assuming users table has team_id)
        $myTeam = User::where('team_id', $teamId)->get();
        $teamMemberIds = $myTeam->pluck('id');

        // Base: assignments that belong to this team (including unassigned rows)
        $base = Assignment::where('team_id', $teamId);

        // Counts required by the dashboard cards
        $activeReassignedCount = (int) $base->clone()->whereIn('status', ['Active', 'Reassigned'])->count();
        $completedCount = (int) $base->clone()->where('status', 'Completed')->count();

        // Determine which team members currently have a non-completed assignment
        $assignedMemberIds = Assignment::where('team_id', $teamId)
            ->whereNotNull('assigned_to')
            ->where('status', '!=', 'Completed')
            ->pluck('assigned_to')
            ->unique()
            ->filter(); // remove any nulls, just in case

        // Unassigned members = members on the team who are NOT in the list above
        $unassignedCount = (int) $teamMemberIds->diff($assignedMemberIds)->count();

        $counts = [
            'active_reassigned'   => $activeReassignedCount,
            'unassigned_members'  => $unassignedCount,
            'completed'           => $completedCount,
        ];

        // Recent assignments for the team (include unassigned entries)
        $recentAssignments = $base->latest()->take(10)->get();

        return view('dashboards.team', compact('counts', 'myTeam', 'recentAssignments') + ['notAssigned' => false]);
    }
}