<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Assignment;
use App\Models\FollowUpHistory;

class TeamMemberDashboardController extends Controller
{
    /**
     * Team Member dashboard: recent assignments + stats.
     */
    public function index()
    {
        $userId = Auth::id();

        // Recent assignments (show member + team)
        $assignments = Assignment::with(['member', 'team'])
            ->where('assigned_to', $userId)
            ->latest()
            ->take(10)
            ->get();

        // Stats computed in DB (pending = Active + Reassigned)
        $followUpStats = [
            'total'     => Assignment::where('assigned_to', $userId)->count(),
            'completed' => Assignment::where('assigned_to', $userId)->where('status', 'Completed')->count(),
            'pending'   => Assignment::where('assigned_to', $userId)
                                ->whereIn('status', ['Active', 'Reassigned'])
                                ->count(),
        ];

        // Blade lives at resources/views/dashboards/team-member.blade.php
        return view('dashboards.team-member', compact('assignments', 'followUpStats'));
    }

    /**
     * Store a follow-up log for a specific assignment.
     */
    public function storeFollowUp(Request $request, $assignmentId)
    {
        $assignment = Assignment::findOrFail($assignmentId);

        // Ensure the assignment belongs to the logged-in team member
        if ($assignment->assigned_to !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Validate input (methods match your migration enum)
        $request->validate([
            'method'  => 'required|string|in:Call,Visit,SMS,WhatsApp,Email,Meeting',
            'notes'   => 'nullable|string|max:500',
            'outcome' => 'nullable|string|max:255',
        ]);

        // Create follow-up record (its own status lifecycle)
        FollowUpHistory::create([
            'assignment_id' => $assignment->id,
            'method'        => $request->method,
            'notes'         => $request->notes,
            'outcome'       => $request->outcome,
            'status'        => 'In Progress', // enum on follow_ups: Pending/In Progress/Completed
        ]);

        // Do NOT change assignment status here.

        return back()->with('success', '✅ Follow-up logged successfully.');
    }

    /**
     * Mark an assignment as completed.
     */
    public function completeAssignment($id)
    {
        $assignment = Assignment::findOrFail($id);

        // Ensure the logged-in team member owns this assignment
        if ($assignment->assigned_to !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $assignment->update(['status' => 'Completed']);

        // Update related follow-ups to completed
        FollowUpHistory::where('assignment_id', $assignment->id)
            ->update(['status' => 'Completed']);

        return back()->with('success', '✅ Assignment marked as completed.');
    }
}
