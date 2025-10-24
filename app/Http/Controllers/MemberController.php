<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\ServiceUnit;
use App\Models\Homecell;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    /**
     * Display a listing of members.
     */
    public function index()
    {
        // Get members with their related service unit & homecell
        $members = Member::with(['serviceUnit', 'homecell'])->paginate(10);

        return view('members.index', compact('members'));
    }

    /**
     * Show the form for creating a new member.
     */
    public function create()
    {
        // Load service units and homecells for dropdowns
        $serviceUnits = ServiceUnit::all();
        $homecells = Homecell::all();

        return view('members.create', compact('serviceUnits', 'homecells'));
    }

    /**
     * Store a newly created member in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100|unique:members,email',
            'type' => 'required|in:First-timer,New Convert,Existing Member',
            'from_other_church' => 'boolean',
            'note' => 'nullable|string',
            'foundation_class_completed' => 'boolean',
            'service_unit_id' => 'nullable|exists:service_units,id',
            'homecell_id' => 'nullable|exists:homecells,id',
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
        $serviceUnits = ServiceUnit::all();
        $homecells = Homecell::all();

        return view('members.edit', compact('member', 'serviceUnits', 'homecells'));
    }

    /**
     * Update the specified member in storage.
     */
    public function update(Request $request, Member $member)
    {
        $request->validate([
            'full_name' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100|unique:members,email,' . $member->id,
            'type' => 'required|in:First-timer,New Convert,Existing Member',
            'from_other_church' => 'boolean',
            'note' => 'nullable|string',
            'foundation_class_completed' => 'boolean',
            'service_unit_id' => 'nullable|exists:service_units,id',
            'homecell_id' => 'nullable|exists:homecells,id',
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
