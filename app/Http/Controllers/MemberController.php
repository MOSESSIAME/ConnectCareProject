<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\ServiceUnit;
use App\Models\Homecell;
use Illuminate\Http\Request;
use Carbon\Carbon;

// ⬇️ Export libs
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\MembersExport;

class MemberController extends Controller
{
    /**
     * Build the filtered query once and reuse it
     */
    private function filteredMembersQuery(Request $request)
    {
        $q          = trim((string) $request->get('q', ''));           // search
        $type       = $request->get('type');                            // First-timer | New Convert | Existing Member
        $serviceId  = $request->get('service_unit_id');                 // FK
        $homecellId = $request->get('homecell_id');                     // FK
        $foundation = $request->get('foundation');                      // completed | pending | null
        $from       = $request->get('from');                            // Y-m-d
        $to         = $request->get('to');                              // Y-m-d
        $sort       = $request->get('sort', 'created_at');              // full_name|email|created_at
        $dir        = strtolower($request->get('dir', 'desc')) === 'asc' ? 'asc' : 'desc';

        $sortable = ['full_name', 'email', 'created_at'];
        if (!in_array($sort, $sortable, true)) {
            $sort = 'created_at';
        }

        return Member::with(['serviceUnit', 'homecell'])
            ->when($q, function ($query) use ($q) {
                $query->where(function ($x) use ($q) {
                    $x->where('full_name', 'like', "%{$q}%")
                      ->orWhere('phone', 'like', "%{$q}%")
                      ->orWhere('email', 'like', "%{$q}%")
                      ->orWhere('address', 'like', "%{$q}%"); // ⬅️ address included in search
                });
            })
            ->when($type, fn ($query) => $query->where('type', $type))
            ->when($serviceId, fn ($query) => $query->where('service_unit_id', $serviceId))
            ->when($homecellId, fn ($query) => $query->where('homecell_id', $homecellId))
            ->when($foundation === 'completed', fn ($q) => $q->where('foundation_class_completed', true))
            ->when($foundation === 'pending', fn ($q) => $q->where('foundation_class_completed', false))
            ->when($from, function ($query) use ($from) {
                if (Carbon::hasFormat($from, 'Y-m-d')) {
                    $query->whereDate('created_at', '>=', $from);
                }
            })
            ->when($to, function ($query) use ($to) {
                if (Carbon::hasFormat($to, 'Y-m-d')) {
                    $query->whereDate('created_at', '<=', $to);
                }
            })
            ->orderBy($sort, $dir);
    }

    /**
     * Display a listing of members with filtering, sorting and pagination.
     */
    public function index(Request $request)
    {
        $members = $this->filteredMembersQuery($request)
            ->paginate(15)
            ->appends($request->query());

        $serviceUnits = ServiceUnit::orderBy('name')->get(['id','name']);
        $homecells    = Homecell::orderBy('name')->get(['id','name']);
        $types        = ['First-timer','New Convert','Existing Member'];

        // Pass current filter inputs back to the view
        return view('members.index', [
            'members'      => $members,
            'serviceUnits' => $serviceUnits,
            'homecells'    => $homecells,
            'types'        => $types,

            'q'          => $request->get('q',''),
            'type'       => $request->get('type'),
            'serviceId'  => $request->get('service_unit_id'),
            'homecellId' => $request->get('homecell_id'),
            'foundation' => $request->get('foundation'),
            'from'       => $request->get('from'),
            'to'         => $request->get('to'),
            'sort'       => $request->get('sort', 'created_at'),
            'dir'        => strtolower($request->get('dir', 'desc')) === 'asc' ? 'asc' : 'desc',
        ]);
    }

    /**
     * Export Excel (respects current filters)
     */
    public function exportExcel(Request $request)
    {
        $query    = $this->filteredMembersQuery($request);
        $filename = 'members_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new MembersExport($query), $filename);
    }

    /**
     * Export PDF (respects current filters)
     */
    public function exportPdf(Request $request)
    {
        $rows = $this->filteredMembersQuery($request)->get();

        $pdf = Pdf::loadView('members.export-pdf', [
            'rows'       => $rows,
            'exportedAt' => now(),
            'filters'    => $request->all(),
        ])->setPaper('a4', 'portrait');

        return $pdf->download('members_' . now()->format('Ymd_His') . '.pdf');
    }

    /**
     * Show the form for creating a new member.
     */
    public function create()
    {
        $serviceUnits = ServiceUnit::orderBy('name')->get();
        $homecells    = Homecell::orderBy('name')->get();

        return view('members.create', compact('serviceUnits', 'homecells'));
    }

    /**
     * Store a newly created member in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'full_name'                  => 'required|string|max:100',
            'phone'                      => 'nullable|string|max:20',
            'email'                      => 'nullable|email|max:100|unique:members,email',
            'type'                       => 'required|in:First-timer,New Convert,Existing Member',
            'address'                    => 'nullable|string|max:255', // ⬅️ address
            'from_other_church'          => 'boolean',
            'note'                       => 'nullable|string',
            'foundation_class_completed' => 'boolean',
            'service_unit_id'            => 'nullable|exists:service_units,id',
            'homecell_id'                => 'nullable|exists:homecells,id',
        ]);

        Member::create($request->all());

        return redirect()->route('members.index')
            ->with('success', 'Member registered successfully.');
    }

    /**
     * Show the form for editing the specified member.
     */
    public function edit(Member $member)
    {
        $serviceUnits = ServiceUnit::orderBy('name')->get();
        $homecells    = Homecell::orderBy('name')->get();

        return view('members.edit', compact('member', 'serviceUnits', 'homecells'));
    }

    /**
     * Update the specified member in storage.
     */
    public function update(Request $request, Member $member)
    {
        $request->validate([
            'full_name'                  => 'required|string|max:100',
            'phone'                      => 'nullable|string|max:20',
            'email'                      => 'nullable|email|max:100|unique:members,email,' . $member->id,
            'type'                       => 'required|in:First-timer,New Convert,Existing Member',
            'address'                    => 'nullable|string|max:255', // ⬅️ address
            'from_other_church'          => 'boolean',
            'note'                       => 'nullable|string',
            'foundation_class_completed' => 'boolean',
            'service_unit_id'            => 'nullable|exists:service_units,id',
            'homecell_id'                => 'nullable|exists:homecells,id',
        ]);

        $member->update($request->all());

        return redirect()->route('members.index')
            ->with('success', 'Member updated successfully.');
    }

    /**
     * Remove the specified member from storage.
     */
    public function destroy(Member $member)
    {
        $member->delete();

        return redirect()->route('members.index')
            ->with('success', 'Member deleted successfully.');
    }
}
