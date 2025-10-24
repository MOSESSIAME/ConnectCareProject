<?php

namespace App\Http\Controllers;

use App\Models\ServiceUnit;
use Illuminate\Http\Request;

class ServiceUnitController extends Controller
{
    /**
     * Display all service units.
     */
    public function index()
    {
        $units = ServiceUnit::all();
        return view('service_units.index', compact('units'));
    }

    /**
     * Show form to create a new service unit.
     */
    public function create()
    {
        return view('service_units.create');
    }

    /**
     * Store new service unit in database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:service_units,name',
            'description' => 'nullable|string|max:255',
        ]);

        ServiceUnit::create($request->only('name', 'description'));

        return redirect()->route('service_units.index')->with('success', 'Service Unit added successfully.');
    }

    /**
     * Show form to edit an existing unit.
     */
    public function edit(ServiceUnit $serviceUnit)
    {
        return view('service_units.edit', compact('serviceUnit'));
    }

    /**
     * Update an existing service unit.
     */
    public function update(Request $request, ServiceUnit $serviceUnit)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:service_units,name,' . $serviceUnit->id,
            'description' => 'nullable|string|max:255',
        ]);

        $serviceUnit->update($request->only('name', 'description'));

        return redirect()->route('service_units.index')->with('success', 'Service Unit updated successfully.');
    }

    /**
     * Delete a service unit.
     */
    public function destroy(ServiceUnit $serviceUnit)
    {
        $serviceUnit->delete();
        return redirect()->route('service_units.index')->with('success', 'Service Unit deleted successfully.');
    }
}
