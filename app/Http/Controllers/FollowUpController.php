<?php

namespace App\Http\Controllers;

use App\Models\FollowUpHistory;
use App\Models\Assignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FollowUpController extends Controller
{
    /**
     * Display follow-up histories.
     */
    public function index()
    {
        $histories = FollowUpHistory::with('assignment.member')->paginate(10);
        return view('followups.index', compact('histories'));
    }

    /**
     * Show form to add follow-up record.
     */
    public function create()
    {
        $assignments = Assignment::with('member')->get();
        return view('followups.create', compact('assignments'));
    }

    /**
     * Store a follow-up record.
     */
    public function store(Request $request)
    {
        $request->validate([
            'assignment_id' => 'required|exists:assignments,id',
            'message' => 'required|string',
            'status' => 'required|in:Pending,In Progress,Completed',
        ]);

        FollowUpHistory::create([
            'assignment_id' => $request->assignment_id,
            'message' => $request->message,
            'status' => $request->status,
        ]);

        return redirect()->route('followups.index')->with('success', 'Follow-up recorded successfully.');
    }
}
