<?php

namespace App\Http\Controllers;

use App\Models\Homecell;
use App\Models\Zone;
use App\Models\User;
use Illuminate\Http\Request;

class HomecellController extends Controller
{
    /**
     * List homecells (admin area).
     */
    public function index()
    {
        $homecells = Homecell::with(['zone', 'leader'])->paginate(10);

        // Views live at resources/views/homecells/*
        return view('homecells.index', compact('homecells'));
    }

    /**
     * Show create form.
     */
    public function create()
    {
        $zones   = Zone::orderBy('name')->get();
        $leaders = User::orderBy('name')->get();

        // Render the non-admin view path you already have
        return view('homecells.create', compact('zones', 'leaders'));
    }

    /**
     * Store a new homecell.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:100|unique:homecells,name',
            'zone_id'   => 'required|exists:zones,id',
            'leader_id' => 'nullable|exists:users,id',
        ]);

        Homecell::create([
            'name'      => $validated['name'],
            'zone_id'   => $validated['zone_id'],
            'leader_id' => $validated['leader_id'] ?? null,
        ]);

        // Keep redirects using the admin route name (from your routes group)
        return redirect()->route('admin.homecells.index')
            ->with('success', 'Homecell created successfully.');
    }

    /**
     * Edit form.
     */
    public function edit(Homecell $homecell)
    {
        $zones   = Zone::orderBy('name')->get();
        $leaders = User::orderBy('name')->get();

        return view('homecells.edit', compact('homecell', 'zones', 'leaders'));
    }

    /**
     * Update record.
     */
    public function update(Request $request, Homecell $homecell)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:100|unique:homecells,name,' . $homecell->id,
            'zone_id'   => 'required|exists:zones,id',
            'leader_id' => 'nullable|exists:users,id',
        ]);

        $homecell->update([
            'name'      => $validated['name'],
            'zone_id'   => $validated['zone_id'],
            'leader_id' => $validated['leader_id'] ?? null,
        ]);

        return redirect()->route('admin.homecells.index')
            ->with('success', 'Homecell updated successfully.');
    }

    /**
     * Delete record.
     */
    public function destroy(Homecell $homecell)
    {
        $homecell->delete();

        return redirect()->route('admin.homecells.index')
            ->with('success', 'Homecell deleted successfully.');
    }
}
