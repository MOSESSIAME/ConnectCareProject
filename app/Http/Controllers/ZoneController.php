<?php

namespace App\Http\Controllers;

use App\Models\Zone;
use App\Models\District;
use App\Models\User;            // <-- add
use Illuminate\Http\Request;

class ZoneController extends Controller
{
    public function index()
    {
        // Eager-load district + leader so the table can show both
        $records = Zone::with(['district', 'leader'])->paginate(10);

        $title  = 'Zones';
        $route  = 'admin.zones';
        $showDistrictColumn = true;

        return view('admin.zones.index', compact('records', 'title', 'route', 'showDistrictColumn'));
    }

    public function create()
    {
        $title     = 'Zones';
        $route     = 'admin.zones';
        $districts = District::orderBy('name')->get(['id','name']);

        // Only users with the "Zonal Leader" role
        $leaders = User::whereHas('role', fn($q) => $q->where('name', 'Zonal Leader'))
            ->orderBy('name')
            ->get(['id','name']);

        return view('admin.zones.create', compact('title', 'route', 'districts', 'leaders'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:100|unique:zones,name',
            'district_id' => 'required|exists:districts,id',
            'leader_id'   => 'nullable|exists:users,id',
        ]);

        Zone::create([
            'name'        => $request->name,
            'district_id' => $request->district_id,
            'leader_id'   => $request->leader_id,  // <-- new
        ]);

        return redirect()
            ->route('admin.zones.index')
            ->with('success', 'Zone created successfully!');
    }

    public function edit(Zone $zone)
    {
        $item      = $zone;
        $title     = 'Zones';
        $route     = 'admin.zones';
        $districts = District::orderBy('name')->get(['id','name']);

        $leaders = User::whereHas('role', fn($q) => $q->where('name', 'Zonal Leader'))
            ->orderBy('name')
            ->get(['id','name']);

        return view('admin.zones.edit', compact('item', 'title', 'route', 'districts', 'leaders'));
    }

    public function update(Request $request, Zone $zone)
    {
        $request->validate([
            'name'        => 'required|string|max:100|unique:zones,name,' . $zone->id,
            'district_id' => 'required|exists:districts,id',
            'leader_id'   => 'nullable|exists:users,id',
        ]);

        $zone->update([
            'name'        => $request->name,
            'district_id' => $request->district_id,
            'leader_id'   => $request->leader_id,   // <-- new
        ]);

        return redirect()
            ->route('admin.zones.index')
            ->with('success', 'Zone updated successfully!');
    }

    public function destroy(Zone $zone)
    {
        $zone->delete();

        return redirect()
            ->route('admin.zones.index')
            ->with('success', 'Zone deleted successfully!');
    }
}
