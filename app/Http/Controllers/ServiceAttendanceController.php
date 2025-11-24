<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServiceAttendance;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf; // <- requires barryvdh/laravel-dompdf

class ServiceAttendanceController extends Controller
{
    /**
     * List attendance records with filters + summary.
     */
    public function index(Request $request)
    {
        // Base query for table + totals
        $base = ServiceAttendance::with('service');

        // Filters
        if ($request->filled('service_id')) {
            $base->where('service_id', $request->service_id);
        }

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $base->whereBetween('created_at', [
                Carbon::parse($request->from_date)->startOfDay(),
                Carbon::parse($request->to_date)->endOfDay(),
            ]);
        }

        // Table
        $records = (clone $base)
            ->latest()
            ->paginate(10)
            ->withQueryString();

        // Totals over filtered dataset (for the big numbers row)
        $totals = (clone $base)
            ->reorder()
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

        // Monthly totals (reset every month)
        $monthStart = now()->startOfMonth();
        $monthEnd   = now()->endOfMonth();
        $monthLabel = now()->format('M Y');

        $monthly = ServiceAttendance::query()
            ->when($request->filled('service_id'), fn($q) => $q->where('service_id', $request->service_id))
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->selectRaw('
                COALESCE(SUM(first_timers),0)  as first_timers_total,
                COALESCE(SUM(new_converts),0)  as new_converts_total
            ')
            ->first();

        $monthlyTotals = [
            'first_timers' => (int) $monthly->first_timers_total,
            'new_converts' => (int) $monthly->new_converts_total,
        ];

        $services = Service::orderBy('name')->get(['id', 'name', 'service_date']);

        return view('attendance.index', compact(
            'records',
            'summary',
            'services',
            'monthLabel',
            'monthlyTotals'
        ));
    }

    /**
     * Show the form to record attendance.
     */
    public function create()
    {
        // Keep services as fixed names (no date appended here)
        $services = Service::orderBy('name')
            ->get(['id', 'name', 'service_date']); // service_date left for reference but not shown in select

        return view('attendance.create', compact('services'));
    }

    /**
     * Persist new attendance (allow multiple entries per service).
     *
     * NOTE: We changed behaviour:
     * - previously updateOrCreate(['service_id' => ...]) enforced a single attendance per service
     * - now we create a new attendance row each time (created_at becomes the attendance date)
     */
    public function store(Request $request)
    {
        $data = $this->validatePayload($request);

        // Create new attendance record (do NOT enforce uniqueness on service_id)
        ServiceAttendance::create([
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
            ->with('success', 'Attendance saved successfully.');
    }

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

    public function update(Request $request, ServiceAttendance $attendance)
    {
        $data = $this->validatePayload($request, $attendance);

        // We no longer enforce uniqueness across service_id globally; if you still want to prevent
        // duplicates on the same created date you can implement that logic here.
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

    public function destroy(ServiceAttendance $attendance)
    {
        $attendance->delete();

        return redirect()->route('attendance.index')
            ->with('success', 'Attendance deleted.');
    }

    private function validatePayload(Request $request, ?ServiceAttendance $attendance = null): array
    {
        // Removed the unique rule on service_id so you can reuse service names
        return $request->validate([
            'service_id'    => [
                'required',
                'exists:services,id',
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

    /**
     * Export filtered attendance to PDF.
     * Route: GET /attendance/export/pdf
     */
    public function exportPdf(Request $request)
    {
        // Build the same query used in index (but return all rows matching filters)
        $query = ServiceAttendance::with('service');

        if ($request->filled('service_id')) {
            $query->where('service_id', $request->service_id);
        }

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('created_at', [
                Carbon::parse($request->from_date)->startOfDay(),
                Carbon::parse($request->to_date)->endOfDay(),
            ]);
        }

        $rows = $query->latest()->get();

        // Prepare a human-friendly filters array for the pdf header
        $filters = [
            'service'   => optional(Service::find($request->service_id))->name,
            'from_date' => $request->from_date,
            'to_date'   => $request->to_date,
        ];

        // If you have barryvdh/laravel-dompdf installed, return a PDF; otherwise return the HTML view
        try {
            $pdf = Pdf::loadView('attendance.pdf', compact('rows', 'filters'));
            $filename = 'attendance_' . now()->format('Ymd_His') . '.pdf';
            return $pdf->download($filename);
        } catch (\Throwable $e) {
            // Fallback: return HTML view so you can see output in browser (useful for debugging)
            return view('attendance.pdf', compact('rows', 'filters'));
        }
    }
}
