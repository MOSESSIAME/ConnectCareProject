<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    /**
     * List teams (admin area).
     */
    public function index()
    {
        $teams = Team::with('leader')->paginate(10);
        $title = 'Teams';
        $route = 'admin.teams'; // for convenience if you reference it in views

        return view('admin.teams.index', compact('teams', 'title', 'route'));
    }

    /**
     * Show create form.
     */
    public function create()
    {
        $leaders = User::whereHas('role', fn ($q) => $q->where('name', 'Team Leader'))
            ->orderBy('name')
            ->get();

        $title = 'Teams';
        $route = 'admin.teams';

        return view('admin.teams.create', compact('leaders', 'title', 'route'));
    }

    /**
     * Store a new team.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:100|unique:teams,name',
            'leader_id' => 'required|exists:users,id',
        ]);

        Team::create([
            'name'      => $validated['name'],
            'leader_id' => $validated['leader_id'],
        ]);

        return redirect()->route('admin.teams.index')
            ->with('success', 'Team created successfully!');
    }

    /**
     * Edit form.
     */
    public function edit(Team $team)
    {
        $leaders = User::whereHas('role', fn ($q) => $q->where('name', 'Team Leader'))
            ->orderBy('name')
            ->get();

        $title = 'Teams';
        $route = 'admin.teams';
        $item  = $team;

        return view('admin.teams.edit', compact('item', 'leaders', 'title', 'route'));
    }

    /**
     * Update a team.
     */
    public function update(Request $request, Team $team)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:100|unique:teams,name,' . $team->id,
            'leader_id' => 'required|exists:users,id',
        ]);

        $team->update([
            'name'      => $validated['name'],
            'leader_id' => $validated['leader_id'],
        ]);

        return redirect()->route('admin.teams.index')
            ->with('success', 'Team updated successfully!');
    }

    /**
     * Delete a team.
     */
    public function destroy(Team $team)
    {
        $team->delete();

        return redirect()->route('admin.teams.index')
            ->with('success', 'Team deleted successfully!');
    }
}
