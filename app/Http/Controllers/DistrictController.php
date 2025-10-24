<?php

namespace App\Http\Controllers;

use App\Models\District;
use App\Models\Church;
use Illuminate\Http\Request;

class DistrictController extends Controller
{
    /**
     * Display a list of all districts (Admin area).
     */
    public function index()
    {
        $records = District::with('church')->paginate(10);
        $title = 'Districts';
        $route = 'admin.districts'; // Correct admin prefix

        return view('admin.districts.index', compact('records', 'title', 'route'));
    }

    /**
     * Show the form for creating a new district.
     */
    public function create()
    {
        $churches = Church::orderBy('name')->get();
        $title = 'Districts';
        $route = 'admin.districts';

        return view('admin.districts.create', compact('title', 'route', 'churches'));
    }

    /**
     * Store a newly created district in the database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:districts,name',
            'church_id' => 'required|exists:churches,id',
        ]);

        District::create($validated);

        return redirect()
            ->route('admin.districts.index')
            ->with('success', 'District created successfully!');
    }

    /**
     * Show the form for editing the specified district.
     */
    public function edit(District $district)
    {
        $churches = Church::orderBy('name')->get();
        $item = $district;
        $title = 'Districts';
        $route = 'admin.districts';

        return view('admin.districts.edit', compact('item', 'title', 'route', 'churches'));
    }

    /**
     * Update the specified district in the database.
     */
    public function update(Request $request, District $district)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:districts,name,' . $district->id,
            'church_id' => 'required|exists:churches,id',
        ]);

        $district->update($validated);

        return redirect()
            ->route('admin.districts.index')
            ->with('success', 'District updated successfully!');
    }

    /**
     * Remove the specified district from the database.
     */
    public function destroy(District $district)
    {
        $district->delete();

        return redirect()
            ->route('admin.districts.index')
            ->with('success', 'District deleted successfully!');
    }
}
