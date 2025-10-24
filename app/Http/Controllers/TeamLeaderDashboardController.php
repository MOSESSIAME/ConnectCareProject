<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Assignment;
use App\Models\User;
use Carbon\Carbon;

class TeamLeaderDashboardController extends Controller
{
    public function index()
    {
        $teamId = Auth::user()->team_id;

        if (!$teamId) {
            return view('dashboards.team', [
                'notAssigned' => true,
                'counts' => ['open'=>0,'in_progress'=>0,'completed'=>0,'overdue'=>0],
                'myTeam' => collect(),
                'recentAssignments' => collect(),
            ]);
        }

        // Team members (assuming users table has team_id)
        $myTeam = User::where('team_id',$teamId)->get();
        $teamMemberIds = $myTeam->pluck('id');

        // Assignments for the team (assuming you store assigned_to user id)
        $base = Assignment::whereIn('assigned_to', $teamMemberIds);

        $counts = [
            'open'        => (int) $base->clone()->where('status','Open')->count(),
            'in_progress' => (int) $base->clone()->where('status','In Progress')->count(),
            'completed'   => (int) $base->clone()->where('status','Completed')->count(),
            'overdue'     => (int) $base->clone()->where('status','!=','Completed')->where('due_date','<',Carbon::today())->count(),
        ];

        $recentAssignments = $base->clone()->latest()->take(10)->get();

        return view('dashboards.team', compact('counts','myTeam','recentAssignments') + ['notAssigned'=>false]);
    }
}
