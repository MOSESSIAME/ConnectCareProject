<!-- <?php> -->
    /**
     * Display follow-up history for a specific assignment.
     */
    // <!-- public function index($assignmentId) -->
    // {
        // ✅ Use find() instead of findOrFail() to handle missing assignments gracefully
    //     <!-- $assignment = Assignment::with(['member', 'assignedTo', 'assignedBy'])->find($assignmentId);

    //     if (!$assignment) {
    //         return redirect()
    //             ->route('team-member.dashboard')
    //             ->with('error', 'This assignment no longer exists or was removed.');
    //     }

    //     $histories = FollowUpHistory::where('assignment_id', $assignmentId)
    //         ->orderBy('created_at', 'desc')
    //         ->get();

    //     return view('followups.index', compact('assignment', 'histories'));
    // } -->

    /**
     * Show form to log a new follow-up.
     */
    // <!-- public function create($assignmentId)
    // {
    //     $assignment = Assignment::with('member')->find($assignmentId);

    //     if (!$assignment) {
    //         return redirect()
    //             ->route('team-member.dashboard')
    //             ->with('error', 'That assignment could not be found.');
    //     }

    //     return view('followups.create', compact('assignment'));
    // } -->

    /**
     * Store a new follow-up entry.
     */
//     <!-- public function store(Request $request, $assignmentId)
//     {
//         $assignment = Assignment::find($assignmentId);

//         if (!$assignment) {
//             return redirect()
//                 ->route('team-member.dashboard')
//                 ->with('error', 'Unable to log follow-up. The assignment was not found.');
//         }

//         $request->validate([
//             'method' => 'required|in:Call,Visit,SMS,WhatsApp,Meeting',
//             'notes' => 'required|string|max:1000',
//             'outcome' => 'required|in:Reached,Not Reached,Postponed,Converted,Other',
//             'next_follow_up_date' => 'nullable|date|after_or_equal:today',
//         ]);

//         FollowUpHistory::create([
//             'assignment_id' => $assignmentId,
//             'notes' => $request->notes,
//             'method' => $request->method,
//             'outcome' => $request->outcome,
//             'next_follow_up_date' => $request->next_follow_up_date,
//             'status' => 'Pending',
//         ]);

//         // ✅ Update assignment status safely
//         if ($assignment->status !== 'Completed') {
//             $assignment->update(['status' => 'In Progress']);
//         }

//         return redirect()
//             ->route('followups.index', $assignmentId)
//             ->with('success', 'Follow-up entry logged successfully!');
//     } -->

//     /**
//      * Mark a follow-up as completed.
//      */
//     <!-- public function complete($id)
//     {
//         $followUp = FollowUpHistory::find($id);

//         if (!$followUp) {
//             return redirect()
//                 ->route('team-member.dashboard')
//                 ->with('error', 'That follow-up record could not be found.');
//         }

//         $followUp->update(['status' => 'Completed']);

//         // ✅ Update assignment only if it exists
//         $assignment = $followUp->assignment;
//         if ($assignment) {
//             $hasPending = $assignment->followUps()->where('status', 'Pending')->exists();

//             if (!$hasPending) {
//                 $assignment->update(['status' => 'Completed']);
//             }
//         }
// <?php

// namespace App\Http\Controllers;

// use App\Models\FollowUpHistory;
// use App\Models\Assignment;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;

// class FollowUpHistoryController extends Controller
// {
//         return back()->with('success', 'Follow-up marked as completed.');
//     }

    /**
     * ✅ Show all follow-ups logged by the currently authenticated Team Member.
     */
//     public function myFollowUps()
//     {
//         $userId = Auth::id();

//         $followups = FollowUpHistory::whereHas('assignment', function ($query) use ($userId) {
//                 $query->where('assigned_to', $userId);
//             })
//             ->with(['assignment.member'])
//             ->latest()
//             ->paginate(10);

//         return view('followups.my', compact('followups'));
//     }
// }
