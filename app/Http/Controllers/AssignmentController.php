<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Member;
use App\Models\Team;
use App\Models\User;
use App\Models\FollowUpHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AssignmentController extends Controller
{
    /**
     * List assignments:
     * - Admin: all (with filters)
     * - Team Leader: only assignments for the team they lead (with filters + ?mine=1)
     * - Team Member / Staff: only assignments assigned to them
     */
    public function index()
    {
        $user  = Auth::user();
        $role  = $user->role->name ?? '';

        $query = Assignment::with(['member', 'assignedBy', 'assignedTo', 'team'])->latest();

        if ($role === 'Admin') {
            // see all
        } elseif ($role === 'Team Leader') {
            $teamId = optional($user->leadsTeam)->id;
            $query->where('team_id', $teamId);
        } else {
            // Team Member / Staff
            $query->where('assigned_to', $user->id);
        }

        // Optional: for leaders "My Assignments"
        if (request()->boolean('mine')) {
            $query->where('assigned_to', $user->id);
        }

        // Filters (from request)
        $status     = request('status');
        $filterTeam = request('team_id');
        $assignedTo = request('assigned_to');
        $q          = trim(request('q', ''));
        $createdBy  = request('created_by');
        $from       = request('from');
        $to         = request('to');

        if ($status) {
            $query->where('status', $status);
        }

        // team filter only allowed by Admin (keeps your previous behaviour)
        if ($role === 'Admin' && $filterTeam) {
            $query->where('team_id', $filterTeam);
        }

        if ($assignedTo) {
            $query->where('assigned_to', $assignedTo);
        }

        if ($q !== '') {
            $query->where(function ($qq) use ($q) {
                $qq->whereHas('member', function ($m) use ($q) {
                        $m->where('full_name', 'like', "%{$q}%")
                          ->orWhere('phone', 'like', "%{$q}%");
                    })
                   ->orWhereHas('assignedTo', function ($u) use ($q) {
                        $u->where('name', 'like', "%{$q}%");
                    });
            });
        }

        // New: filter by who created the assignment
        if ($createdBy) {
            $query->where('assigned_by', $createdBy);
        }

        // New: date range filters
        if ($from) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to) {
            $query->whereDate('created_at', '<=', $to);
        }

        $assignments = $query->paginate(10)->withQueryString();

        //
        // Provide lists for the filter selects so the view can render options
        //
        // Teams: all team rows (admin filter list)
        $teams = Team::orderBy('name')->get();

        // Assignees: union of users present in team_user pivot OR used in assignments
        $pivotUserIds = DB::table('team_user')->pluck('user_id')->unique()->filter()->values()->toArray();
        $assignedIds  = Assignment::whereNotNull('assigned_to')->pluck('assigned_to')->unique()->filter()->values()->toArray();
        $assigneeIds  = array_values(array_unique(array_merge($pivotUserIds, $assignedIds)));

        $assignees = User::whereIn('id', $assigneeIds)->orderBy('name')->get();

        // Creators: users who have created assignments (assigned_by)
        $creatorIds = Assignment::whereNotNull('assigned_by')->pluck('assigned_by')->unique()->filter()->values()->toArray();
        $creators = User::whereIn('id', $creatorIds)->orderBy('name')->get();

        // Pass current filter values to the view as well (optional convenience)
        return view('assignments.index', [
            'assignments' => $assignments,
            'teams'       => $teams,
            'assignees'   => $assignees,
            'creators'    => $creators,
            'q'           => $q,
            'status'      => $status,
            'team_id'     => $filterTeam,
            'assigned_to' => $assignedTo,
            'created_by'  => $createdBy,
            'from'        => $from,
            'to'          => $to,
        ]);
    }

    /**
     * Admin: create view to assign a standby member to a team (and optionally a member).
     */
    public function create()
    {
        // Standby = FT/NC with no Active/Reassigned assignment
        $members = Member::whereIn('type', ['First-timer', 'New Convert'])
            ->whereDoesntHave('assignments', function ($q) {
                $q->whereIn('status', ['Active', 'Reassigned']);
            })
            ->orderBy('full_name')
            ->get();

        // load teams with their members (no users.team_id needed)
        $teams = Team::with(['members:id,name'])->orderBy('name')->get();

        return view('assignments.create', compact('members', 'teams'));
    }

    /**
     * Admin: create an assignment for a member against a team (assigned_to optional).
     */
    public function store(Request $request)
    {
        $request->validate([
            'member_id'   => 'required|exists:members,id',
            'team_id'     => 'required|exists:teams,id',
            'assigned_to' => 'nullable|exists:users,id',
            'status'      => 'nullable|in:Active,Reassigned,Completed',
        ]);

        // Prevent duplicate active/reassigned for the same member
        $exists = Assignment::where('member_id', $request->member_id)
            ->whereIn('status', ['Active', 'Reassigned'])
            ->exists();
        abort_if($exists, 422, 'This member already has an active assignment.');

        // If assigning directly to a user, ensure they belong to that team (via pivot)
        if ($request->filled('assigned_to')) {
            $inTeam = DB::table('team_user')
                ->where('team_id', $request->team_id)
                ->where('user_id', $request->assigned_to)
                ->exists();
            abort_unless($inTeam, 422, 'Selected user is not in the chosen team.');
        }

        Assignment::create([
            'member_id'   => $request->member_id,
            'assigned_by' => Auth::id(),
            'assigned_to' => $request->assigned_to, // may be null → leader can assign later
            'team_id'     => $request->team_id,
            'status'      => $request->status ?? 'Active',
        ]);

        return redirect()->route('assignments.index')
            ->with('success', 'Assignment created successfully.');
    }

    /**
     * Standby / Backlog page:
     * - Admin: FT/NC with no active assignment (global pool)
     * - Team Leader: assignments in my team with NULL assigned_to (team backlog)
     */
    public function standby(Request $request)
    {
        $user = Auth::user();
        $role = $user->role->name ?? '';

        if ($role === 'Admin') {
            $q = trim($request->get('q', ''));

            $members = Member::whereIn('type', ['First-timer', 'New Convert'])
                ->whereDoesntHave('assignments', function ($q2) {
                    $q2->whereIn('status', ['Active', 'Reassigned']);
                })
                ->when($q, function ($m) use ($q) {
                    $m->where('full_name', 'like', "%{$q}%")
                      ->orWhere('phone', 'like', "%{$q}%");
                })
                ->orderBy('full_name')
                ->paginate(15)
                ->withQueryString();

            $teams = Team::with('members:id,name')->orderBy('name')->get();

            $mode = 'admin';
            return view('assignments.standby', compact('mode', 'members', 'teams'));
        }

        if ($role === 'Team Leader') {
            $leaderTeamId = optional($user->leadsTeam)->id;
            abort_unless($leaderTeamId, 403, 'You are not attached to a team.');

            $backlog = Assignment::with(['member', 'team'])
                ->where('team_id', $leaderTeamId)
                ->whereNull('assigned_to')
                ->whereIn('status', ['Active', 'Reassigned'])
                ->latest()
                ->paginate(15)
                ->withQueryString();

            // pull members via pivot
            $teamMembers = Team::with('members:id,name')
                ->find($leaderTeamId)
                ?->members ?? collect();

            $mode = 'leader';
            return view('assignments.standby', compact('mode', 'backlog', 'teamMembers'));
        }

        abort(403);
    }

    /**
     * Team Leader/Admin: assign a team assignment to a specific team member.
     */
    public function assignToMember(Request $request, Assignment $assignment)
    {
        $request->validate([
            'assigned_to' => 'required|exists:users,id',
        ]);

        $user  = Auth::user();
        $role  = $user->role->name ?? '';
        $team  = $assignment->team;

        // Leader must own this team; Admin can assign any
        if ($role === 'Team Leader') {
            abort_unless($user->id === optional($team)->leader_id, 403, 'You can only assign within your team.');
        }

        // Ensure chosen user belongs to this assignment's team (via pivot)
        $inTeam = DB::table('team_user')
            ->where('team_id', $assignment->team_id)
            ->where('user_id', $request->assigned_to)
            ->exists();
        abort_unless($inTeam, 422, 'Selected user is not a member of this team.');

        $assignment->update([
            'assigned_to' => $request->assigned_to,
            // keep status as-is (Active/Reassigned)
        ]);

        return back()->with('success', 'Assignment given to the selected team member.');
    }

    /**
     * Quick status update (any role that can see the record may use).
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Active,Reassigned,Completed',
        ]);

        $assignment = Assignment::findOrFail($id);
        $assignment->update(['status' => $request->status]);

        return back()->with('success', 'Assignment status updated successfully.');
    }

    /**
     * Soft-delete an assignment.
     * Now deletes related follow-ups first to avoid FK constraint errors.
     */
    public function destroy($id)
    {
        $assignment = Assignment::findOrFail($id);

        $user = Auth::user();
        $role = $user->role->name ?? '';

        // permission: Admin can delete any, Team Leader only in their team
        if ($role === 'Team Leader') {
            $teamId = optional($user->leadsTeam)->id;
            abort_unless($teamId && $assignment->team_id == $teamId, 403, 'You can only delete assignments in your team.');
        }

        // Delete follow-ups for this assignment (to avoid FK constraint)
        FollowUpHistory::where('assignment_id', $assignment->id)->delete();

        // Now delete the assignment (soft or hard depending on your model)
        $assignment->delete();

        return back()->with('success', 'Assignment deleted successfully.');
    }

    /**
     * Show edit form for a single assignment.
     *
     * - Admin: can edit any assignment (change team, assigned_to, status)
     * - Team Leader: only assignments in their team; can change assigned_to within team
     */
    public function edit(Assignment $assignment)
    {
        $user = Auth::user();
        $role = $user->role->name ?? '';

        // Authorization: Team Leader can only edit assignments in their team
        if ($role === 'Team Leader') {
            $leaderTeamId = optional($user->leadsTeam)->id;
            abort_unless($leaderTeamId && $assignment->team_id == $leaderTeamId, 403, 'Not your team.');
        }

        // Load supporting data:
        // - Admin: all teams + their members
        // - Leader: only their team members
        if ($role === 'Admin') {
            $teams = Team::with('members:id,name')->orderBy('name')->get();
            $assignees = collect(); // kept for compatibility with views expecting this var
        } else {
            // Team Leader: load members only for their team
            $leaderTeamId = optional($user->leadsTeam)->id;
            $teams = Team::where('id', $leaderTeamId)->with('members:id,name')->get();
            $assignees = Team::with('members:id,name')->find($leaderTeamId)?->members ?? collect();
        }

        // Optionally provide a list of users in the current assignment team for the select
        $currentTeamMembers = Team::with('members:id,name')->find($assignment->team_id)?->members ?? collect();

        return view('assignments.edit', [
            'assignment' => $assignment,
            'teams' => $teams,
            'assignees' => $assignees,
            'currentTeamMembers' => $currentTeamMembers,
        ]);
    }

    /**
     * Update an assignment (team change, assigned_to, status)
     *
     * - Admin: can change team_id and assigned_to (must check pivot membership)
     * - Team Leader: can only change assigned_to within their team (status allowed)
     */
    public function update(Request $request, Assignment $assignment)
    {
        $user = Auth::user();
        $role = $user->role->name ?? '';

        if ($role === 'Admin') {
            $request->validate([
                'team_id'     => 'required|exists:teams,id',
                'assigned_to' => 'nullable|exists:users,id',
                'status'      => 'required|in:Active,Reassigned,Completed',
            ]);

            // If assigned_to present, check that user belongs to chosen team (via pivot)
            if ($request->filled('assigned_to')) {
                $inTeam = DB::table('team_user')
                    ->where('team_id', $request->team_id)
                    ->where('user_id', $request->assigned_to)
                    ->exists();
                abort_unless($inTeam, 422, 'Selected user is not in the chosen team.');
            }

            $assignment->update([
                'team_id'     => $request->team_id,
                'assigned_to' => $request->assigned_to,
                'status'      => $request->status,
            ]);

            return redirect()->route('assignments.index')->with('success', 'Assignment updated successfully.');
        }

        // Team Leader: can only update assigned_to (must be a member of their team)
        if ($role === 'Team Leader') {
            $leaderTeamId = optional($user->leadsTeam)->id;
            abort_unless($leaderTeamId && $assignment->team_id == $leaderTeamId, 403, 'Not your team.');

            $request->validate([
                'assigned_to' => 'required|exists:users,id',
                'status'      => 'required|in:Active,Reassigned,Completed',
            ]);

            // Check membership via pivot
            $inTeam = DB::table('team_user')
                ->where('team_id', $leaderTeamId)
                ->where('user_id', $request->assigned_to)
                ->exists();
            abort_unless($inTeam, 422, 'Selected user is not in your team.');

            $assignment->update([
                'assigned_to' => $request->assigned_to,
                'status'      => $request->status,
            ]);

            return redirect()->route('assignments.index')->with('success', 'Assignment updated within your team.');
        }

        // Other roles shouldn't be able to hit this route
        abort(403);
    }

    /**
     * Reassign form:
     * - Admin: can move across teams and/or to a different user
     * - Team Leader: must stay within their team
     */
    public function reassignForm(Request $request)
    {
        $user = Auth::user();
        $role = $user->role->name ?? '';

        if ($role === 'Admin') {
            $assignments = Assignment::with(['member','team','assignedTo'])
                ->latest()
                ->paginate(15)
                ->withQueryString();

            // Provide teams with their members (no users.team_id)
            $teams = Team::with(['members:id,name'])
                ->orderBy('name')
                ->get();

            return view('assignments.reassign', [
                'mode'        => 'admin',
                'assignments' => $assignments,
                'teams'       => $teams,
            ]);
        }

        // Team Leader: only their team
        $teamId = optional($user->leadsTeam)->id;
        abort_unless($teamId, 403, 'You are not attached to a team.');

        $assignments = Assignment::with(['member','team','assignedTo'])
            ->where('team_id', $teamId)
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $teamMembers = Team::with('members:id,name')
            ->find($teamId)
            ?->members ?? collect();

        return view('assignments.reassign', [
            'mode'        => 'leader',
            'assignments' => $assignments,
            'teamMembers' => $teamMembers,
        ]);
    }

    /**
     * Reassign POST:
     * - Admin: may change team_id and/or assigned_to (if assigned_to set, must belong to the chosen team)
     * - Team Leader: can only change assigned_to within their team
     */
    public function reassign(Request $request)
    {
        $user = Auth::user();
        $role = $user->role->name ?? '';

        if ($role === 'Admin') {
            $request->validate([
                'assignment_id' => 'required|exists:assignments,id',
                'team_id'       => 'required|exists:teams,id',
                'assigned_to'   => 'nullable|exists:users,id',
            ]);

            $assignment = Assignment::findOrFail($request->assignment_id);

            if ($request->filled('assigned_to')) {
                // Check membership via pivot
                $inTeam = DB::table('team_user')
                    ->where('team_id', $request->team_id)
                    ->where('user_id', $request->assigned_to)
                    ->exists();
                abort_unless($inTeam, 422, 'Selected user is not in the chosen team.');
            }

            $assignment->update([
                'team_id'     => $request->team_id,
                'assigned_to' => $request->assigned_to, // may be null; leader can pick later
                'status'      => 'Reassigned',
            ]);

            return back()->with('success', 'Assignment reassigned successfully.');
        }

        // Team Leader
        $request->validate([
            'assignment_id' => 'required|exists:assignments,id',
            'assigned_to'   => 'required|exists:users,id',
        ]);

        $assignment   = Assignment::with('team')->findOrFail($request->assignment_id);
        $leaderTeamId = optional($user->leadsTeam)->id;
        abort_unless($leaderTeamId && $assignment->team_id == $leaderTeamId, 403, 'Not your team.');

        // Check membership via pivot
        $inMyTeam = DB::table('team_user')
            ->where('team_id', $leaderTeamId)
            ->where('user_id', $request->assigned_to)
            ->exists();
        abort_unless($inMyTeam, 422, 'Selected user is not in your team.');

        $assignment->update([
            'assigned_to' => $request->assigned_to,
            'status'      => 'Reassigned',
        ]);

        return back()->with('success', 'Assignment reassigned within your team.');
    }

    /**
     * OPTIONAL: bulk assign (Admin only).
     */
    public function bulkForm()
    {
        $members = Member::whereIn('type', ['First-timer', 'New Convert'])
            ->whereDoesntHave('assignments', function ($q) {
                $q->whereIn('status', ['Active', 'Reassigned']);
            })
            ->orderBy('full_name')
            ->get();

        $teams = Team::with(['members:id,name'])->orderBy('name')->get();

        return view('assignments.bulk', compact('members', 'teams'));
    }

    public function bulkStore(Request $request)
    {
        $request->validate([
            'member_ids'   => 'required|array|min:1',
            'member_ids.*' => 'exists:members,id',
            'team_id'      => 'required|exists:teams,id',
            'assigned_to'  => 'nullable|exists:users,id',
        ]);

        if ($request->filled('assigned_to')) {
            // Check membership via pivot
            $inTeam = DB::table('team_user')
                ->where('team_id', $request->team_id)
                ->where('user_id', $request->assigned_to)
                ->exists();
            abort_unless($inTeam, 422, 'Selected user is not in the chosen team.');
        }

        foreach ($request->member_ids as $memberId) {
            $exists = Assignment::where('member_id', $memberId)
                ->whereIn('status', ['Active', 'Reassigned'])
                ->exists();
            if ($exists) {
                // skip silently
                continue;
            }

            Assignment::create([
                'member_id'   => $memberId,
                'assigned_by' => Auth::id(),
                'team_id'     => $request->team_id,
                'assigned_to' => $request->assigned_to,
                'status'      => 'Active',
            ]);
        }

        return redirect()->route('assignments.index')->with('success', 'Bulk assignment completed.');
    }
}
