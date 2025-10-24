<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServiceAttendance;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class ServiceAttendanceController extends Controller
{
    /**
     * List attendance records with filters + summary.
     */
    public function index(Request $request)
    {
        // Build a base query FIRST (no ordering here)
        $base = ServiceAttendance::with('service');

        // Filters
        if ($request->filled('service_id')) {
            $base->where('service_id', $request->service_id);
        }

        // Uses created_at; switch to service_date if you prefer by joining services
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $base->whereBetween('created_at', [
                Carbon::parse($request->from_date)->startOfDay(),
                Carbon::parse($request->to_date)->endOfDay(),
            ]);
        }

        // Table records (apply ordering only here)
        $records = (clone $base)
            ->latest()                 // adds ORDER BY created_at desc
            ->paginate(10)
            ->withQueryString();

        // Totals over the entire filtered dataset (strip any ORDER BY)
        $totals = (clone $base)
            ->reorder()                // <— clears any ORDER BY to avoid MySQL 1140
            ->selectRaw('
                COALESCE(SUM(males),0)         as males,
                COALESCE(SUM(females),0)       as females,
                COALESCE(SUM(children),0)      as children,
                COALESCE(SUM(first_timers),0)  as first_timers,
                COALESCE(SUM(new_converts),0)  as new_converts,
                COALESCE(SUM(offering),0)      as offering
            ')
            ->first();

        $summary = [
            'total_males'        => (int) $totals->males,
            'total_females'      => (int) $totals->females,
            'total_children'     => (int) $totals->children,
            'total_first_timers' => (int) $totals->first_timers,
            'total_new_converts' => (int) $totals->new_converts,
            'total_offering'     => (float) $totals->offering,
        ];

        $services = Service::orderBy('name')->get(['id', 'name', 'service_date']);

        return view('attendance.index', compact('records', 'summary', 'services'));
    }

    /**
     * Show the form to record attendance.
     */
    public function create()
    {
        $services = Service::orderByDesc('service_date')
            ->orderBy('name')
            ->get(['id', 'name', 'service_date']);

        return view('attendance.create', compact('services'));
    }

    /**
     * Persist new attendance (idempotent per service).
     */
    public function store(Request $request)
    {
        $data = $this->validatePayload($request);

        // Avoid duplicates per service — update if already captured
        ServiceAttendance::updateOrCreate(
            ['service_id' => $data['service_id']],
            [
                'males'         => $data['males'],
                'females'       => $data['females'],
                'children'      => $data['children'] ?? 0,
                'first_timers'  => $data['first_timers'] ?? 0,
                'new_converts'  => $data['new_converts'] ?? 0,
                'offering'      => $data['offering'] ?? 0,
                'notes'         => $data['notes'] ?? null,
            ]
        );

        return redirect()->route('attendance.index')
            ->with('success', 'Attendance saved successfully.');
    }

    /**
     * Show edit form.
     */
    public function edit(ServiceAttendance $attendance)
    {
        $services = Service::orderByDesc('service_date')
            ->orderBy('name')
            ->get(['id', 'name', 'service_date']);

        return view('attendance.edit', [
            'attendance' => $attendance,
            'services'   => $services,
        ]);
    }

    /**
     * Update an attendance record.
     */
    public function update(Request $request, ServiceAttendance $attendance)
    {
        $data = $this->validatePayload($request, $attendance);

        // Keep the "one record per service" rule on edit
        if (isset($data['service_id'])) {
            $exists = ServiceAttendance::where('service_id', $data['service_id'])
                ->where('id', '!=', $attendance->id)
                ->exists();

            if ($exists) {
                return back()
                    ->withErrors(['service_id' => 'Attendance for this service already exists.'])
                    ->withInput();
            }
        }

        $attendance->update([
            'service_id'    => $data['service_id'],
            'males'         => $data['males'],
            'females'       => $data['females'],
            'children'      => $data['children'] ?? 0,
            'first_timers'  => $data['first_timers'] ?? 0,
            'new_converts'  => $data['new_converts'] ?? 0,
            'offering'      => $data['offering'] ?? 0,
            'notes'         => $data['notes'] ?? null,
        ]);

        return redirect()->route('attendance.index')
            ->with('success', 'Attendance updated successfully.');
    }

    /**
     * Delete an attendance record.
     */
    public function destroy(ServiceAttendance $attendance)
    {
        $attendance->delete();

        return redirect()->route('attendance.index')
            ->with('success', 'Attendance deleted.');
    }

    /**
     * Centralized validation (used by store & update).
     */
    private function validatePayload(Request $request, ?ServiceAttendance $attendance = null): array
    {
        return $request->validate([
            'service_id'    => [
                'required',
                'exists:services,id',
                Rule::unique('service_attendances', 'service_id')->ignore(optional($attendance)->id),
            ],
            'males'         => ['required', 'integer', 'min:0'],
            'females'       => ['required', 'integer', 'min:0'],
            'children'      => ['nullable', 'integer', 'min:0'],
            'first_timers'  => ['nullable', 'integer', 'min:0'],
            'new_converts'  => ['nullable', 'integer', 'min:0'],
            'offering'      => ['nullable', 'numeric', 'min:0'],
            'notes'         => ['nullable', 'string', 'max:1000'],
        ]);
    }
}
