<?php

namespace App\Http\Controllers;

use App\Models\Church;
use App\Models\District;
use App\Models\Zone;
use App\Models\Homecell;
use App\Models\HomecellReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class HomecellReportController extends Controller
{
    /**
     * Display filtered, paginated reports (Admin, Pastor, Zonal Leader, Staff).
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Filters
        $churchId   = $request->get('church_id');
        $districtId = $request->get('district_id');
        $zoneId     = $request->get('zone_id');
        $homecellId = $request->get('homecell_id');
        $from       = $request->get('from'); // Y-m-d
        $to         = $request->get('to');   // Y-m-d
        $q          = trim((string) $request->get('q', ''));

        $query = HomecellReport::with(['church', 'district', 'zone', 'homecell', 'submittedBy'])
            ->latest();

        // Zonal Leader – confine to own zone
        if (($user->role->name ?? null) === 'Zonal Leader' && isset($user->zone_id)) {
            $query->where('zone_id', $user->zone_id);
        }

        // Apply filters
        $query->when($churchId,   fn($q2) => $q2->where('church_id',   $churchId))
              ->when($districtId, fn($q2) => $q2->where('district_id', $districtId))
              ->when($zoneId,     fn($q2) => $q2->where('zone_id',     $zoneId))
              ->when($homecellId, fn($q2) => $q2->where('homecell_id', $homecellId))
              ->when($from, function ($q2) use ($from) {
                  if (Carbon::hasFormat($from, 'Y-m-d')) {
                      $q2->whereDate('created_at', '>=', $from);
                  }
              })
              ->when($to, function ($q2) use ($to) {
                  if (Carbon::hasFormat($to, 'Y-m-d')) {
                      $q2->whereDate('created_at', '<=', $to);
                  }
              })
              ->when($q, function ($q2) use ($q) {
                  // Search across testimonies and submitter's name
                  $q2->where(function ($sub) use ($q) {
                      $sub->where('testimonies', 'like', "%{$q}%")
                          ->orWhereHas('submittedBy', function ($u) use ($q) {
                              $u->where('name', 'like', "%{$q}%");
                          });
                  });
              });

        $reports = $query->paginate(15)->appends($request->query());

        // For filters
        $churches = Church::orderBy('name')->get(['id','name']);

        return view('reports.homecells.index', compact(
            'reports',
            'churches',
            'churchId', 'districtId', 'zoneId', 'homecellId',
            'from', 'to', 'q'
        ));
    }

    /**
     * Export current filter results to PDF (landscape A4).
     * Route: GET /reports/homecells/export/pdf
     */
    public function exportPdf(Request $request)
    {
        $user = Auth::user();

        $churchId   = $request->get('church_id');
        $districtId = $request->get('district_id');
        $zoneId     = $request->get('zone_id');
        $homecellId = $request->get('homecell_id');
        $from       = $request->get('from');
        $to         = $request->get('to');
        $q          = trim((string) $request->get('q', ''));

        $query = HomecellReport::with(['church', 'district', 'zone', 'homecell', 'submittedBy'])
            ->latest();

        if (($user->role->name ?? null) === 'Zonal Leader' && isset($user->zone_id)) {
            $query->where('zone_id', $user->zone_id);
        }

        $query->when($churchId,   fn($q2) => $q2->where('church_id',   $churchId))
              ->when($districtId, fn($q2) => $q2->where('district_id', $districtId))
              ->when($zoneId,     fn($q2) => $q2->where('zone_id',     $zoneId))
              ->when($homecellId, fn($q2) => $q2->where('homecell_id', $homecellId))
              ->when($from, function ($q2) use ($from) {
                  if (Carbon::hasFormat($from, 'Y-m-d')) {
                      $q2->whereDate('created_at', '>=', $from);
                  }
              })
              ->when($to, function ($q2) use ($to) {
                  if (Carbon::hasFormat($to, 'Y-m-d')) {
                      $q2->whereDate('created_at', '<=', $to);
                  }
              })
              ->when($q, function ($q2) use ($q) {
                  $q2->where(function ($sub) use ($q) {
                      $sub->where('testimonies', 'like', "%{$q}%")
                          ->orWhereHas('submittedBy', function ($u) use ($q) {
                              $u->where('name', 'like', "%{$q}%");
                          });
                  });
              });

        $reports = $query->get();

        $filters = [
            'church'   => $churchId ? optional(Church::find($churchId))->name : null,
            'district' => $districtId ? optional(District::find($districtId))->name : null,
            'zone'     => $zoneId ? optional(Zone::find($zoneId))->name : null,
            'homecell' => $homecellId ? optional(Homecell::find($homecellId))->name : null,
            'from'     => $from,
            'to'       => $to,
            'q'        => $q,
        ];

        $pdf = Pdf::loadView('reports.homecells.pdf', compact('reports', 'filters'))
            ->setPaper('a4', 'landscape');

        $stamp = now()->format('Ymd_His');
        return $pdf->download("homecell_reports_{$stamp}.pdf");
    }

    /**
     * Show form to submit new homecell report.
     */
    public function create()
    {
        $churches = Church::all();
        $districts = District::all();
        $zones = Zone::all();
        $homecells = Homecell::all();

        return view('reports.homecells.create', compact('churches', 'districts', 'zones', 'homecells'));
    }

    /**
     * Store a submitted homecell report.
     */
    public function store(Request $request)
    {
        $request->validate([
            'church_id'     => 'required|exists:churches,id',
            'district_id'   => 'required|exists:districts,id',
            'zone_id'       => 'required|exists:zones,id',
            'homecell_id'   => 'required|exists:homecells,id',
            'males'         => 'required|integer|min:0',
            'females'       => 'required|integer|min:0',
            'first_timers'  => 'required|integer|min:0',
            'new_converts'  => 'required|integer|min:0',
            'testimonies'   => 'nullable|string|max:500',
        ]);

        HomecellReport::create([
            'church_id'     => $request->church_id,
            'district_id'   => $request->district_id,
            'zone_id'       => $request->zone_id,
            'homecell_id'   => $request->homecell_id,
            'submitted_by'  => Auth::id(),
            'males'         => $request->males,
            'females'       => $request->females,
            'children'      => $request->children,
            'first_timers'  => $request->first_timers,
            'new_converts'  => $request->new_converts,
            'testimonies'   => $request->testimonies,
        ]);

        return redirect()
            ->route('reports.homecells.index')
            ->with('success', '✅ Homecell report submitted successfully!');
    }
}
