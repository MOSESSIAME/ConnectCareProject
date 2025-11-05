<?php


// app/Http/Controllers/Admin/TeamMemberController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;

class TeamMemberController extends Controller
{
    public function index(Team $team)
    {
        // Optional: limit candidates by role (e.g. Staff + Team Member)
        $candidates = User::query()
            ->with('role')
            ->orderBy('name')
            ->get();

        // Current members
        $members = $team->members()->with('role')->orderBy('name')->get();

        return view('admin.teams.members', compact('team', 'members', 'candidates'));
    }

    public function store(Request $request, Team $team)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        // attach if not already
        if (! $team->members()->where('users.id', $data['user_id'])->exists()) {
            $team->members()->attach($data['user_id']);
        }

        return back()->with('success', 'Member added to team.');
    }

    public function destroy(Team $team, User $user)
    {
        $team->members()->detach($user->id);

        return back()->with('success', 'Member removed from team.');
    }
}
