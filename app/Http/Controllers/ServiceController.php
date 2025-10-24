<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * List services.
     */
    public function index()
    {
        $services = Service::orderBy('service_date', 'desc')->paginate(10);
        return view('services.index', compact('services'));
    }

    /**
     * Show create form.
     */
    public function create()
    {
        return view('services.create');
    }

    /**
     * Store new service.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:services,name',
            'date' => 'required|date',
        ]);

        Service::create([
            'name'         => $validated['name'],
            'service_date' => $validated['date'], // map form 'date' -> DB 'service_date'
        ]);

        return redirect()->route('services.index')
            ->with('success', 'Service created successfully.');
    }

    /**
     * Show edit form.
     */
    public function edit(Service $service)
    {
        return view('services.edit', compact('service'));
    }

    /**
     * Update service.
     */
    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:services,name,' . $service->id,
            'date' => 'required|date',
        ]);

        $service->update([
            'name'         => $validated['name'],
            'service_date' => $validated['date'],
        ]);

        return redirect()->route('services.index')
            ->with('success', 'Service updated successfully.');
    }

    /**
     * Delete service.
     */
    public function destroy(Service $service)
    {
        $service->delete();

        return redirect()->route('services.index')
            ->with('success', 'Service deleted successfully.');
    }
}
