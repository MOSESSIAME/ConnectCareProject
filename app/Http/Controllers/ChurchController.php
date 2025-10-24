<?php

namespace App\Http\Controllers;

use App\Models\Church;
use Illuminate\Http\Request;

class ChurchController extends Controller
{
    /**
     * Display a list of all churches.
     */
    public function index()
    {
        $records = Church::paginate(10);

        // Use admin-prefixed route base so the blades can call route("$route.*")
        $title = 'Churches';
        $route = 'admin.churches';

        return view('admin.churches.index', compact('records', 'title', 'route'));
    }

    /**
     * Show the form for creating a new church.
     */
    public function create()
    {
        $title = 'Churches';
        $route = 'admin.churches';

        return view('admin.churches.create', compact('title', 'route'));
    }

    /**
     * Store a newly created church in the database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:churches,name',
        ]);

        Church::create([
            'name' => $request->name,
        ]);

        return redirect()
            ->route('admin.churches.index')
            ->with('success', 'Church created successfully!');
    }

    /**
     * Show the form for editing the specified church.
     */
    public function edit(Church $church)
    {
        $item  = $church;
        $title = 'Churches';
        $route = 'admin.churches';

        return view('admin.churches.edit', compact('item', 'title', 'route'));
    }

    /**
     * Update the specified church in the database.
     */
    public function update(Request $request, Church $church)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:churches,name,' . $church->id,
        ]);

        $church->update([
            'name' => $request->name,
        ]);

        return redirect()
            ->route('admin.churches.index')
            ->with('success', 'Church updated successfully!');
    }

    /**
     * Remove the specified church from the database.
     */
    public function destroy(Church $church)
    {
        $church->delete();

        return redirect()
            ->route('admin.churches.index')
            ->with('success', 'Church deleted successfully!');
    }
}
