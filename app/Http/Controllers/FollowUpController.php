<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\FollowUpHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FollowUpController extends Controller
{
    public function index()
    {
        return $this->my();
    }

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

        return view('followups.my', compact('histories'));
    }

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

    /**
     * Edit followup form.
     */
    public function edit(FollowUpHistory $followup)
    {
        $user = Auth::user();
        $role = $user->role->name ?? '';

        if (!in_array($role, ['Admin', 'Team Leader']) && $followup->assignment?->assigned_to !== $user->id) {
            abort(403, 'You cannot edit this follow-up.');
        }

        $methods  = FollowUpHistory::METHODS;
        $outcomes = FollowUpHistory::OUTCOMES;
        $statuses = FollowUpHistory::STATUSES;

        return view('followups.edit', compact('followup', 'methods', 'outcomes', 'statuses'));
    }

    /**
     * Update followup.
     */
    public function update(Request $request, FollowUpHistory $followup)
    {
        $user = Auth::user();
        $role = $user->role->name ?? '';

        if (!in_array($role, ['Admin', 'Team Leader']) && $followup->assignment?->assigned_to !== $user->id) {
            abort(403, 'You cannot update this follow-up.');
        }

        $request->validate([
            'method'  => 'required|in:' . implode(',', FollowUpHistory::METHODS),
            'notes'   => 'nullable|string|max:2000',
            'outcome' => 'required|in:' . implode(',', FollowUpHistory::OUTCOMES),
            'status'  => 'required|in:' . implode(',', FollowUpHistory::STATUSES),
        ]);

        $followup->update($request->only(['method', 'notes', 'outcome', 'status']));

        return redirect()->route('followups.assignment', $followup->assignment_id)
            ->with('success', 'Follow-up updated successfully.');
    }

    /**
     * Destroy a follow-up (allowed for Admin, Team Leader, assignment assignee).
     */
    public function destroy(FollowUpHistory $followup)
    {
        $user = Auth::user();
        $role = $user->role->name ?? '';

        if (!in_array($role, ['Admin', 'Team Leader']) && $followup->assignment?->assigned_to !== $user->id) {
            abort(403, 'You cannot delete this follow-up.');
        }

        $assignmentId = $followup->assignment_id;
        $followup->delete();

        return redirect()->route('followups.assignment', $assignmentId)
            ->with('success', 'Follow-up deleted.');
    }
}
