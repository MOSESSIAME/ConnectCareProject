<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\FollowUpHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FollowUpController extends Controller
{
    /**
     * GET /followups
     * Show "my follow-ups" (admins/team leaders see all).
     * No redirect to a route name, so it won’t blow up if a name is missing.
     */
    public function index()
    {
        return $this->my();
    }

    /**
     * Same data as index(), split out in case you also want a named route.
     */
    public function my()
    {
        $user = Auth::user();
        $role = $user->role->name ?? '';

        $query = FollowUpHistory::with(['assignment.member', 'assignment.assignedTo'])
            ->latest();

        if (!in_array($role, ['Admin', 'Team Leader'])) {
            $query->whereHas('assignment', fn ($q) => $q->where('assigned_to', $user->id));
        }

        $histories = $query->paginate(10);

        // This should be your existing "My Follow-ups" blade
        return view('followups.my', compact('histories'));
    }

    /**
     * Follow-ups for a specific assignment.
     * GET /followups/{assignment}
     */
    public function assignmentIndex(Assignment $assignment)
    {
        $user = Auth::user();
        $role = $user->role->name ?? '';

        if (!in_array($role, ['Admin', 'Team Leader']) && $assignment->assigned_to !== $user->id) {
            abort(403, 'You cannot view follow-ups for this assignment.');
        }

        $histories = FollowUpHistory::where('assignment_id', $assignment->id)
            ->latest()
            ->get();

        return view('followups.index', compact('assignment', 'histories'));
    }

    /**
     * GET /followups/{assignment}/create
     */
    public function create(Assignment $assignment)
    {
        $user = Auth::user();
        $role = $user->role->name ?? '';

        if (!in_array($role, ['Admin', 'Team Leader']) && $assignment->assigned_to !== $user->id) {
            abort(403, 'You cannot log a follow-up for this assignment.');
        }

        $methods  = FollowUpHistory::METHODS;
        $outcomes = FollowUpHistory::OUTCOMES;
        $statuses = FollowUpHistory::STATUSES;

        return view('followups.create', compact('assignment', 'methods', 'outcomes', 'statuses'));
    }

    /**
     * POST /followups/{assignment}
     */
    public function store(Request $request, Assignment $assignment)
    {
        $user = Auth::user();
        $role = $user->role->name ?? '';

        if (!in_array($role, ['Admin', 'Team Leader']) && $assignment->assigned_to !== $user->id) {
            abort(403, 'You cannot log a follow-up for this assignment.');
        }

        $request->validate([
            'method'  => 'required|in:' . implode(',', FollowUpHistory::METHODS),
            'notes'   => 'nullable|string|max:2000',
            'outcome' => 'required|in:' . implode(',', FollowUpHistory::OUTCOMES),
            'status'  => 'required|in:' . implode(',', FollowUpHistory::STATUSES),
        ]);

        FollowUpHistory::create([
            'assignment_id' => $assignment->id,
            'method'        => $request->method,
            'notes'         => $request->notes,
            'outcome'       => $request->outcome,
            'status'        => $request->status,
        ]);

        return redirect()
            ->route('followups.assignment', $assignment->id)
            ->with('success', 'Follow-up recorded successfully.');
    }

    /**
     * POST /followups/complete/{id}
     */
    public function complete($id)
    {
        $followup = FollowUpHistory::with('assignment')->findOrFail($id);

        $user = Auth::user();
        $role = $user->role->name ?? '';

        if (!in_array($role, ['Admin', 'Team Leader']) && $followup->assignment?->assigned_to !== $user->id) {
            abort(403, 'You cannot complete this follow-up.');
        }

        $followup->update(['status' => 'Completed']);

        return back()->with('success', 'Follow-up marked as completed.');
    }
}
