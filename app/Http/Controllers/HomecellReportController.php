<?php

namespace App\Http\Controllers;

use App\Models\Church;
use App\Models\District;
use App\Models\Zone;
use App\Models\Homecell;
use App\Models\HomecellReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomecellReportController extends Controller
{
    /**
     * Display all reports (Admin, Pastor, Zonal Leader, Staff)
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Fetch base query
        $query = HomecellReport::with('church', 'district', 'zone', 'homecell', 'submittedBy')
            ->latest();

        // Zonal Leader sees only their zone reports
        if ($user->role->name === 'Zonal Leader' && isset($user->zone_id)) {
            $query->where('zone_id', $user->zone_id);
        }

        // ✅ Apply filters from dropdowns
        $query->when($request->church_id, fn($q) => $q->where('church_id', $request->church_id))
              ->when($request->district_id, fn($q) => $q->where('district_id', $request->district_id))
              ->when($request->zone_id, fn($q) => $q->where('zone_id', $request->zone_id))
              ->when($request->homecell_id, fn($q) => $q->where('homecell_id', $request->homecell_id));

        // Paginate results
        $reports = $query->paginate(10);

        // Fetch lists for filters
        $churches = Church::all();

        return view('reports.homecells.index', compact('reports', 'churches'));
    }

    /**
     * Show form to submit new homecell report
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
     * Store a submitted homecell report
     */
    public function store(Request $request)
    {
        $request->validate([
            'church_id' => 'required|exists:churches,id',
            'district_id' => 'required|exists:districts,id',
            'zone_id' => 'required|exists:zones,id',
            'homecell_id' => 'required|exists:homecells,id',
            'males' => 'required|integer|min:0',
            'females' => 'required|integer|min:0',
            // 'children' => 'required|integer|min:0',
            'first_timers' => 'required|integer|min:0',
            'new_converts' => 'required|integer|min:0',
            'testimonies' => 'nullable|string|max:500',
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
