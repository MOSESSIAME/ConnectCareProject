<?php

namespace App\Http\Controllers;

use App\Models\Zone;
use App\Models\District;
use Illuminate\Http\Request;

class ZoneController extends Controller
{
    /**
     * Display a list of all zones (Admin).
     */
    public function index()
    {
        // Eager-load district so the table can show it
        $records = Zone::with('district')->paginate(10);

        $title  = 'Zones';
        $route  = 'admin.zones';
        $showDistrictColumn = true; // tell the view to render the column

        return view('admin.zones.index', compact('records', 'title', 'route', 'showDistrictColumn'));
    }

    /**
     * Show the form for creating a new zone.
     */
    public function create()
    {
        $title     = 'Zones';
        $route     = 'admin.zones';
        $districts = District::orderBy('name')->get(['id','name']);

        return view('admin.zones.create', compact('title', 'route', 'districts'));
    }

    /**
     * Store a newly created zone in the database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:100|unique:zones,name',
            'district_id' => 'required|exists:districts,id',
        ]);

        Zone::create([
            'name'        => $request->name,
            'district_id' => $request->district_id,
        ]);

        return redirect()
            ->route('admin.zones.index')
            ->with('success', 'Zone created successfully!');
    }

    /**
     * Show the form for editing the specified zone.
     */
    public function edit(Zone $zone)
    {
        $item      = $zone;
        $title     = 'Zones';
        $route     = 'admin.zones';
        $districts = District::orderBy('name')->get(['id','name']);

        return view('admin.zones.edit', compact('item', 'title', 'route', 'districts'));
    }

    /**
     * Update the specified zone in the database.
     */
    public function update(Request $request, Zone $zone)
    {
        $request->validate([
            'name'        => 'required|string|max:100|unique:zones,name,' . $zone->id,
            'district_id' => 'required|exists:districts,id',
        ]);

        $zone->update([
            'name'        => $request->name,
            'district_id' => $request->district_id,
        ]);

        return redirect()
            ->route('admin.zones.index')
            ->with('success', 'Zone updated successfully!');
    }

    /**
     * Remove the specified zone from the database.
     */
    public function destroy(Zone $zone)
    {
        $zone->delete();

        return redirect()
            ->route('admin.zones.index')
            ->with('success', 'Zone deleted successfully!');
    }
}
